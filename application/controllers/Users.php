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
                    'message' => 'Wrong email or password.'
                ], 502);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Method POST (data kosong).'
            ], 502);
        }
    }

    public function registration_post() {
        $nama_lengkap = strip_tags($this->post('fullname'));
        $kesibukan = strip_tags($this->post('sibuk'));
        $email = strip_tags($this->post('email'));
        $password = $this->post('password');
        
        // Validate the post data
        if(!empty($nama_lengkap) && !empty($kesibukan) && !empty($email) && !empty($password)) {
            
            // Check if the given email already exists
            $con['returnType'] = 'count';
            $con['conditions'] = array(
                'email' => $email,
            );
            $userCount = $this->user->getData($con);
            
            if ($userCount > 0) {
                $this->response([
                    'status' => false,
                    'message' => 'Email sudah terdaftar.'
                ], 502);
            } else {
                // Insert user data
                $userData = array(
                    'nama_lengkap' => $first_name,
                    'kesibukan' => $last_name,
                    'email' => $email,
                    'password' => md5($password)
                );
                $insert = $this->user->insert($userData);
                
                if ($insert) {
                    $this->response([
                        'status' => TRUE,
                        'message' => 'The user has been added successfully.',
                        'data' => $insert
                    ], REST_Controller::HTTP_OK);
                } else {
                    $this->response([
                        'status' => false,
                        'message' => 'Tidak berhasil menambahkan user (sqlerror).'
                    ], 502);
                }
            }
        } else {
            $this->response([
                'status' => false,
                'message' => 'Method POST (data kosong).'
            ], 502);
        }
    }
}
?>
