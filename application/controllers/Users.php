<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

Class Users Extends REST_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->helper(['jwt', 'authorization']);  
        $this->load->model('user');
    }

    public function verify_post()
    {
        $headers = $this->input->request_headers();
        var_dump($headers);
        $token = $headers[17]["Authorization"];
        var_dump($token);
        exit();
        try {
            $data = AUTHORIZATION::validateToken($token);
            if ($data === false) {
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                $this->response($response, $status);
                exit();
            } else {
                return $data;
            }
        } catch (Exception $e) {
            $status = parent::HTTP_UNAUTHORIZED;
            $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
            $this->response($response, $status);
        }
    }

    public function sendemail_post()
    {
        $this->load->library('email');

        $this->email->from('rplgdc@yukmulaicoding.com', 'RPLGDC');
        $this->email->to('sinambelacornelius@gmail.com');

        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');

        var_dump($this->email->send());
    }

    public function forget_post() {
        $email = $this->post('email');
        $con['returnType'] = 'count';
        $con['conditions'] = array(
            'email' => $email,
        );
        $userCount = $this->user->getData($con);
        if ($userCount) {

        }
    }
    public function profile_post() {
        $data = $this->verify_request();
        var_dump($data);
        exit();
        $status = parent::HTTP_OK;
        if ($status == 200) {
            $response = ['status' => $status, 'data' => $data];
            $this->response($response, $status)
 ;       } else {
            $response = ['status' => $status, 'data' => 'Format Error!'];
            $this->response($response, $status);
        }
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
                $token = AUTHORIZATION::generateToken($user);
                $status = parent::HTTP_OK;
                $this->response([
                    'status' => $status,
                    'access_token' => $token
                ], $status);
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
                    'nama_lengkap' => $nama_lengkap,
                    'kesibukan' => $kesibukan,
                    'email' => $email,
                    'password' => md5($password)
                );
                $insert = $this->user->insert($userData);
                
                if ($insert) {
                    $token = AUTHORIZATION::generateToken($insert);
                    $status = parent::HTTP_OK;
                    $this->response([
                        'status' => $status,
                        'access_token' => $token
                    ], $status);
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
