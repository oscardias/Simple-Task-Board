<?php

class User_model extends CI_Model {

    private $salt = 'r4nd0m';
    
    private $error = false;
    
    // User info
    private $photo;
    
    public $USER_LEVEL_ADMIN = 1;
    public $USER_LEVEL_PM = 2;
    public $USER_LEVEL_DEV = 3;

    public function create($data)
    {
        $data['password'] = sha1($data['password'].$this->salt);
        $insert = $this->db->insert('user', $data);
        return $insert;
    }

    public function update($id, $data)
    {
        if(isset($data['password']))
            $data['password'] = sha1($data['password'].$this->salt);
        $this->db->where('id', $id);
        $update = $this->db->update('user', $data);
        return $update;
    }

    public function get($id = false)
    {
        if ($id) $this->db->where('id', $id);
        $this->db->order_by('email', 'asc');
        $get = $this->db->get('user');

        if($id) return $get->row_array();
        if($get->num_rows > 0) return $get->result_array();
        return array();
    }
    
    public function get_count()
    {
        $this->db->select('count(*) as count');
        $get = $this->db->get('user')->row_array();

        return $get['count'];
    }
    
    public function validate($email, $password)
    {
        $this->db->where('email', $email)->where('password', sha1($password.$this->salt));
        $get = $this->db->get('user');

        if($get->num_rows > 0) return $get->row_array();
        return array();
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('user');
    }

    public function upload_photo()
    {
        $this->load->library('upload');

        $config = array(
            'allowed_types' => 'gif|png|jpg|jpeg',
            'upload_path' => getcwd().'/upload/profile/'.$this->session->userdata('user'),
            'max_size' => 2048,
            'overwrite' => true
        );
        $this->upload->initialize($config);

        // Create path
        if(is_dir($config['upload_path'])) {
            
            $objects = scandir($config['upload_path']);
            foreach ($objects as $object)
            {
                if($object != '.' AND $object != '..')
                {
                    unlink($config['upload_path'].'/'.$object);
                }
            }
            
        } else {
            
            if(!mkdir($config['upload_path'], 0755, true)){
                $this->error = 'An error occurred while we uploaded your picture.';
                return false;
            }
            
        }

        // Run the upload
        if (!$this->upload->do_upload('photo')) {
            // Problem in upload
            $this->error = $this->upload->display_errors();
            return false;
        }

        // Resize images
        $upload_data = $this->upload->data();
        if(!$this->user_model->prepare_image($upload_data)){
            return false;
        }

        $this->photo = $upload_data['file_name'];
        
        return true;
    }

    public function prepare_image($data)
    {
        // Make it square - Crop
        if($data['image_width'] > $data['image_height'])
            $size = $data['image_height'];
        else
            $size = $data['image_width'];
        
        $config = array(
            'source_image'   => $data['full_path'],
            'maintain_ratio' => false,
            'width'          => $size,
            'height'         => $size
        );
        
        $this->load->library('image_lib'); 

        $this->image_lib->clear();
        $this->image_lib->initialize($config); 
        if(!$this->image_lib->crop()){
            $this->error = $this->image_lib->display_errors();
            return false;
        }
        
        // Resize in three different sizes
        $target = array(
            0 => array('name' => 'large', 'width' => 128, 'height' => 128),
            1 => array('name' => 'medium', 'width' => 64, 'height' => 64),
            2 => array('name' => 'thumb', 'width' => 32, 'height' => 32)
        );
        
        for($i = 0; $i < count($target); $i++) {
            // Image library settings
            // Resize
            $config = array(
                'source_image' => $data['full_path'],
                'new_image'    => $data['file_path'].$target[$i]['name'].$data['file_name'],
                'width'        => $target[$i]['width'],
                'height'       => $target[$i]['height'],
                'master_dim'   => (($data['image_width'] / $data['image_height']) >= ($target[$i]['width'] / $target[$i]['height']))?'height':'width'
            );

            $this->image_lib->clear();
            $this->image_lib->initialize($config); 
            if(!$this->image_lib->resize()){
                $this->error = $this->image_lib->display_errors();
                return false;
            }
            
        }
        
        return true;
    }
    
    function get_photo()
    {
        return $this->photo;
    }
    
    function error_message()
    {
        return $this->error;
    }
    
}
