<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * Outputs the task hierarchy HTML
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('task_hierarchy_html'))
{
    function task_hierarchy_html($project, $tasks, $i = 0)
    {
        if($i == 0)
            echo '<ul class="task-hierarchy">';
        else
            echo '<ul>';
        
        foreach($tasks as $value){
            echo '<li class="level-'.$i.'">';
            
            echo anchor(base_url()."task/view/{$project}/{$value['id']}", $value['title']);
            if($value['children'])
                task_hierarchy_html ($project, $value['children'], $i + 1);
            
            echo '</li>';
        }
        
        echo '</ul>';
    }
}

/**
 * Outputs the task parents HTML
 *
 * @access	public
 * @return	bool
 */
if ( ! function_exists('task_parents_html'))
{
    function task_parents_html($project, $parent)
    {
        if($parent) {
            
            $CI =& get_instance();
            $CI->load->model('task_model');
            $tasks = $CI->task_model->get_parents($project, $parent);
            
            foreach($tasks as $value){
                echo anchor(base_url()."task/view/{$project}/{$value['id']}", $value['title']);
                echo ' > ';
            }
        }
    }
}

// ------------------------------------------------------------------------
