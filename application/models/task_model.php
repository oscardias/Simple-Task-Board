<?php

class Task_model extends CI_Model {

    public function create($data)
    {
        if(!$data['parent_id'])
            $data['parent_id'] = null;
        
        $this->db->select_max('code');
        $this->db->where('project_id', $data['project_id']);
        $get = $this->db->get('task');
        
        if($get->num_rows > 0) {
            $row = $get->row_array();
            $data['code'] = $row['code'] + 1;
        } else
            $data['code'] = 1;
        
        $insert = $this->db->insert('task', $data);
        if($insert)
            return $this->db->insert_id();
        
        return false;
    }


    public function update($project, $id, $data, $move = false)
    {
        $this->db->trans_start();
        
        if(isset($data['parent_id']) && !$data['parent_id'])
            $data['parent_id'] = null;
        
        if($move) {
            $this->timer($id, 'move', $data['status']);
        }
        
        $this->db->where('project_id', $project);
        $this->db->where('task_id', $id);
        $this->db->update('task', $data);
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get($project, $id = false, $status = false)
    {
        $this->db->select('t.*, sum(th.duration) as total_duration, th_last.date_created as task_history_date_created')->
                from('task t')->
                join('task_history th', 't.task_id = th.task_id', 'left')->
                join('task_history th_last', 't.task_id = th_last.task_id AND th_last.date_finished IS NULL', 'left')->
                where('t.project_id', $project);
        
        if ($id) $this->db->where('t.task_id', $id);
        if ($status) $this->db->where('t.status', $status);
        
        $this->db->group_by('t.task_id')->
                order_by('t.status', 'asc')->
                order_by('t.priority', 'asc');
        
        $get = $this->db->get();

        if ($id) return $get->row_array();
        if($get->num_rows > 0) return $get->result_array();
        return array();
    }
    
    public function get_hierarchy($project, $parent = null, $single_array = true, $output = array(), $i = 0)
    {
        // Select tasks according to project and parent - count children so it knows if there are any
        $this->db->select('task.*, count(child.task_id) as children')->
                from('task')->
                join('task child', 'task.task_id = child.parent_id', 'left')->
                where('task.project_id', $project)->
                where('task.parent_id', $parent)->
                order_by('task.title', 'asc')->
                group_by('task.task_id');
        
        $tasks = $this->db->get()->result_array();

        // If it IS a single array, add children in the same array
        if($single_array) {
            foreach ($tasks as $value) {
                $output[] = array(
                    'id' => $value['task_id'], 
                    'title' => str_repeat('&nbsp;&nbsp;', $i).(($i)?'- ':'').$value['title']
                    );

                if($value['children'] > 0)
                    $output = $this->get_hierarchy($project, $value['task_id'], $single_array, $output, $i+1);
            }
        } else {
            // If it IS NOT a single array, add children as a sub array
            foreach ($tasks as $value) {
                $output[] = array(
                    'id' => $value['task_id'], 
                    'title' => $value['title'],
                    'children' => ($value['children'] > 0)?$this->get_hierarchy($project, $value['task_id'], $single_array, $output, $i+1):array()
                    );
            }
        }
        
        return $output;
    }
    
    public function get_parents($project, $id)
    {
        $this->db->select('task.*')->
                from('task')->
                where('task.project_id', $project)->
                where('task.task_id', $id);
        
        $tasks = $this->db->get()->result_array();

        foreach ($tasks as $value) {
            if($value['parent_id'])
                $output = $this->get_parents ($project, $value['parent_id']);
            
            $output[] = array('id' => $value['task_id'], 'title' => $value['title']);
        }
        
        return $output;
    }
    
    public function get_user_tasks($user)
    {
        $this->db->select('t.*');
        $this->db->distinct();
        $this->db->from('task t');
        $this->db->where('t.user_id', $user);
        $this->db->where('t.status !=', 3);
        $this->db->order_by('t.status', 'desc');
        $get = $this->db->get();

        if($get->num_rows > 0) return $get->result_array();
        return array();
    }
    
    public function get_project_user_tasks($project, $user, $limit = FALSE)
    {
        $this->db->select('t.*')->
                distinct()->
                from('task t')->
                where('t.user_id', $user)->
                where('t.project_id', $project)->
                where('t.status !=', 3)->
                order_by('t.status', 'desc');
        if($limit)
            $this->db->limit($limit);
        $get = $this->db->get();

        if($get->num_rows > 0) return $get->result_array();
        return array();
    }
    
    public function get_related_users($project)
    {
        $this->db->select('u.*, up.project');
        
        $this->db->from('user u');
        $this->db->join('user_project up', 'up.user = u.id and up.project = '.$project, 'left');
        $this->db->order_by('u.email', 'asc');
        $get = $this->db->get();

        if($get->num_rows > 0) return $get->result_array();
        return array();
    }

    public function delete($project, $id)
    {
        $this->db->trans_start();
                
        // Remove task
        $this->db->where('project_id', $project);
        $this->db->where('task_id', $id);
        $this->db->delete('task');
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
    
    public function create_comment($data)
    {
        return $this->db->insert('task_comments', $data);
    }

    public function get_comments($task)
    {
        $this->db->select('task_comments.*, user.email')->
                from('task_comments')->
                join('user', 'task_comments.user_id = user.id')->
                where('task_id', $task);
        $get = $this->db->get();
        
        if($get->num_rows > 0)
            return $get->result_array();
        
        return array();
    }
    
    public function get_history($task, $detail = false)
    {
        if($detail)
            // Get all entries
            $this->db->select('u.email, th.status, th.date_created, th.date_finished, th.duration')->
                    from('task_history th')->
                    join('user u', 'th.user_id = u.id', 'left')->
                    where('task_id', $task)->
                    order_by('status, date_created');
        else
            // Get sum fro each phase
            $this->db->select('status, sum(duration) as duration')->
                    from('task_history')->
                    where('task_id', $task)->
                    group_by('status')->
                    order_by('status');
        
        $get = $this->db->get();
        
        if($get->num_rows > 0)
            return $get->result_array();
        
        return array();
    }
    
    public function get_last_history($task)
    {
        $this->db->select('status, date_created')->
                from('task_history')->
                where('task_id', $task)->
                where('date_finished', NULL);
        $get = $this->db->get();
        
        if($get->num_rows > 0)
            return $get->row_array();
        
        return array();
    }
    
    public function get_status_array()
    {
        return array(
            0 => 'To Do',
            1 => 'In Progress',
            2 => 'Testing',
            3 => 'Done'
        );
    }
    
    public function timer($task_id, $type = 'move', $status = false)
    {
        $this->db->trans_start();
        
        if($type != 'play') {
            
            // Update user ID, date finished and duration
            $history = $this->db->select('task_history_id, status, date_created')->
                    where('task_id', $task_id)->
                    where('date_finished', NULL)->
                    get('task_history')->row_array();

            if($history){
                $now = strtotime(date('Y-m-d H:i:s'));
                $before = strtotime($history['date_created']);
                
                if($status === false)
                    $status = $history['status'];

                $this->db->where('task_history_id', $history['task_history_id'])->
                        set('user_id', $this->session->userdata('user'))->
                        set('date_finished', date('Y-m-d H:i:s'))->
                        set('duration', $now - $before)->
                        update('task_history');
            }
            
        }
        
        if($status === false) {
            // Get status of last entry in case it was not defined
            $history = $this->db->select('status')->
                    from('task_history')->
                    where('task_id', $task_id)->
                    order_by('task_history_id', 'desc')->
                    get()->row_array();
            $status = $history['status'];
        }

        if($type != 'stop') {
            
            // Create new entry in history
            $history_data = array(
                'task_id' => $task_id,
                'user_id' => $this->session->userdata('user'),
                'status' => $status,
                'date_created' => date('Y-m-d H:i:s'),
                'date_finished' => NULL
            );
            $this->db->insert('task_history', $history_data);
            
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
