<?php

class Transport_model extends CI_Model {

    public function create($data)
    {
        $insert = $this->db->insert('transport', $data);
        return $insert;
    }


    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $update = $this->db->update('transport', $data);
        return $update;
    }

    public function get($id = false, $open = false)
    {
        if ($id) $this->db->where('id', $id);
        if ($open) $this->db->where('open', '1');
        $this->db->order_by('id', 'desc');
        $get = $this->db->get('transport');

        if($get->num_rows > 0) return $get->result();
        return false;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('transport');
    }

}
