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
        
    }
    
    public function tasks($project_id)
    {
//        TODO: review transport functionality
//        // Load open transports
//        $this->load->model('transport_model');
//        $transports = $this->transport_model->get(false, true);
//        if ($transports)
//            $data['transports'] = $transports;
        
        // Load tasks
        $this->load->model('task_model');
        $tasks = $this->task_model->get_project_tasks($project_id);
        
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
        $data['page_title']  = "Project: ".$project['name'];
        
        // Load text helper to be used in the view
        $this->load->helper('text');
        
        // Load View
        $this->template->show('task_board', $data);
    }

    public function add()
    {
        $data['page_title']  = "New Task";
        $data['title']       = '';
        $data['description'] = '';
        $data['priority']    = '2';
        $data['files']       = '';
        $data['database']    = '';
        
        $this->template->show('task_add', $data);
    }

    public function edit($id)
    {
        $this->load->model('task_model');
        
        $tasks = $this->task_model->get($id);
        
        foreach ($tasks as $task) {
            $data = $task;
            $data['page_title']  = "Edit Task #".$task['id'];
        }
        
        $this->template->show('task_add', $data);
    }
    
    public function save()
    {
        $this->load->model('task_model');
        
        $sql_data = array(
            'status'   => ($this->input->post('status'))?$this->input->post('status'):0,
            'title'    => $this->input->post('title'),
            'text'     => $this->input->post('description'),
            'priority' => $this->input->post('priority'),
			'files'    => ($this->input->post('files'))?$this->input->post('files'):'',
			'database' => ($this->input->post('database'))?$this->input->post('database'):''
        );
        
        if ($this->input->post('id'))
            $this->task_model->update($this->input->post('id'),$sql_data);
        else
            $this->task_model->create($sql_data);

        redirect('task');
    }
}
