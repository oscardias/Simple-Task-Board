<?php

class Task_model extends CI_Model {

    public function create($data)
    {
        $insert = $this->db->insert('task', $data);
        return $insert;
    }


    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $update = $this->db->update('task', $data);
        return $update;
    }

    public function get($id = false, $status = false)
    {
        if ($id) $this->db->where('id', $id);
        if ($status) $this->db->where('status', $status);
        $this->db->order_by('status', 'asc');
        $this->db->order_by('priority', 'asc');
        $get = $this->db->get('task');

        if ($id) return $get->row();
        if($get->num_rows > 0) return $get->result();
        return array();
    }
    
    public function get_project_tasks($project)
    {
        if ($project) $this->db->where('project', $project);
        $this->db->order_by('status', 'asc');
        $this->db->order_by('priority', 'asc');
        $get = $this->db->get('task');

        if($get->num_rows > 0) return $get->result();
        return array();
    }
    
    public function get_user_tasks($user)
    {
        $this->db->from('task t');
        $this->db->join('task_history h', 't.id = h.task');
        $this->db->where('h.user', $user);
        $this->db->order_by('t.status', 'asc');
        $get = $this->db->get();

        if($get->num_rows > 0) return $get->result();
        return array();
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('task');
    }

}
