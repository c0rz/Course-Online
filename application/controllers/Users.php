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
        $this->load->model('nembak');
    }

    private function verify()
    {
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $token = $headers["Authorization"];
            try {
                $data = AUTHORIZATION::validateToken($token);
                if ($data === false) {
                    $status = parent::HTTP_UNAUTHORIZED;
                    $response = ['status' => $status, 'message' => 'Unauthorized Access!'];
                    $this->response($response, $status);
                } else {
                    return $data;
                }
            } catch (Exception $e) {
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'message' => 'Unauthorized Access!'];
                $this->response($response, $status);
            }
        }
    }

    public function change_info_put() {
        $data = $this->verify();
        if ($data) {
            $private_id = $data->data;
            $full_name = strip_tags($this->put('nama'));
            $password = $this->put('password');
            $password2 = $this->put('password_confirm');
            $kesibukan = strip_tags($this->put('sibuk'));
            if (!empty($full_name)) {
                $userData = array();
                if (!empty($full_name)) {
                    $userData['nama_lengkap'] = $full_name;
                }
                if (!empty($kesibukan)) {
                    $userData['last_name'] = $last_name;
                }
                if (!empty($password) && !empty($password2)) {
                    if ($password == $password2) {
                        $userData['password'] = md5($password);
                    } else {
                        $response = ['status' => false, 'message' => 'Password not same.'];
                        $this->response($response, parent::HTTP_OK);
                    }
                }
                $update = $this->user->update($userData, $private_id);
                if($update){
                    $response = ['status' => true, 'message' => 'The user info has been updated successfully.'];
                    $this->response($response, REST_Controller::HTTP_OK);
                } else {
                    $response = ['status' => false, 'message' => 'Unauthorized Access! [UPDATE]'];
                    $this->response($response, parent::HTTP_OK);
                }
            } else {
                $response = ['status' => false, 'message' => 'Unauthorized Access! [PUT]'];
                $this->response($response, parent::HTTP_OK);
            }

        } else {
            $response = ['status' => false, 'message' => 'Unauthorized Access!'];
            $this->response($response, parent::HTTP_OK);
        }
    }

    public function forget_passwordx_post() {
        $email = $this->post('email');
        $status = parent::HTTP_UNAUTHORIZED;
        if (!empty($email)) {
            $con['returnType'] = 'count';
            $con['conditions'] = array(
                'email' => $email,
            );
            $userCount = $this->user->getData($con);
            if ($userCount) {
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'message' => 'User Ditemukan!'];
                $this->response($response, $status);
            } else {
                $response = ['status' => false, 'message' => 'User Tidak Ditermukan'];
                $this->response($response, $status);
            }
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => false, 'message' => 'Unauthorized Access!'];
            $this->response($response, $status);
        }
    }

    public function profile_post() {
        $data = $this->verify();
        if ($data) {
            $con = array('id_akun' => $data->data);
            $user = $this->user->getData($con);
            $response = ['status' => true, 'data' => $user];
            $this->response($response, parent::HTTP_OK);
        } else {
            $response = ['status' => false, 'message' => 'Unauthorized Access!'];
            $this->response($response, parent::HTTP_OK);
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
                $date = new DateTime();
                $token = AUTHORIZATION::generateToken(['data' => $user['id_akun'], 'exp' => $date->getTimestamp() + 60*60*5]);
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
                'message' => 'Unauthorized Access!'
            ], 502);
        }
    }

    public function registration_post() {
        $nama_lengkap = strip_tags($this->post('fullname'));
        $kesibukan = strip_tags($this->post('sibuk'));
        $email = strip_tags($this->post('email'));
        $password = $this->post('password');
        
        if(!empty($nama_lengkap) && !empty($kesibukan) && !empty($email) && !empty($password)) {
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
                $userData = array(
                    'nama_lengkap' => $nama_lengkap,
                    'kesibukan' => $kesibukan,
                    'email' => $email,
                    'password' => md5($password),
                    'level' => 'Member',
                );
                $insert = json_decode($this->user->insert($userData));
                if ($insert) {
                    $date = new DateTime();
                    $token = AUTHORIZATION::generateToken(['data' => $insert, 'exp' => $date->getTimestamp() + 60*60*5]);
                    $this->response([
                        'status' => true,
                        'access_token' => $token
                    ], parent::HTTP_OK);
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
                'message' => 'Unauthorized Access!'
            ], 502);
        }
    }
}
?>
