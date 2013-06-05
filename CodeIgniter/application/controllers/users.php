<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends BLH_Controller{
    public function __construct(){
        parent::__construct(false);
    }

    public function register(){
        $data = $_POST;
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $ret = array('status'=>false);
        if ($this->form_validation->run("account/reg") == FALSE){
            $this->form_validation->set_error_delimiters('', '');
            $ret['status'] = false;
            $ret['errcode']=1;
            $ret['msg']=validation_errors();
        }else{
            $code = $data['invitationcode'];
            unset($data['invitationcode']);
            $this->load->model("Invitation");
            $inviteUserId = $this->Invitation->getInviter($code);
            if($inviteUserId!==false){
                $data['invitedby'] = $inviteUserId;
                $this->load->model("Userinfo");
                $id = $this->Userinfo->addNew($data);
                $ret['status'] = $id !=false;
                if($ret['status']){
                    $this->Invitation->rmCode($code);
                }else{
                    $ret['errcode']=2;
                    $ret['msg']='register failed';
                }
            }else{
                $ret['errcode']=3;
                $ret['msg'] = "invalid invitation code";
            }
        }
        echo json_encode($ret);
    }

    public function login(){
        $ret = array();
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
        $this->_email= $this->input->post('email');
        if( $this->form_validation->run("account/login") == false){
            $ret['status'] = false;
            $ret['errcode'] = 1;
            $ret['msg'] = validation_errors();

        }else{
            $this->load->model("Userinfo");
            $this->Userinfo->afterLogin($this->_userid, $this->_nickname);
            $ret['status'] = true;
        }
        echo json_encode($ret);
    }

    public function logout(){
        if($this->auth(true)){
            $this->session->sess_destroy();
        }
        echo json_encode(array("status"=>true));
    }

    public function invitationcode(){
        $ret = array('status'=>false);
        if($this->auth(true)){
            $this->load->model("Invitation");
            $code = $this->Invitation->genCode($this->_userid);
            if($code){
                $ret['code'] = $code;
                $ret['status'] = true;
            }
        }
        echo json_encode($ret);
    }

    public function edit($id=null){
        $ret = array('status'=>false);
        $data = $this->input->post(NULL, true);
        unset($data['email']);
        unset($data['invitedby']);
        if(is_numeric($id) && count($data)>0 && $this->auth(true) ){

            $this->load->model("Userinfo");
            $ret['status'] = $this->Userinfo->edit($id, $data); ;
            if($ret['status'] && $this->input->post('nickname')){
                $this->session->set_userdata('nickname', $this->input->post('nickname', true));
            }
        }
        echo json_encode($ret);
    }


    function info($id=null){
        $ret = array('status'=>false);
        if($id && is_numeric($id) && $this->auth(true)){
           $this->load->model("Userinfo"); 
           $data = $this->Userinfo->info($id);
           if($data){
            $ret['status']=true;
            $ret['info'] = $data;
           }
        }
        
        echo json_encode($ret);
        
    }

    function password_check($passwd=null){
        $email = $this->_email ? $this->_email : $this->input->post("email");
        if(null == $passwd || ! isset($email) ) return false;
        $this->load->model("Userinfo");
        $ret = $this->Userinfo->checkUserPass($email, $passwd);
        if($ret != false && is_array($ret)){
            $this->_userid = $ret['id'];
            $this->_nickname= $ret['nickname'];
        }
        return $ret != false;
    }

    function authfailed(){
        if($this->session){
            $this->session->sess_destroy();
        }
        echo json_encode(array('status'=>false, 'message'=>'登录失败'));
    }
}
