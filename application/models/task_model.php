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

        if($get->num_rows > 0) return $get->result();
        return false;
    }

    public function count()
    {
//        if ($filter != '') {
//            foreach ($filter as $key => $value) {
//                $parts = explode('|', $key);
//                switch ($parts[0]) {
//                    case 'like':
//                        $this->db->like($parts[1], $value);
//                        break;
//
//                    default:
//                        $this->db->where($key, $value);
//                        break;
//                }
//            }
//        }
//        $count = $this->db->count_all_results('contato');
//
//        return $count;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('task');
    }

}
