<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/RestController.php';

class Auth extends REST_Controller {

    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('user');
    }

    public function index_post() 
    {
        $email = $this->post('email');
        $password = $this->post('password');
        if(!empty($email) && !empty($password)){
            $con['returnType'] = 'single';
            $con['conditions'] = array(
                'email' => $email,
                'password' => md5($password),
                'status' => 1
            );
            $user = $this->user->getRows($con);
            var_dump($con);
        } else {
            $this->response("Provide email and password.", REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function getData()
    {
        $model = $this->user->get_all();
        $result = array();
        foreach ($model as $hasil) {
            $result[] = array(
                "id" => $hasil->id,
                "nama_Pengguna" => $hasil->nama_lengkap,
                "kesibukan" => $hasil->kesibukan,
                "email" => $hasil->email,
                "Password" => md5($hasil->password),
                "level" => $hasil->level);
        }
        echo json_encode($result,TRUE);
    }
}