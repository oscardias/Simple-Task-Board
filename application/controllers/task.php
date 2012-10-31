<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task extends CI_Controller {
    
    private $error = false;
    
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
        $data['parent_id']      = 0;
        $data['title']       = '';
        $data['description'] = '';
        $data['priority']    = '2';
        $data['files']       = '';
        $data['database']    = '';
        
        $data['project_id']  = $project;
        $data['users'] = $this->task_model->get_related_users($project);
        $data['user_id'] = $this->session->userdata('user');
        $data['tasks'] = $this->task_model->get_hierarchy($project);
        
        if($this->error)
            $data['error'] = $this->error;
        
        $this->template->show('task_add', $data);
    }

    public function edit($project, $id)
    {
        $this->load->model('task_model');
        
        $data = $this->task_model->get($project, $id);
        $data['page_title']  = "Edit Task #".$data['code'];
        
        $data['project_id']  = $project;
        $data['users'] = $this->task_model->get_related_users($project);
        $data['tasks'] = $this->task_model->get_hierarchy($project);
        
        if($this->error)
            $data['error'] = $this->error;
        
        $this->template->show('task_add', $data);
    }

    public function view($project, $id)
    {
        $this->load->helper('tasks');
        $this->load->helper('stb_date');
        
        $this->load->model('task_model');
        $this->load->model('user_model');
        
        $data = $this->task_model->get($project, $id);
        $data['page_title']  = "View Task #".$data['code'];
        
        $data['project_id']  = $project;
        
        if($data['parent_id'])
            $data['parent_tasks'] = $this->task_model->get_parents($project, $data['parent_id']);
        else
            $data['parent_tasks'] = false;
        
        $data['children_tasks'] = $this->task_model->get_hierarchy($project, $id, false);
        
        $user = $this->user_model->get($data['user_id']);
        $data['user'] = $user['email'];
        
        $data['comments'] = $this->task_model->get_comments($id);
        
        $data['task_history'] = $this->task_model->get_history($id);
        $data['task_history_last'] = $this->task_model->get_last_history($id);
        $data['status_arr'] = $this->task_model->get_status_array();
        
        $this->template->show('task', $data);
    }
    
    public function save()
    {
        $project_id = $this->input->post('project_id');
        $id = $this->input->post('task_id');
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        
        if($this->form_validation->run() === false)  {
            $this->error = true;
            
            if ($id)
                $this->edit ($project_id, $id);
            else
                if($project_id)
                    $this->add ($project_id);
                else
                    redirect('dashboard');
            
            return;
        }
        
        $this->load->model('task_model');
        
        $sql_data = array(
            'project_id' => $project_id,
            'status' => ($this->input->post('status'))?$this->input->post('status'):0,
            'title' => $this->input->post('title'),
            'parent_id' => $this->input->post('parent_id'),
            'description' => $this->input->post('description'),
            'priority' => $this->input->post('priority'),
            'user_id'     => $this->input->post('user_id'),
            'files'    => ($this->input->post('files'))?$this->input->post('files'):'',
            'database' => ($this->input->post('database'))?$this->input->post('database'):''
        );
        
        if ($id)
            $this->task_model->update($this->input->post('project_id'), $id, $sql_data);
        else
            $id = $this->task_model->create($sql_data);

        redirect('task/view/'.$this->input->post('project_id').'/'.$id);
    }
    
    public function move($project, $id, $status)
    {
        $this->load->model('task_model');
        
        $sql_data = array(
            'status' => $status
        );

        $this->task_model->update($project, $id, $sql_data, true);

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
            'task_id' => $this->input->post('task_id'),
            'user_id' => $this->session->userdata('user'),
            'comment' => $this->input->post('comment')
        );
        
        $this->load->model('task_model');
        $this->task_model->create_comment($data);

        redirect('task/view/'.$this->input->post('project_id').'/'.$data['task_id']);
    }
    
    public function timer($project, $id, $action = 'stop')
    {
        $this->load->model('task_model');
        
        $result = $this->task_model->timer($id, $action);

        if(!IS_AJAX)
            redirect('task/view/'.$project.'/'.$id);
        else {
            if($result)
                echo json_encode (array(
                    'result' => 1,
                    'new_action' => base_url().'task/timer/'.$project.'/'.$id.(($action == 'stop')?'/play':'/stop')
                    ));
            else
                echo json_encode (array('result' => 0));
        }
    }
    
}
