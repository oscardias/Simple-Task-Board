<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Install extends CI_Controller {

    public function index()
    {
        $data = array();
        
        // Check if users are already there
        $this->load->model('user_model');
        $users = $this->user_model->get_count();
        if($users == 0){
            $data['already_installed'] = false;
        } else {
            $data['already_installed'] = true;
        }
        
        // Check if database version is correct
        $this->load->model('database_model');
        if($this->database_model->is_up_to_date()) {
            $data['update_database'] = false;
        } else {
            $data['update_database'] = true;
        }
        
        // Load View
        $data['page_title']  = "Installation";
        
        $data['email'] = '';
        $data['password'] = '';
        
        $this->template->show('login', $data);
    }
    
    public function run()
    {
        // Check if users are already there
        $this->load->model('user_model');
        $users = $this->user_model->get();
        if(!$users){
            $insert = array(
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
                'level' => $this->user_model->USER_LEVEL_ADMIN
            );
            $this->user_model->create($insert);
        }
        
        // Load View
        $data['page_title']  = "Login";
        
        $data['email'] = '';
        $data['password'] = '';
        
        $data['answer'] = 'User created!';
        
        $this->template->show('login', $data);
    }
    
    public function database()
    {
        // Update the database
        $this->load->model('database_model');
        if(!$this->database_model->is_up_to_date()) {
            $this->database_model->upgrade();
        }
        
        // Load View
        $data['page_title']  = "Login";
        
        $data['email'] = '';
        $data['password'] = '';
        
        $data['answer'] = 'Database upgraded!';
        
        $this->template->show('login', $data);
    }
}