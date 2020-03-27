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
            $kategori = strip_tags($this->post('kategori'));
            $judul = strip_tags($this->post('judul'));
            $pembimbing = strip_tags($this->post('narasumber'));
            $isi = $this->post('text');
            $url_ex = strip_tags($this->post('url_video'));
            if (!empty($kategori) && !empty($judul) && !empty($pembimbing) && !empty($isi)) {
                $postData = array(
                    'kategori' => $kategori,
                    'judul_materi' => $judul,
                    'pembimbing' => $pembimbing,
                    'isi_materi' => $isi,
                );
                if (!empty($url_ex)) {
                    $postData['url_video'] = $url_ex;
                } else {
                    $postData['url_video'] = " ";
                }
                $insert = $this->course->insert($postData, 'katalog');
                if ($insert) {
                    $response = ['status' => true, 'message' => 'Success Create!'];
                    $this->response($response, parent::HTTP_UNAUTHORIZED);
                } else {
                    $response = ['status' => parent::HTTP_UNAUTHORIZED, 'message' => 'Insert fail!'];
                    $this->response($response, parent::HTTP_UNAUTHORIZED);
                }
            } else {
                $response = ['status' => parent::HTTP_UNAUTHORIZED, 'message' => 'Empty Post!'];
                $this->response($response, parent::HTTP_UNAUTHORIZED);
            }
        } else {
            $response = ['status' => parent::HTTP_UNAUTHORIZED, 'message' => 'Unauthorized Access!'];
            $this->response($response, parent::HTTP_UNAUTHORIZED);
        }
    }
}
?>
