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
        
        $this->title = 'New Task';
        $this->menu = 'dashboard|tasks';
        
        $data['parent_id']   = 0;
        $data['title']       = '';
        $data['description'] = '';
        $data['priority']    = '2';
        $data['duration']    = '';
        $data['start_date']  = '';
        
        $data['project_id']  = $project;
        $data['users'] = $this->task_model->get_related_users($project);
        $data['user_id'] = $this->session->userdata('user');
        $data['tasks'] = $this->task_model->get_hierarchy($project);
        
        if($this->error)
            $data['error'] = $this->error;
        
        $this->load->view('task_add', $data);
    }

    public function edit($project, $id)
    {
        $this->load->model('task_model');
        
        $data = $this->task_model->get($project, $id);
        $data['start_date'] = date('m/d/Y', strtotime($data['start_date']));
        
        $this->title = "Edit Task #{$data['code']}";
        $this->menu = 'dashboard|tasks';
        
        $data['project_id']  = $project;
        $data['users'] = $this->task_model->get_related_users($project);
        $data['tasks'] = $this->task_model->get_hierarchy($project, null, true, array(), 0, $id);
        
        if($this->error)
            $data['error'] = $this->error;
        
        $this->load->view('task_add', $data);
    }

    public function view($project, $id)
    {
        $this->load->helper('tasks');
        $this->load->helper('stb_date');
        
        $this->load->model('task_model');
        $this->load->model('user_model');
        
        $data = $this->task_model->get($project, $id);
        
        $this->title = "View Task #{$data['code']}";
        $this->menu = 'dashboard|tasks|edit_task';
                
        $data['project_id']  = $project;
        
        if($data['parent_id'])
            $data['parent_tasks'] = $this->task_model->get_parents($project, $data['parent_id']);
        else
            $data['parent_tasks'] = false;
        
        $data['children_tasks'] = $this->task_model->get_hierarchy($project, $id, false);
        
        $user = $this->user_model->get($data['user_id']);
        $data['user'] = $user;
        
        $data['comments'] = $this->task_model->get_comments($id);
        
        $data['task_history'] = $this->task_model->get_history($id);
        $data['task_history_last'] = $this->task_model->get_last_history($id);
        $data['status_arr'] = $this->task_model->get_status_array();
        
        $this->load->view('task', $data);
    }
    
    public function save()
    {
        $project_id = $this->input->post('project_id');
        $id = $this->input->post('task_id');
        
        if($this->input->post('cancel') !== FALSE) {
            if($id)
                redirect('task/view/'.$project_id.'/'.$id);
            else
                redirect('project/tasks/'.$project_id);
        }
                    
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('parent_id', 'Parent', '');
        $this->form_validation->set_rules('priority', 'Priority', '');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('user_id', 'Assigned to', '');
        $this->form_validation->set_rules('duration', 'Estimated Duration', 'trim');
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim');
        
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
            'project_id'  => $project_id,
            'status'      => ($this->input->post('status'))?$this->input->post('status'):0,
            'title'       => $this->input->post('title'),
            'parent_id'   => $this->input->post('parent_id'),
            'description' => $this->input->post('description'),
            'priority'    => $this->input->post('priority'),
            'user_id'     => $this->input->post('user_id'),
            'duration'    => $this->input->post('duration'),
            'start_date'  => date('Y-m-d', strtotime($this->input->post('start_date')))
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
    
    public function ajax_comment($project_id, $task_id, $status)
    {
        if(!IS_AJAX) {
            redirect('project/tasks/'.$project_id);
        }
        
        $this->layout = 'ajax';
        
        if($this->input->post('user_id')) {
            
            // Save user
            $this->load->model('task_model');
            
            if($this->input->post('user_id') != $this->session->userdata('user')) {
                $sql_data = array('user_id' => $this->input->post('user_id'));

                $this->task_model->update(
                        $this->input->post('project_id'),
                        $this->input->post('task_id'),
                        $sql_data);
            }
            
            // Save comment
            if($this->input->post('comment')) {
                $data = array(
                    'task_id' => $this->input->post('task_id'),
                    'user_id' => $this->session->userdata('user'),
                    'comment' => $this->input->post('comment')
                );

                $this->task_model->create_comment($data);
            }

            echo base_url("task/move/$project_id/$task_id/$status");
            
        } else {
            
            // Load modal
            $this->load->model('task_model');
            
            $data = array(
                'task_id' => $task_id,
                'project_id' => $project_id,
                'status' => $status,
                'user_id' => $this->session->userdata('user'),
                'users' => $this->task_model->get_related_users($project_id)
            );

            $this->load->view('task_comment_modal', $data);
            
        }
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
        $this->layout = 'ajax';
        
        $this->load->helper('stb_date');
        $this->load->model('task_model');
        
        $result = $this->task_model->timer($id, $action);

        if(!IS_AJAX)
            redirect('task/view/'.$project.'/'.$id);
        else {
            if($result) {
                $task  = $this->task_model->get($project, $id);
                if($action == 'stop')
                    $duration = timespan_diff($task['total_duration']);
                else
                    $duration = timespan_diff($task['total_duration'] + (time() - strtotime($task['task_history_date_created'])));
                
                echo json_encode (array(
                    'result' => 1,
                    'new_action' => base_url().'task/timer/'.$project.'/'.$id.(($action == 'stop')?'/play':'/stop'),
                    'duration' => $duration
                    ));
            } else
                echo json_encode (array('result' => 0));
        }
    }
    
    public function history($project, $id)
    {
        $this->load->helper('stb_date');
        $this->load->model('task_model');
        
        // Get the task
        $data = $this->task_model->get($project, $id);
        
        // Get the history
        $data['task_history'] = $this->task_model->get_history($id, true);
        $data['status_arr'] = $this->task_model->get_status_array();

        if(!IS_AJAX) {
            $data['project_id']  = $project;
            $data['task_id']  = $id;
            
            $this->title = "Task #{$data['code']} History";
            $this->menu = 'dashboard|tasks|view_task';
            
            $this->load->view('task_history', $data);
        } else {
            $this->layout = 'ajax';
            
            $this->load->view('task_history_modal', $data);
        }
    }    
    
}
