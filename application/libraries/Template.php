<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Template {
    function show($view, $data = array())
    {
        // Get current CI Instance
        $CI = & get_instance();
                
        // Load template views
        $CI->load->view('template/header', $data);
        $CI->load->view($view, $data);
        $CI->load->view('template/footer', $data);
    }
    
    function menu($view)
    {
        // Get current CI Instance
        $CI = & get_instance();
                
        // Load menu template
        $CI->load->view('template/menu', array('view' => $view));
    }
    
    function tasks($project, $tasks)
    {
        // Get current CI Instance
        $CI = & get_instance();
                
        $i = 0;
        foreach ($tasks as $task) {
            $i++;
            // Load menu template
            $CI->load->view('template/single_task', array('i' => $i, 'project' => $project, 'task' => $task));
        }
    }
}

/* End of file Template.php */