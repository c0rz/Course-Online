<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class REST extends CI_Model
{
	public function __construct() {
        parent::__construct();
        $this->load->helper(['jwt', 'authorization']);
    }

    public function verify()
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
}
