<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {
    
    function Project()
    {
        parent::__construct();
        
        if(!$this->session->userdata('logged'))
            redirect('login');
    }
    
    public function index()
    {
        redirect('dashboard');
    }
    
    public function tasks($project_id)
    {        
        // Load tasks
        $this->load->model('task_model');
        $tasks = $this->task_model->get($project_id);
        
        foreach ($tasks as $task) {
            if ($task['status'] == 0) {
                $data['stories'][] = $task;
            } elseif ($task['status'] == 1) {
                $data['tasks'][] = $task;
            } elseif ($task['status'] == 2) {
                $data['tests'][] = $task;
            } elseif ($task['status'] == 3) {
                $data['done'][] = $task;
            }
        }

        // Load project info
        $this->load->model('project_model');
        $project = $this->project_model->get($project_id);
        
        $data['page_title'] = "Project: ".$project['name'];
        $data['project']    = $project_id;
        
        $data['current_user'] = $this->session->userdata('user');
        
        $db_users = $this->project_model->get_related_users($project_id);
        $users = array();
        foreach ($db_users as $user) {
            $users[$user['id']] = $user;
        }
        $data['users'] = $users;
        
        // Load text helper to be used in the view
        $this->load->helper('text');
        
        // Load View
        $this->template->show('task_board', $data);
    }

    public function add()
    {
        $this->load->model('project_model');
        
        $data['page_title']  = "New Project";
        $data['user']        = '';
        $data['name']        = '';
        $data['description'] = '';
        
        $users = $this->project_model->get_related_users();
        foreach ($users as $key => $value) {
            if($value['id'] == $this->session->userdata('user'))
                $users[$key]['project'] = 1;
            else
                $users[$key]['project'] = 0;
        }
        $data['users'] = $users;
        
        $this->template->show('project_add', $data);
    }

    public function edit($id)
    {
        $this->load->model('project_model');
        
        $data = $this->project_model->get($id);
        
        $data['page_title']  = "Edit Project #".$id;
        $data['project']  = $id;
        $data['users'] = $this->project_model->get_related_users($id);
        
        $this->template->show('project_add', $data);
    }
    
    public function save()
    {
        $this->load->model('project_model');
        
        $sql_data = array(
            'user'        => $this->session->userdata('user'),
            'name'        => $this->input->post('name'),
            'description' => $this->input->post('description')
        );
        
        $project_id = $this->input->post('id');
        
        if ($project_id)
            $this->project_model->update($project_id,$sql_data);
        else
            $project_id = $this->project_model->create($sql_data);
            
        // Related users
        $this->project_model->delete_related($project_id);
        
        $users = $this->input->post('users');
        foreach ($users as $user) {
            $sql_data = array(
                'user' => $user,
                'project' => $project_id
            );
            $this->project_model->create_related($sql_data);
        }

        if ($project_id)
            redirect('project/tasks/'.$project_id);
        else
            redirect('project');
    }
}
