<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    
    function Dashboard()
    {
        parent::__construct();
        
        if(!$this->usercontrol->has_permission('dashboard'))
            redirect('login');        
    }
    
    public function index()
    {
        $this->title = 'Dashboard';
        $this->menu = 'users|new_project';
        
        //Load models
        $this->load->model('project_model');
        $this->load->model('task_model');
        
        //Load projects
        $projects = $this->project_model->get_user_related($this->session->userdata('user'));
        
        foreach ($projects as $key => $project) {
            $projects[$key]['tasks'] = $this->task_model->get_project_user_tasks($project['id'], $this->session->userdata('user'),5);
        }
        
        $data['projects'] = $projects;
        
        // Load tasks
        $data['tasks'] = $this->task_model->get_user_tasks($this->session->userdata('user'));
        
        $data['page_title']  = "Dashboard";
        $data['status_arr'] = $this->task_model->get_status_array();
        
        // Load View
        $this->load->view('dashboard', $data);
    }

}
