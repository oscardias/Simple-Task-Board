<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends CI_Controller {
    
    function Task()
    {
        parent::__construct();
        
        if(!$this->usercontrol->has_permission('task'))
            redirect('dashboard');
    }
    
    public function index()
    {
        redirect('dashboard');
    }

    public function add($project)
    {
        $this->load->model('task_model');
        
        $data['page_title']  = "New Task";
        $data['title']       = '';
        $data['description'] = '';
        $data['priority']    = '2';
        $data['files']       = '';
        $data['database']    = '';
        
        $data['project']  = $project;
        $data['users'] = $this->task_model->get_related_users($project);
        $data['user'] = $this->session->userdata('user');
        
        $this->template->show('task_add', $data);
    }

    public function edit($project, $id)
    {
        $this->load->model('task_model');
        
        $data = $this->task_model->get($project, $id);
        $data['page_title']  = "Edit Task #".$id;
        
        $data['project']  = $project;
        $data['task']  = $id;
        $data['users'] = $this->task_model->get_related_users($project);
        
        $this->template->show('task_add', $data);
    }

    public function view($project, $id)
    {
        $this->load->model('task_model');
        $this->load->model('user_model');
        
        $data = $this->task_model->get($project, $id);
        $data['page_title']  = "View Task #".$id;
        
        $data['project']  = $project;
        $data['task']  = $id;
        
        $user = $this->user_model->get($data['user']);
        $data['user'] = $user['email'];
        
        $data['comments'] = $this->task_model->get_comments($project, $id);
        
        $this->template->show('task', $data);
    }
    
    public function save()
    {
        $this->load->model('task_model');
        
        $sql_data = array(
            'project' => $this->input->post('project'),
            'status' => ($this->input->post('status'))?$this->input->post('status'):0,
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'priority' => $this->input->post('priority'),
            'user'     => $this->input->post('user'),
            'files'    => ($this->input->post('files'))?$this->input->post('files'):'',
            'database' => ($this->input->post('database'))?$this->input->post('database'):''
        );
        
        $id = $this->input->post('id');
        
        if ($id)
            $this->task_model->update($this->input->post('project'), $id, $sql_data);
        else
            $id = $this->task_model->create($sql_data);

        redirect('task/view/'.$this->input->post('project').'/'.$id);
    }
    
    public function move($project, $id, $status)
    {
        $this->load->model('task_model');
        
        $sql_data = array(
            'status' => $status
        );

        $this->task_model->update($project, $id, $sql_data);

        redirect('project/tasks/'.$project);
    }
    
    public function remove($project, $id)
    {
        $this->load->model('task_model');
        $this->task_model->delete($project, $id);

        redirect('project/tasks/'.$project);
    }
    
    public function comment()
    {
        // TODO: Check if user is related to project
        
        $data = array(
            'project' => $this->input->post('project'),
            'task' => $this->input->post('task'),
            'user' => $this->session->userdata('user'),
            'comment' => $this->input->post('comment')
        );
        
        $this->load->model('task_model');
        $this->task_model->create_comment($data);

        redirect('task/view/'.$data['project'].'/'.$data['task']);
    }

}
