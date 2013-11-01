<?php

class Database_model extends CI_Model {

    private $latest_database_version = '2.1';
    private $current_database_version = '1.0';
    
    public function is_up_to_date()
    {
        // Check if settings table has been defined - if not it's version 1.0
        if(!$this->table_exists('settings'))
            return false;
        
        // Get current database version from the settings table
        $query = $this->db->where('setting_name', 'database_version')->get('settings')->row_array();
        if($query['setting_value'] == $this->latest_database_version) {
            $this->current_database_version = $this->latest_database_version;
            return true; // Database is up to date
        }
        
        // System database is in a previous version
        $this->current_database_version = $query['setting_value'];
        return false;
    }
    
    public function upgrade()
    {
        // Use CI dbforge class
        $this->load->dbforge();
        
        // Execute 1.0 -> 1.1 database updates
        if($this->current_database_version == '1.0') {
            
            $this->db->trans_start();
            
            //DROP TABLE transport
            if($this->table_exists('transport'))
                $this->dbforge->drop_table('transport');
            
            //ALTER TABLE  `task` ADD  `parent` INT UNSIGNED NOT NULL AFTER  `id`;
            if(!$this->field_exists('task', 'parent'))
                $this->db->query('ALTER TABLE  `task` ADD  `parent` INT UNSIGNED NOT NULL AFTER  `id`');
            
            //ALTER TABLE  `task_comments` ADD  `project` INT UNSIGNED NOT NULL AFTER  `id`;
            if(!$this->field_exists('task_comments', 'project'))
                $this->db->query('ALTER TABLE  `task_comments` ADD  `project` INT UNSIGNED NOT NULL AFTER  `id`');
            
            //CREATE TABLE settings
            if(!$this->table_exists('settings')) {
                $fields = array(
                    'setting_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'auto_increment' => TRUE
                    ),
                    'setting_name' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '50'
                     ),
                    'setting_value' => array(
                        'type' =>'VARCHAR',
                        'constraint' => '150'
                    )
                );
                $this->dbforge->add_field($fields);
                $this->dbforge->add_key('setting_id', TRUE);
                $this->dbforge->create_table('settings', TRUE);

                // CREATE UNIQUE INDEX `setting_name` ON `settings`(`setting_name`)
                $this->db->query('CREATE UNIQUE INDEX `setting_name` ON `settings`(`setting_name`)');
            }
            
