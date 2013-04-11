<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {
    
    private $error = false;
    
    public function index()
    {
        redirect('dashboard');
    }
    
    public function tasks($project_id)
    {
        $this->load->helper('stb_date');
        $this->load->helper('tasks');
        
        // Check permission
        if(!$this->usercontrol->has_permission('project', 'tasks'))
            redirect('dashboard');
        
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
        
        $this->title = "Project: {$project['name']}";
        $this->menu = 'dashboard|edit_project|new_task';
        
        $data['project_id']    = $project_id;
        
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
        $this->load->view('task_board', $data);
    }

    public function add()
    {
        // Check permission
        if(!$this->usercontrol->has_permission('project'))
            redirect('dashboard');
        
        $this->load->model('project_model');
        
        $this->title = "New Project";
        $this->menu = 'dashboard';
        
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
        
        if($this->error)
            $data['error'] = $this->error;
        
        $this->load->view('project_add', $data);
    }

    public function edit($id)
    {
        // Check permission
        if(!$this->usercontrol->has_permission('project'))
            redirect('dashboard');
        
        $this->load->model('project_model');
        
        $data = $this->project_model->get($id);
        
        $data['page_title']  = "Edit Project #".$id;
        $data['project_id']  = $id;
        $data['users'] = $this->project_model->get_related_users($id);
        
        if($this->error)
            $data['error'] = $this->error;
        
        $this->template->show('project_add', $data);
    }
    
    public function save()
    {
        if($this->input->post('cancel') !== FALSE)
            redirect('dashboard');
            
        // Check permission
        if(!$this->usercontrol->has_permission('project'))
            redirect('dashboard');

        // Get project ID - false if new entry
        $project_id = $this->input->post('id');
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('users[]', 'Associated Users', '');
        
        if($this->form_validation->run() === false)  {
            $this->error = true;
            
            if ($project_id)
                $this->edit ($project_id);
            else
                $this->add ();
            
            return;
        }
        
        $this->load->model('project_model');

        $sql_data = array(
            'user'        => $this->session->userdata('user'),
            'name'        => $this->input->post('name'),
            'description' => $this->input->post('description')
        );

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
