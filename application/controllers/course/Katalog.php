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
        $this->load->model('REST');
    }

    private function level() {
        $data = $this->REST->verify();
        if ($data) {
            $con = array('id_akun' => $data->data);
            $user = $this->course->getData($con, 'account');
            if ($user["level"] == "Admin") {
                return true;
            } else {
                return false;
            }
        }
    }

    public function add_katalog_post() {
        $data = $this->level();
        if ($data) {
            echo "asek";
        } else {
            echo "mpos";
        }
    }
}
?>
