<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    public function index()
    {
        // Check if users exist
        $this->load->model('user_model');
        $users = $this->user_model->get_count();
        if($users == 0)
            redirect('install');
        
        // Check if database version is correct
        $this->load->model('database_model');
        if(!$this->database_model->is_up_to_date())
            redirect('install');
        
        // Load View
        $this->title  = 'Login';
        $this->layout = 'login';
        $this->menu   = 'none';
        
        $data['email'] = '';
        $data['password'] = '';
                
        $this->load->view('login', $data);
    }
    
    public function validate()
    {
        $this->load->model('user_model');
        $result = $this->user_model->validate($this->input->post('email'),$this->input->post('password'));
        
        if($result) {
            $this->session->set_userdata(array(
                'logged' => true,
                'user'  => $result['id'],
                'level' => $result['level']
            ));
            
            redirect('dashboard');
        } else {
            // Load View
            $this->title  = 'Login';
            $this->layout = 'login';
            $this->menu   = 'none';

            $data['email'] = $this->input->post('email');
            $data['password'] = $this->input->post('password');
            
            $data['error'] = true;
            
            $this->load->view('login', $data);
        }
    }
    
    public function logout()
    {
        $this->session->unset_userdata('logged');

        redirect('login');
    }
    
    public function github()
    {
        $this->config->load('oauth');
            
        $client_id     = $this->config->item('github_client_id');
        $client_secret = $this->config->item('github_client_secret');
        $redirect_url  = $this->config->item('github_redirect_url');

        //get request , either code from github, or login request
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            //authorised at github
            if(isset($_GET['code']))
            {
                $code = $_GET['code'];

                //perform post request now
                $post = http_build_query(array(
                    'client_id' => $client_id ,
                    'redirect_uri' => $redirect_url ,
                    'client_secret' => $client_secret,
                    'code' => $code ,
                ));

                $context = stream_context_create(array("http" => array(
                    "method" => "POST",
                    "header" => "Content-Type: application/x-www-form-urlencoded\r\n" .
                                "Content-Length: ". strlen($post) . "\r\n".
                                "Accept: application/json" ,  
                    "content" => $post,
                ))); 

                $json_data = file_get_contents("https://github.com/login/oauth/access_token", false, $context);

                $r = json_decode($json_data , true);

                $access_token = $r['access_token'];

                $url = "https://api.github.com/user?access_token=$access_token";

                $data =  file_get_contents($url);

                //echo $data;
                $user_data = json_decode($data , true);
                $username = $user_data['login'];

                $emails =  file_get_contents("https://api.github.com/user/emails?access_token=$access_token");
                $emails = json_decode($emails , true);
                $email = $emails[0];

//                $signup_data = array(
//                    'username' => $username ,
//                    'email' => $email ,
//                    'source' => 'github' ,
//                );

                // Check if user exists and login
                $this->load->model('user_model');
                $result = $this->user_model->validate_github($email);
                if($result) {
                    $this->session->set_userdata(array(
                        'logged' => true,
                        'user'  => $result['id'],
                        'level' => $result['level']
                    ));

                    redirect('dashboard');
                } else {
                    // Load View
                    $this->title  = 'Login';
                    $this->layout = 'login';
                    $this->menu   = 'none';

                    $view_data['email'] = $email;

                    $view_data['error'] = true;
                    $view_data['error_github'] = true;

                    $this->load->view('login', $view_data);
                }
            }
            else
            {
                $url = "https://github.com/login/oauth/authorize?client_id=$client_id&redirect_uri=$redirect_url&scope=user:email,repo";
                redirect($url);
            }
        }
    }
}
