<?php

class Database_model extends CI_Model {

    private $latest_database_version = '1.2';
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
