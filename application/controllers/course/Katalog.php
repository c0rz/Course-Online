<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

Class Katalog Extends REST_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->helper(['jwt', 'authorization']);  
        $this->load->model('course');
        $this->load->model('check_jwt');
    }

    public function profile_post() {
        $data = $this->check_jwt->verify();
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
}
?>
