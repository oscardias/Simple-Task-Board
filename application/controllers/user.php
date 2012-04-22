<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
    
    private $LEVEL;
    
    function User()
    {
        parent::__construct();
        
        if(!$this->session->userdata('logged'))
            redirect('login');
        
        $this->LEVEL = array(
            1 => 'Full Access',
            2 => 'Project Manager',
            3 => 'Developer'
        );
    }
    
    public function index()
    {
        // Load open transports
        $this->load->model('user_model');
        $data['users'] = $this->user_model->get(false);
        $data['level_list'] = $this->LEVEL;
        
        $data['page_title']  = "Users";
        
        // Load View
        $this->template->show('users', $data);
    }

    public function add()
    {
        $data['page_title']  = "New User";
        $data['email']    = '';
        $data['password'] = '';
        $data['level']    = '1';
        $data['level_list'] = $this->LEVEL;
        
        $this->template->show('users_add', $data);
    }

    public function edit($id)
    {
        $this->load->model('user_model');
        $users = $this->user_model->get($id);
        
        foreach ($users as $user) {
            $data = $user;
            $data['password'] = 'password';
            $data['page_title']  = "Edit User #".$user['id'];
        }
        
        $data['level_list'] = $this->LEVEL;
        
        $this->template->show('users_add', $data);
    }
    
    public function remove($id)
    {
        $this->load->model('user_model');
        $this->user_model->delete($id);
        
        redirect('user');
    }
    
    public function save()
    {
        $this->load->model('user_model');
        
        $sql_data = array(
            'email'    => $this->input->post('email'),
            'level'    => $this->input->post('level')
        );
        
        if($this->input->post('reset_password')){
            $sql_data['password'] = $this->input->post('password');
        }
        
        if ($this->input->post('id'))
            $this->user_model->update($this->input->post('id'),$sql_data);
        else
            $this->user_model->create($sql_data);

        redirect('user');
    }
    
}
