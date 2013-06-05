<?php
class Userinfo extends CI_Model{
    protected $_table="userinfo";
    protected $_basicFields=array("id","nickname","icon");
    protected $_infoFields =array("email","sex","company","position","cellphone","qq","weibo","weixin");

    function __construct(){
        parent::__construct();
    }

    public function addNew($data){
        if($data['passwd'] != $data['passwdconf']){
            return false;
        }
        unset($data['passwdconf']);
        $data['passwd'] = md5($this->config->item('salt').$data['passwd']);
        //$data['passwd'] = md5($data['passwd']);

        $ret = $this->db->insert($this->_table, $data); 
        if($ret){
            return $this->db->insert_id();
        }
        return false;
    }

    public function edit($id, $data){
        $this->db->where('id', $id);
        if(isset($data['passwd'])){
            $data['passwd'] = md5($this->config->item('salt').$data['passwd']);
        }
        $res = $this->db->update($this->_table, $data);
        return $res;
    }

    public function isValidId($id){
        $this->db->where("id", $id);
        return 1 == $this->db->count_all_results($this->_table);
    }

    public function afterLogin($userid, $username){
        $this->load->library('session');
        $data = array("userid"=>$userid,"nickname"=>$username, "logged_in"=>1);
        $this->session->set_userdata($data);
    }

    public function checkUserPass($email, $passwd){
       $this->db->select("id, nickname, passwd");
       $this->db->where("email", $email);
       $query = $this->db->get("userinfo");
       if($query->num_rows() == 1){
           $row = $query->row();
           if($row->passwd == md5($this->config->item('salt').$passwd)){
            return array('id'=>$row->id, 'nickname'=>$row->nickname);
           }
       }
       return false;
    }

    public function info($id){
        $this->db->where('id', $id);
        $res = $this->db->get($this->_table);
        if($res->num_rows() == 1){
           $row = $res->row();
           return $this->_fetchFields($row);
        }
        return false;
    }

    protected function _fetchFields($row){
        $data = array();
        foreach($this->_basicFields as $f){
            $data[$f] = $row->$f;
        }
        foreach($this->_infoFields as $f){
            $privateControl = "public{$f}";
            if($row->$privateControl){
                $data[$f] = $row->$f;
            }
        }
        return $data;
    }


}
