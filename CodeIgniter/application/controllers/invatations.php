<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invatations extends CI_Controller{

    public function getCode(){
        $data = $_POST;
        $code = $data['invitationcode'];
        $this->load->model("Invitation");
    }

    public function edit($id){
    }
}
