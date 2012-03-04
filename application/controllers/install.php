<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Install extends CI_Controller {

    public function index()
    {
        $data = array();
        
        // Check if users are already there
        $this->load->model('user_model');
        $users = $this->user_model->get();
        if($users){
            $data['already_installed'] = true;
        } else {
            $data['already_installed'] = false;
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
        
        $this->template->show('login', $data);
    }
    
}