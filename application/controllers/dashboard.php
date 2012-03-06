<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    
    function Dashboard()
    {
        parent::__construct();
        
        if(!$this->session->userdata('logged'))
            redirect('login');
    }
    
    public function index()
    {
        //Load projects
        $this->load->model('project_model');
        $data['projects'] = $this->project_model->get_user_related($this->session->userdata('user'));
        
        // Load tasks
        $this->load->model('task_model');
        $tasks = $this->task_model->get_user_tasks($this->session->userdata('user'));
        
        // Load View
        $this->template->show('dashboard', $data);
    }

}
