<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Usercontrol {
    private $CI;
    
    private $level =  FALSE;
    
    public function __construct() {
        $this->CI = & get_instance();
    }
    
    public function set_level($l)
    {
        $this->level = $l;
    }
    
    function has_permission($controller, $method = false)
    {
        // Check if user is logged in
        if(!$this->CI->session->userdata('logged'))
            return false;
        
        // Get user level if it was not set
        if($this->level === FALSE)
            $this->level = $this->CI->session->userdata('level');
        
        switch ($controller) {
            case 'dashboard':
                // All users have access to tasks
                return true;
                break;
            
            case 'project':
                // All users have access to project tasks
                if($method == 'tasks')
                    return true;
                // Other methods are innaccessible to developers
                if($this->level != 3)
                    return true;
                break;
            
            case 'task':
                // All users have access to tasks
                return true;
                break;
                
            case 'user':
                // Only full access has acess to users
                if($this->level == 1)
                    return true;
                break;

            default:
                break;
        }
        
        return false;
    }
    
}

/* End of file Template.php */