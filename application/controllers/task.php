<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends CI_Controller {
    
    function Task()
    {
        parent::__construct();
        
        if(!$this->session->userdata('logged'))
            redirect('login');
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
        $data['users'] = $this->task_model->get_related_users($project);
        
        $this->template->show('task_add', $data);
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
        
        if ($this->input->post('id'))
            $this->task_model->update($this->input->post('project'), $this->input->post('id'),$sql_data);
        else
            $this->task_model->create($sql_data);

        redirect('project/tasks/'.$this->input->post('project'));
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
}
