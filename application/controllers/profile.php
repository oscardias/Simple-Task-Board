<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Controller {
    
    private $error = false;
    
    function Profile()
    {
        parent::__construct();
        
        if(!$this->usercontrol->has_permission('profile'))
            redirect('dashboard');
    }
    
    public function index()
    {
        // Load profile info
        $this->load->model('user_model');
        $data = $this->user_model->get($this->session->userdata('user'));
        $data['password'] = '';
        $data['links'] = json_decode($data['links']);
        if($data['photo'])
            $data['photo'] = 'upload/profile/'.$this->session->userdata('user').'/large'.$data['photo'];
        
        $this->title = "My Profile";
        $this->menu = 'dashboard';
        
        if($this->error)
            $data['error'] = $this->error;
        
        // Load View
        $this->load->view('profile_edit', $data);
    }
    
    public function save()
    {
        if($this->input->post('cancel') !== FALSE)
            redirect('dashboard');
            
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Name', 'trim');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim');
        $this->form_validation->set_rules('links[]', 'Links', 'trim|prep_url');
        $this->form_validation->set_rules('photo', 'Photo', '');
        
        if($this->form_validation->run() === false)  {
            $this->error = true;
            
            $this->index ();
            
            return;
        }
        
        $this->load->model('user_model');
        
        $links = json_encode(array_filter($this->input->post('links')));
        
        $sql_data = array(
            'name'     => $this->input->post('name'),
            'email'    => $this->input->post('email'),
            'links'    => $links
        );
        
        if($this->input->post('reset_password')){
            $sql_data['password'] = $this->input->post('password');
        }
        
        // Photo upload
        if($_FILES['photo']['name'] != "") {
            if($this->user_model->upload_photo())
                $sql_data['photo'] = $this->user_model->get_photo();
            else {
                $this->error = $this->user_model->error_message();
                
                $this->index ();

                return;
            }
        }

        $this->user_model->update($this->session->userdata('user'), $sql_data);
        
        redirect('profile');
    }
    
    public function view($user_id)
    {
        // Load profile info
        $this->load->model('user_model');
        $data['user'] = $this->user_model->get($user_id);
        $data['user']['password'] = '';
        $data['user']['links'] = json_decode($data['user']['links']);
        if($data['user']['photo'])
            $data['user']['photo'] = 'upload/profile/'.$user_id.'/large'.$data['user']['photo'];
        
        if(!IS_AJAX) {
            $user = ($data['user']['name']?$data['user']['name']:$data['user']['email']);
            
            $this->title = "Profile: {$user}";
            $this->menu = 'dashboard|users';

            // Load View
            $this->load->view('profile_details', $data);
        } else {
            $this->layout = 'ajax';
                
            // Load View
            $this->load->view('profile_details_modal', $data);
        }
    }
}
