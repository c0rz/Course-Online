<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

Class Users Extends REST_Controller {
    
    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('user');
    }
    
    function login_post(){
        $email = $this->post('email');
        $password = $this->post('password');
        if (!empty($email) && !empty($password)) {
            $con['returnType'] = 'single';
            $con['conditions'] = array(
                'email' => $email,
                'password' => md5($password)
            );
            $user = $this->user->getData($con);
            if ($user) {
                $this->response([
                    'status' => true,
                    'message' => 'User login successful.',
                    'data' => $user
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => '"Wrong email or password.'
                ], 502);
            }
        } else {
            $data = array('status' => false, 'message' => 'Method POST');
            $this->response($data, 502);
        }
    }

}
?>
