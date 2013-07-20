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
        
        $data['date_updated'] = date('Y-m-d H:i:s');
        
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
        
        $data['date_updated'] = date('Y-m-d H:i:s');
        
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
                order_by('th.date_finished', 'desc')->
                order_by('t.status', 'asc')->
                order_by('t.priority', 'asc');
        
        $get = $this->db->get();

        if ($id) return $get->row_array();
        if($get->num_rows > 0) return $get->result_array();
        return array();
    }
    
    public function get_hierarchy($project, $parent = null, $single_array = true, $output = array(), $i = 0, $current_id = false)
    {
        // Select tasks according to project and parent - count children so it knows if there are any
        $this->db->select('task.*, count(child.task_id) as children')->
                from('task')->
                join('task child', 'task.task_id = child.parent_id', 'left')->
                where('task.project_id', $project)->
                where('task.parent_id', $parent)->
                order_by('task.title', 'asc')->
                group_by('task.task_id');
        
        if($current_id)
            $this->db->where('task.task_id !=', $current_id);
        
        $tasks = $this->db->get()->result_array();

        // If it IS a single array, add children in the same array
        if($single_array) {
            foreach ($tasks as $value) {
                $output[] = array(
                    'id' => $value['task_id'], 
                    'title' => str_repeat('&nbsp;&nbsp;', $i).(($i)?'- ':'').$value['title']
                    );

                if($value['children'] > 0)
                    $output = $this->get_hierarchy($project, $value['task_id'], $single_array, $output, $i+1, $current_id);
            }
        } else {
            // If it IS NOT a single array, add children as a sub array
            foreach ($tasks as $value) {
                $output[] = array(
                    'id' => $value['task_id'], 
                    'title' => $value['title'],
                    'children' => ($value['children'] > 0)?$this->get_hierarchy($project, $value['task_id'], $single_array, $output, $i+1, $current_id):array()
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
        // Get Projects and 5 tasks in each project
        $query = 'SELECT t.*, p.id as project_id, p.name as project_name
FROM project p
JOIN user_project up ON p.id = up.project AND up.user = '.$user.'
LEFT JOIN (SELECT tmp.* FROM
(SELECT *, IF( @prev <> project_id, @rownum := 1, @rownum := @rownum+1 ) AS rank, @prev := project_id
FROM task t
JOIN (SELECT @rownum := NULL, @prev := 0) AS r 
WHERE user_id = '.$user.' AND status < 3
ORDER BY t.project_id) AS tmp
WHERE tmp.rank <= 5) AS t ON p.id = t.project_id
ORDER BY p.id desc, t.status desc';
        
        $get = $this->db->query($query);

        if($get->num_rows > 0)
            return $get->result_array();
        
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
        $this->db->select('task_comments.*, user.name, user.email')->
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
    
    public function get_github($project_id)
    {
        return $this->db->select('task.*, user.github_username')->
                where('project_id', $project_id)->
                join('user', 'task.user_id = user.id')->
                order_by('task.code', 'asc')->
                get('task')->result_array();
    }
    
    public function update_local($new, $upd)
    {
        $this->db->trans_start();
        
        if($new)
            $this->db->insert_batch('task', $new);
        
        if($upd)
            $this->db->update_batch('task', $upd, 'task_id');
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }    

    public function update_github($new, $upd, $repo, $access_token)
    {
        if($new) {
            // If task code change
            $task_upd = array();
        
            // Create new issues
            foreach ($new as $issue) {
                $task_id = $issue['task_id'];
                $code = $issue['code'];
                unset($issue['task_id']);
                unset($issue['code']);
                
                $result = $this->_github_edit_issue($repo, $access_token, $issue);
                $result_array = json_decode($result, TRUE);
                
                if($issue['state'] == 'closed') {
                    // Set issue to closed state
                    $issue['number'] = $result_array['number'];
                    $this->_github_edit_issue($repo, $access_token, $issue);
                }
                                
                // Check if task code needs to be updated
                if($issue['number'] != $code) {
                    $task_upd[] = array(
                        'task_id' => $task_id,
                        'code'    => $result_array['number']
                    );
                }
            }
        }
        
        // Update task codes
        $this->update_local(array(), $task_upd);
        
        if($upd) {
            // Edit existing issues
            foreach ($upd as $issue) {
                $this->_github_edit_issue($repo, $access_token, $issue);
            }
        }
    }
    
    private function _github_edit_issue($repo, $access_token, $issue)
    {
        // Open connection
        $ch = curl_init();

        // Set options
        if(isset($issue['number'])) {
            $number = $issue['number'];
            unset($issue['number']);
            curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/$repo/issues/$number?access_token=$access_token");
        } else {
            curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/$repo/issues?access_token=$access_token");
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($issue));

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);
        
        return $result;
    }
}
