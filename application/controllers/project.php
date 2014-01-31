<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends CI_Controller {
    
    private $error = false;
    
    public function index()
    {
        redirect('dashboard');
    }
    
    public function tasks($project_id)
    {
        $this->load->helper('stb_date');
        $this->load->helper('tasks');
        
        // Check permission
        if(!$this->usercontrol->has_permission('project', 'tasks'))
            redirect('dashboard');
        
        // Load tasks
        $this->load->model('task_model');
        $tasks = $this->task_model->get($project_id);
        
        foreach ($tasks as $task) {
            if ($task['status'] == 0) {
                $data['stories'][] = $task;
            } elseif ($task['status'] == 1) {
                $data['tasks'][] = $task;
            } elseif ($task['status'] == 2) {
                $data['tests'][] = $task;
            } elseif ($task['status'] == 3) {
                $data['done'][] = $task;
            }
        }

        // Load project info
        $this->load->model('project_model');
        $project = $this->project_model->get($project_id);
        
        $this->title = "Project: {$project['name']}";
        $this->menu = 'dashboard|edit_project|new_task';
        
        // Check if github sync can be used
        if($project['github_repo'] && $this->session->userdata('github')) {
            $this->github_sync = $project_id;
        }
        
        $data['project_id']    = $project_id;
        
        $data['current_user'] = $this->session->userdata('user');
        
        $db_users = $this->project_model->get_related_users($project_id);
        $users = array();
        foreach ($db_users as $user) {
            $users[$user['id']] = $user;
        }
        $data['users'] = $users;
        
        // Load text helper to be used in the view
        $this->load->helper('text');
        
        // Load View
        $this->load->view('task_board', $data);
    }

    public function add()
    {
        // Check permission
        if(!$this->usercontrol->has_permission('project'))
            redirect('dashboard');
        
        $this->load->model('project_model');
        
        $this->title = "New Project";
        $this->menu = 'dashboard';
        
        $data['user']        = '';
        $data['name']        = '';
        $data['description'] = '';
        $data['github_repo'] = '';
        
        $users = $this->project_model->get_related_users();
        foreach ($users as $key => $value) {
            if($value['id'] == $this->session->userdata('user'))
                $users[$key]['project'] = 1;
            else
                $users[$key]['project'] = 0;
        }
        $data['users'] = $users;
        
        if($this->error)
            $data['error'] = $this->error;
        
        $this->load->view('project_add', $data);
    }

    public function edit($id)
    {
        // Check permission
        if(!$this->usercontrol->has_permission('project'))
            redirect('dashboard');
        
        $this->load->model('project_model');
        
        $data = $this->project_model->get($id);
        
        $this->title = "Edit Project #$id";
        $this->menu = 'dashboard|tasks';
        
        $data['project_id']  = $id;
        $data['users'] = $this->project_model->get_related_users($id);
        
        if($this->error)
            $data['error'] = $this->error;
        
        $this->load->view('project_add', $data);
    }
    
    public function save()
    {
        if($this->input->post('cancel') !== FALSE)
            redirect('dashboard');
            
        // Check permission
        if(!$this->usercontrol->has_permission('project'))
            redirect('dashboard');

        // Get project ID - false if new entry
        $project_id = $this->input->post('id');
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('github_repo', 'Github Repo', 'trim');
        $this->form_validation->set_rules('users[]', 'Associated Users', '');
        
        if($this->form_validation->run() === false)  {
            $this->error = true;
            
            if ($project_id)
                $this->edit ($project_id);
            else
                $this->add ();
            
            return;
        }
        
        $this->load->model('project_model');

        $sql_data = array(
            'user'        => $this->session->userdata('user'),
            'name'        => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'github_repo' => $this->input->post('github_repo')
        );

        if ($project_id)
            $this->project_model->update($project_id,$sql_data);
        else
            $project_id = $this->project_model->create($sql_data);

        // Related users
        $this->project_model->delete_related($project_id);

        $users = $this->input->post('users');
        foreach ($users as $user) {
            $sql_data = array(
                'user' => $user,
                'project' => $project_id
            );
            $this->project_model->create_related($sql_data);
        }

        if ($project_id)
            redirect('project/tasks/'.$project_id);
        else
            redirect('project');

    }
    
    function github_sync($project_id)
    {
        // Validate if user logged with github
        if(!$this->session->userdata('github'))
            redirect('project/tasks/'.$project_id);
        
        // Load models
        $this->load->model('project_model');
        $this->load->model('user_model');
        $this->load->model('task_model');
        
        // Get project info
        $project = $this->project_model->get($project_id);
        
        // Get user info
        $user = $this->user_model->get($this->session->userdata('user'));
        
        // Validate if necessary info was set
        if(!($project['github_repo'] && $user['github_token'])) {
            redirect('project/tasks/'.$project_id);
        }
        
        // Fetch info from Github
        $issues = $this->_get_github_issues($project['github_repo'], $user['github_token']);
        
        // Reorder issues
        $ordered_issues = array();
        foreach ($issues as $issue) {
            $ordered_issues[(int)$issue['number']] = $issue;
        }
        $issues = $ordered_issues;
        unset($ordered_issues);
        
        // Fetch local tasks
        $tasks = $this->task_model->get_github($project_id);
        
        //************
        // Sync tasks
        // Vars to store updates
        $task_upd = array();
        $task_new = array();
        
        // Loop through local tasks and check github issues
        foreach ($tasks as $task) {
            $code = (int)$task['github_code'];
            // Check if there is an issue with this code
            if($code && isset($issues[$code])) {
                // Check if entry is the same
                // Assignee
                if($task['github_username'] != $issues[$code]['assignee']['login']) {
                    // Only update this info if user has the github username locally
                    if($task['github_username'] && $issues[$code]['assignee']['login']) {
                        $user = $this->user_model->get_github($issues[$code]['assignee']['login']);

                        if($user) {
                            $task_upd[$code] = array(
                                'task_id' => $task['task_id'],
                                'user_id' => $user['id']
                            );
                        }
                    }
                }
                    
                // Contents
                if(
                    $task['title'] != $issues[$code]['title'] ||
                    $task['description'] != $issues[$code]['body'] ||
                    (
                        ( $task['status'] == 3 && $issues[$code]['state'] == 'open' ) ||
                        ( $task['status'] < 3 && $issues[$code]['state'] == 'closed' )
                    )
                  )
                {
                    $task_upd[$code] = array(
                        'task_id' => $task['task_id'],
                        'github_code' => $issues[$code]['number'],
                        'status' => ($issues[$code]['state'] == 'closed')?3:0,
                        'title' => $issues[$code]['title'],
                        'description' => $issues[$code]['body'],
                        'date_updated' => date('Y-m-d H:i:s', strtotime($issues[$code]['updated_at']))
                    );
                }
                
                // Unset this issue, so we don't loop it again later
                unset($issues[$code]);
            }
        }
        
        // If issues remaining, loop and create local tasks
        if(count($issues)) {
            // Get last code
            $max_code = $this->db->select_max('code')->
                    where('project_id', $project_id)->
                    get('task')->row_array();
            $max_code = $max_code['code'] + 1;

            foreach ($issues as $key => $issue) {
                $user = $this->user_model->get_github($issue['user']['login']);
                
                $task_new[$key] = array(
                    'project_id' => $project_id,
                    'parent_id' => NULL,
                    'user_id' => $user['id'],
                    'code' => $max_code,
                    'github_sync' => 1,
                    'github_code' => $issue['number'],
                    'status' => ($issue['state'] == 'closed')?3:0,
                    'title' => $issue['title'],
                    'priority' => 2,
                    'due_date' => NULL,
                    'description' => $issue['body'],
                    'date_updated' => date('Y-m-d H:i:s', strtotime($issue['updated_at']))
                );
                
                $max_code++;
            }
        }
        
        //*****************
        // Update local tatks
        $this->task_model->update_local($task_new, $task_upd);
        
        // Redirect to task board
        redirect('project/tasks/'.$project_id);
    }
    
    private function _get_github_issues($repo, $access_token, $state = 'open', $url = false)
    {
        // Open connection
        $ch = curl_init();

        // Set options
        if($url === FALSE)
            $url = "https://api.github.com/repos/$repo/issues?state=$state&access_token=$access_token";
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Simple Task Board');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

        // Execute post
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        $link = '';
        preg_match("/Link: <(.*)>; rel=\"next\"/", $header, $link);

        // Close connection
        curl_close($ch);
        
        $issues = json_decode($body, TRUE);
        
        if($link) {
            $additional = $this->_get_github_issues($repo, $access_token, $state, $link[1]);
            return array_merge($issues, $additional);
        } else {
            if($state == 'open') {
                $additional = $this->_get_github_issues($repo, $access_token, 'close');
                return array_merge($issues, $additional);
            } else {
                return $issues;
            }
        }
    }
}
