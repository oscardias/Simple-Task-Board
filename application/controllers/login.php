<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    public function index()
    {
        // Check if users exist
        $this->load->model('user_model');
        $users = $this->user_model->get_count();
        if($users == 0)
            redirect('install');
        
        // Check if database version is correct
        $this->load->model('database_model');
        if(!$this->database_model->is_up_to_date())
            redirect('install');
        
        // Load View
        $this->title  = 'Login';
        $this->layout = 'login';
        $this->menu   = 'none';
        
        $data['email'] = '';
        $data['password'] = '';
                
        $this->load->view('login', $data);
    }
    
    public function validate()
    {
        $this->load->model('user_model');
        $result = $this->user_model->validate($this->input->post('email'),$this->input->post('password'));
        
        if($result) {
            $this->session->set_userdata(array(
                'logged' => true,
                'user'  => $result['id'],
                'level' => $result['level']
            ));
            
            redirect('dashboard');
        } else {
            // Load View
            $this->title  = 'Login';
            $this->layout = 'login';
            $this->menu   = 'none';

            $data['email'] = $this->input->post('email');
            $data['password'] = $this->input->post('password');
            
            $data['error'] = true;
            
            $this->load->view('login', $data);
        }
    }
    
    public function logout()
    {
        $this->session->unset_userdata('logged');

        redirect('login');
    }
}
