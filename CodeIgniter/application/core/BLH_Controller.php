<?php

class BLH_Controller extends CI_Controller{
    public function __construct($need_auth=true){
        parent::__construct();


        if($need_auth){
            $this->auth();
        }
    }

    public function auth($ret=false){
       $this->load->library("session");
       if(false != $this->session->userdata('userid') && 1 == $this->session->userdata('logged_in')){
            $this->_userid = $this->session->userdata('userid');
            $this->_nickname = $this->session->userdata('nickname');
            return true;
       }
       $this->session->sess_destroy();
       if(!$ret){
        echo json_encode(array("status"=>false, "errmsg"=>"please login first!"));
        exit;
       }
       return false;
    }

}
