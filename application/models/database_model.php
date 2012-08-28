<?php

class Database_model extends CI_Model {

    private $latest_database_version = '1.1';
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
            
            // Create installation date
            $this->update_setting('stb_install_date', date("Y-m-d H:i:s"));
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