            // Create database version indicator
            $this->update_setting('database_version', '1.1');
            $this->current_database_version = '1.1';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        // Execute 1.1 -> 1.2 database updates
        if($this->current_database_version == '1.1') {
            
            $this->db->trans_start();
            
            // Create new task table
            if($this->table_exists('task_tmp'))
                $this->dbforge->drop_table('task_tmp');
            
            if(!$this->field_exists('task', 'task_id')) {
                $fields = array(
                    'task_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'auto_increment' => TRUE
                    ),
                    'project_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                     ),
                    'parent_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => TRUE
                    ),
                    'user_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                     ),
                    'code' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                     ),
                    'status' => array(
                        'type' => 'TINYINT',
                        'constraint' => 4, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                     ),
                    'title' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '50',
                        'null' => FALSE
                     ),
                    'priority' => array(
                        'type' => 'TINYINT',
                        'constraint' => 4, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                     ),
                    'description' => array(
                        'type' => 'TEXT',
                        'null' => FALSE
                     ),
                    'files' => array(
                        'type' => 'TEXT',
                        'null' => FALSE
                     ),
                    'database' => array(
                        'type' => 'TEXT',
                        'null' => FALSE
                     ),
                    'date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
                );
                $this->dbforge->add_field($fields);
                $this->dbforge->add_key('task_id', TRUE);
                $this->dbforge->create_table('task_tmp', TRUE);

                // Indexes
                $this->db->query('ALTER TABLE  `task_tmp` ADD INDEX  `status` (  `project_id` ,  `status` )');
                $this->db->query('ALTER TABLE  `task_tmp` ADD INDEX  `parent` (  `parent_id` )');
                
                // Copy all items
                $task_array = $this->db->get('task')->result_array();
                
                foreach ($task_array as $value) {
                    $this->db->insert('task_tmp', array(
                        'project_id' => $value['project'],
                        'parent_id' => ($value['parent'])?$value['parent']:NULL,
                        'user_id' => $value['user'],
                        'code' => $value['id'],
                        'status' => $value['status'],
                        'title' => $value['title'],
                        'priority' => $value['priority'],
                        'description' => $value['description'],
                        'files' => $value['files'],
                        'database' => $value['database'],
                        'date_created' => $value['date_created']
                    ));
                }
                
                // Drop original task table
                $this->dbforge->drop_table('task');
                
                // Rename new table
                $this->dbforge->rename_table('task_tmp', 'task');
                
                // Foreign Keys
                $this->db->query('ALTER TABLE  `task` ADD CONSTRAINT `task_stbfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION');
                $this->db->query('ALTER TABLE  `task` ADD CONSTRAINT `task_stbfk_2` FOREIGN KEY (`parent_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
            
            // Create new task table
            if($this->table_exists('task_comments_tmp'))
                $this->dbforge->drop_table('task_comments_tmp');
                
            if(!$this->field_exists('task_comments', 'task_comments_id')) {
                $fields = array(
                    'task_comments_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'auto_increment' => TRUE
                    ),
                    'task_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                    ),
                    'user_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                     ),
                    'comment' => array(
                        'type' => 'TEXT',
                        'null' => FALSE
                     ),
                    'date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
                );
                $this->dbforge->add_field($fields);
                $this->dbforge->add_key('task_comments_id', TRUE);
                $this->dbforge->create_table('task_comments_tmp', TRUE);

                // Indexes
                $this->db->query('ALTER TABLE  `task_comments_tmp` ADD INDEX  `task` (  `task_id` )');
                
                // Copy all items
                $task_array = $this->db->get('task_comments')->result_array();
                
                foreach ($task_array as $value) {
                    $this->db->insert('task_comments_tmp', array(
                        'task_id' => $value['project'],
                        'user_id' => $value['user'],
                        'comment' => $value['comment'],
                        'date_created' => $value['date_created']
                    ));
                }
                
                // Drop original task table
                $this->dbforge->drop_table('task_comments');
                
                // Rename new table
                $this->dbforge->rename_table('task_comments_tmp', 'task_comments');
                
                // Foreign Keys
                $this->db->query('ALTER TABLE  `task_comments` ADD CONSTRAINT `task_comments_stbfk_1` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
            
            //CREATE TABLE task_history
            if(!$this->table_exists('task_history')) {
                $fields = array(
                    'task_history_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'auto_increment' => TRUE
                    ),
                    'task_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                    ),
                    'status' => array(
                        'type' => 'TINYINT',
                        'constraint' => 4, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                     ),
                    'date_created' => array(
                        'type' => 'DATETIME',
                        'null' => FALSE
                     ),
                    'date_finished' => array(
                        'type' => 'DATETIME',
                        'null' => TRUE
                     ),
                    'duration' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => TRUE
                     )
                );
                $this->dbforge->add_field($fields);
                $this->dbforge->add_key('task_history_id', TRUE);
                $this->dbforge->create_table('task_history', TRUE);

                $this->db->query('CREATE INDEX `task` ON `task_history`(`task_id`, `status`)');
                
                // Foreign Keys
                $this->db->query('ALTER TABLE  `task_history` ADD CONSTRAINT `task_history_stbfk_1` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            }
            
            // Create database version indicator
            $this->update_setting('database_version', '1.2');
            $this->current_database_version = '1.2';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        // Execute 1.2 -> 1.3 database updates
        if($this->current_database_version == '1.2') {
            
            $this->db->trans_start();
            
            //ALTER TABLE  `task_history` ADD  `user_id` INT UNSIGNED NULL AFTER  `task_id`;
            if(!$this->field_exists('task_history', 'user_id'))
                $this->db->query('ALTER TABLE  `task_history` ADD  `user_id` INT UNSIGNED NULL AFTER  `task_id`');

            // Indexes
            $this->db->query('ALTER TABLE  `task_history` ADD INDEX  `user` (  `user_id` )');
                
            // Foreign Keys
            $this->db->query('ALTER TABLE  `task_history` ADD CONSTRAINT `task_history_stbfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            
            // Create database version indicator
            $this->update_setting('database_version', '1.3');
            $this->current_database_version = '1.3';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }

        // Execute 1.3 -> 1.4 database updates
        if($this->current_database_version == '1.3') {
            
            $this->db->trans_start();
            
            //ALTER TABLE  `user` ADD  `name` VARCHAR(100) NULL AFTER  `level`;
            if(!$this->field_exists('user', 'name'))
                $this->db->query('ALTER TABLE  `user` ADD  `name` VARCHAR(100) NULL AFTER  `level`');
            
            //ALTER TABLE  `user` ADD  `links` TEXT NULL AFTER  `name`;
            if(!$this->field_exists('user', 'links'))
                $this->db->query('ALTER TABLE  `user` ADD  `links` TEXT NULL AFTER  `name`');

            //ALTER TABLE  `user` ADD  `photo` VARCHAR(255) NULL AFTER  `links`;
            if(!$this->field_exists('user', 'photo'))
                $this->db->query('ALTER TABLE  `user` ADD  `photo` VARCHAR(255) NULL AFTER  `links`');
            
            // Create database version indicator
            $this->update_setting('database_version', '1.4');
            $this->current_database_version = '1.4';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        // Execute 1.4 -> 1.5 database updates
        if($this->current_database_version == '1.4') {
            
            $this->db->trans_start();
            
            //ALTER TABLE  `task` ADD  `duration` INT UNSIGNED NULL AFTER  `priority`;
            if(!$this->field_exists('task', 'duration'))
                $this->db->query('ALTER TABLE  `task` ADD  `duration` INT UNSIGNED NULL AFTER  `priority`');
            
            //ALTER TABLE  `task` ADD  `start_date` DATE NULL AFTER  `duration`;
            if(!$this->field_exists('task', 'start_date'))
                $this->db->query('ALTER TABLE  `task` ADD  `start_date` DATE NULL AFTER  `duration`');

            //CREATE TABLE task_predecessor
            if(!$this->table_exists('task_predecessor')) {
                $fields = array(
                    'task_predecessor_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'auto_increment' => TRUE
                    ),
                    'task_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                    ),
                    'predecessor_id' => array(
                        'type' => 'INT',
                        'constraint' => 10, 
                        'unsigned' => TRUE,
                        'null' => FALSE
                     )
                );
                $this->dbforge->add_field($fields);
                $this->dbforge->add_field("type ENUM('fs','ss') NOT NULL DEFAULT 'fs'");
                $this->dbforge->add_key('task_predecessor_id', TRUE);
                $this->dbforge->create_table('task_predecessor', TRUE);

                $this->db->query('CREATE INDEX `task` ON `task_predecessor`(`task_id`)');
                
                // Foreign Keys
                $this->db->query('ALTER TABLE  `task_predecessor` ADD CONSTRAINT `task_predecessor_stbfk_1` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE');
                $this->db->query('ALTER TABLE  `task_predecessor` ADD CONSTRAINT `task_predecessor_stbfk_2` FOREIGN KEY (`predecessor_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE');
            }
            
            // Drop old columns
            if($this->field_exists('task', 'files'))
                $this->dbforge->drop_column('task', 'files');
            
            if($this->field_exists('task', 'database'))
                $this->dbforge->drop_column('task', 'database');
            
            // Create database version indicator
            $this->update_setting('database_version', '1.5');
            $this->current_database_version = '1.5';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        // Execute 1.5 -> 1.6 database updates
        if($this->current_database_version == '1.5') {
            
            $this->db->trans_start();
            
            //Drop previus columns
            if($this->field_exists('task', 'duration'))
                $this->dbforge->drop_column('task', 'duration');
            
            if($this->field_exists('task', 'start_date'))
                $this->dbforge->drop_column('task', 'start_date');

            //DROP TABLE task_predecessor
            if($this->table_exists('task_predecessor'))
                $this->dbforge->drop_table('task_predecessor');
            
            //ALTER TABLE  `task` ADD  `due_date` DATE NULL AFTER  `priority`;
            if(!$this->field_exists('task', 'due_date'))
                $this->db->query('ALTER TABLE  `task` ADD  `due_date` DATE NULL AFTER  `priority`');
                        
            // Create database version indicator
            $this->update_setting('database_version', '1.6');
            $this->current_database_version = '1.6';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        // Execute 1.6 -> 1.7 database updates
        if($this->current_database_version == '1.6') {
            
            $this->db->trans_start();
            
            //ALTER TABLE  `user` ADD  `github_username` VARCHAR(100) NULL AFTER  `photo`;
            if(!$this->field_exists('user', 'github_username'))
                $this->db->query('ALTER TABLE  `user` ADD  `github_username` VARCHAR(100) NULL AFTER  `photo`');
                        
            //ALTER TABLE  `user` ADD  `github_token` VARCHAR(40) NULL AFTER  `github_username`;
            if(!$this->field_exists('user', 'github_token'))
                $this->db->query('ALTER TABLE  `user` ADD  `github_token` VARCHAR(40) NULL AFTER  `github_username`');
            
            // Create database version indicator
            $this->update_setting('database_version', '1.7');
            $this->current_database_version = '1.7';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        // Execute 1.7 -> 1.8 database updates
        if($this->current_database_version == '1.7') {
            
            $this->db->trans_start();
            
            //ALTER TABLE  `project` ADD  `github_repo` VARCHAR(255) NULL AFTER  `description`;
            if(!$this->field_exists('project', 'github_repo'))
                $this->db->query('ALTER TABLE  `project` ADD  `github_repo` VARCHAR(255) NULL AFTER  `description`');
            
            // Create database version indicator
            $this->update_setting('database_version', '1.8');
            $this->current_database_version = '1.8';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        // Execute 1.8 -> 1.9 database updates
        if($this->current_database_version == '1.8') {
            
            $this->db->trans_start();
            
            //ALTER TABLE  `task` ADD  `date_updated` DATETIME NULL AFTER  `date_created`;
            if(!$this->field_exists('task', 'date_updated'))
                $this->db->query('ALTER TABLE  `task` ADD  `date_updated` DATETIME NULL AFTER  `date_created`');
            
            // Create database version indicator
            $this->update_setting('database_version', '1.9');
            $this->current_database_version = '1.9';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        // Execute 1.9 -> 2.0 database updates
        if($this->current_database_version == '1.9') {
            // Github integration - create tasks for deleted tasks
            // No gaps betwen task numbers
            $this->db->trans_start();
            
            // Get any user with github login
            $user = $this->db->where('github_username IS NOT NULL', null, false)->
                    limit(1)->get('user')->row_array();
            
            // Check if an user was found
            if($user) {
                // Get projects
                $projects = $this->db->get('project')->result_array();
                foreach ($projects as $project) {
                    $tasks = $this->db->where('project_id', $project['id'])->
                            get('task')->result_array();
                    $ord_tasks = array();
                    $max_code = 0;
                    foreach ($tasks as $task) {
                        $ord_tasks[$task['code']] = $task;
                        if($max_code < $task['code'])
                            $max_code = $task['code'];
                    }

                    // Check all codes
                    for($i = 1; $i <= $max_code; $i++){
                        if(!isset($ord_tasks[$i])) {
                            $insert = array(
                                'project_id' => $project['id'],
                                'user_id' => $user['id'],
                                'code' => $i,
                                'title' => 'Deleted Task',
                                'description' => 'Task Deleted From Simple Task Board',
                                'priority' => 4,
                                'status' => 3
                            );

                            $this->db->insert('task', $insert);
                        }
                    }
                }
            }
            
            // Create database version indicator
            $this->update_setting('database_version', '2.0');
            $this->current_database_version = '2.0';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
            
        // Execute 2.0 -> 2.1 database updates
        if($this->current_database_version == '2.0') {
            // Github integration update
            // Indicate github sync for task
            //ALTER TABLE `task` ADD COLUMN `github_sync` BIT NOT NULL DEFAULT 0;
            if(!$this->field_exists('task', 'github_sync'))
                $this->db->query('ALTER TABLE `task` ADD COLUMN `github_sync` BIT NOT NULL DEFAULT 0;');
            // Indicate github issue number
            //ALTER TABLE  `task` ADD  `github_code` INT NULL AFTER  `code`;
            if(!$this->field_exists('task', 'github_code'))
                $this->db->query('ALTER TABLE  `task` ADD  `github_code` INT NULL;');
            
            // Update data
            $this->db->trans_start();
            
            // Get projects with Github integration
            $projects = $this->db->where('github_repo IS NOT NULL', FALSE, NULL)->
                    where('github_repo <>', '')->
                    get('project')->result_array();
            
            // Update project's tasks with github_code (same as code currently)
            foreach ($projects as $project) {
                $this->db->where('project_id', $project['id'])->
                        set('github_code', 'code', FALSE)->
                        set('github_sync', 1)->
                        update('task');
            }
            
            // Create database version indicator
            $this->update_setting('database_version', '2.1');
            $this->current_database_version = '2.1';
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
            
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE)
                return false;
            
        }
        
        return true;
    }

    
    /*
     * Generic methods
     */
    public function table_exists($table_name)
    {
        // Check if table exits
        $query = $this->db->query('show tables like \'' . $table_name . '\'');
        if ($query->num_rows == 0) {
            return false;
        }
        return true;
    }
    
    public function field_exists($table_name, $field_name)
    {
        // Check if field exits
        $query = $this->db->query('show columns from  ' . $table_name . ' where field like \'' . $field_name . '\'');
        if ($query->num_rows == 0) {
            return false;
        }
        return true;
    }
    
    public function index_exists($table_name, $index_name, $validate = array())
    {
        // Check if index exits
        $query = $this->db->query('show index from ' . $table_name . ' where key_name like \'' . $index_name . '\'');
        if ($query->num_rows == 0) {
            return false;
        }
        
        if($validate){
            $array = $query->result_array();
            foreach ($array as $key => $value) {
                if($value['Column_name'] !== $validate[$key])
                    return false;
            }
        }
        
        return true;
    }
    
    public function foreign_exists($key_name)
    {
        // Check if foreign key exits
        $query = $this->db->query('select constraint_name from information_schema.key_column_usage where constraint_name = \''.$key_name.'\'');
        if ($query->num_rows == 0) {
            return false;
        }
        
        return true;
    }
    
    public function update_setting($setting_name, $setting_value)
    {
        $data = array(
            'setting_name' => $setting_name,
            'setting_value' => $setting_value
        );
        
        // Check if setting exits
        $query = $this->db->where('setting_name', $setting_name)->get('settings');
        
        if ($query->num_rows == 0) {
            $this->db->insert('settings', $data);
        }
        
        $this->db->where('setting_name', $setting_name)->update('settings', $data);
    }
    
}
