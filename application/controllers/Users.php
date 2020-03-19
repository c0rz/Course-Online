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

    public function index()
    {
      // Konfigurasi email
        $config = [
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'protocol'  => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_user' => 'sinambelaalfredo@gmail.com',  // Email gmail
            'smtp_pass'   => 'alfredo1999',  // Password gmail
            'smtp_crypto' => 'ssl',
            'smtp_port'   => 465,
            'crlf'    => "\r\n",
            'newline' => "\r\n"
        ];

        // Load library email dan konfigurasinya
        $this->load->library('email', $config);

        // Email dan nama pengirim
        $this->email->from('no-reply@masrud.com', 'MasRud.com');

        // Email penerima
        $this->email->to('sinambelacornelius@domain.com'); // Ganti dengan email tujuan

        // Lampiran email, isi dengan url/path file
        $this->email->attach('https://masrud.com/content/images/20181215150137-codeigniter-smtp-gmail.png');

        // Subject email
        $this->email->subject('Kirim Email dengan SMTP Gmail CodeIgniter | MasRud.com');

        // Isi email
        $this->email->message("Ini adalah contoh email yang dikirim menggunakan SMTP Gmail pada CodeIgniter.<br><br> Klik <strong><a href='https://masrud.com/post/kirim-email-dengan-smtp-gmail' target='_blank' rel='noopener'>disini</a></strong> untuk melihat tutorialnya.");

        // Tampilkan pesan sukses atau error
        if ($this->email->send()) {
            echo 'Sukses! email berhasil dikirim.';
        } else {
            echo 'Error! email tidak dapat dikirim.';
        }
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
    public function Profile_get() {
        $data = $this->verify_request();
        $status = parent::HTTP_OK;
        if ($status == 200) {
            $response = ['status' => $status, 'data' => $data];
            $this->response($response, $status);
        } else {
            $response = ['status' => $status, 'data' => 'Unauthorized Access!'];
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

    private function verify_request()
    {
        $headers = $this->input->request_headers();
        $token = $headers['Authorization'];
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
}
?>
