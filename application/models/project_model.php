<?php

class Project_model extends CI_Model {

    public function create($data)
    {
        $insert = $this->db->insert('project', $data);
        return $insert;
    }


    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $update = $this->db->update('project', $data);
        return $update;
    }

    public function get($id = false)
    {
        if ($id) $this->db->where('id', $id);
        $this->db->order_by('name', 'asc');
        $get = $this->db->get('project');

        if($id) return $get->row();
        if($get->num_rows > 1) return $get->result();
        return array();
    }
    
    public function get_user_owned($user)
    {
        $this->db->where('user', $user);
        $this->db->order_by('name', 'asc');
        $get = $this->db->get('project');

        if($get->num_rows > 0) return $get->result();
        return array();
    }
    
    public function get_user_related($user)
    {
        $this->db->from('project p');
        $this->db->join('user_project u', 'p.id = u.project');
        $this->db->where('u.user', $user);
        $this->db->order_by('p.name', 'asc');
        $get = $this->db->get();

        if($get->num_rows > 0) return $get->result();
        return array();
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('project');
    }

}
