<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transport extends CI_Controller {

    public function index()
    {
        redirect('task');
    }

    public function add()
    {
        $this->load->model('task_model');
        
        $tasks = $this->task_model->get(false, '2');
                
        $list     = '';
        $files    = '';
        $database = '';
        
        if($tasks) {
            foreach ($tasks as $task) {
                $list .= $task->id.',';

                $parts = explode(',', $task->files);
                foreach ($parts as $item)
                    $files[$item] = $item;

                $parts = explode(',', $task->database);
                foreach ($parts as $item)
                    $database[$item] = $item;
            }
        }
        
        $data['tasks']    = ($list)?trim($list, ','):'';
        $data['files']    = ($files)?trim(implode(',', $files), ','):'';
        $data['database'] = ($database)?trim(implode(',', $database), ','):'';
        
        $this->template->show('transport_add', $data);
    }
    
    public function edit($id)
    {
        $this->load->model('transport_model');
        
        $transports = $this->transport_model->get($id);
        
        foreach ($transports as $transport) {
            $data['id']       = $transport->id;
            $data['tasks']    = $transport->tasks;
            $data['files']    = $transport->files;
            $data['database'] = $transport->database;
        }
        
        $this->template->show('transport_add', $data);
    }
    
    public function close($id)
    {
        $this->load->model('transport_model');
        
        $sql_data = array(
            'open' => '0'
        );
        
        $this->transport_model->update($id,$sql_data);

        redirect('task');
    }
    
    public function save()
    {
        $this->load->model('transport_model');
        
        $sql_data = array(
            'tasks'    => $this->input->post('tasks'),
            'files'    => $this->input->post('files'),
            'database' => $this->input->post('database')
        );
        
        if ($this->input->post('id'))
            $this->transport_model->update($this->input->post('id'),$sql_data);
        else
            $this->transport_model->create($sql_data);

        redirect('task');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */