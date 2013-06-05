<?php
class Invitation extends CI_Model{
    public $_table = "invitation";

    function __construct(){
        parent::__construct();
    }


    public function getInviter($code){
       $this->db->select("userid");
       $this->db->where("invitationcode", $code);
       $this->db->where("expires >", time());
       $this->db->limit(1);
       $query = $this->db->get($this->_table);
       //$count = $this->db->count_all_results($this->_table);
       $res = $query->result();
       if(count($res) == 1){
            $row = $res[0];
            return $row->userid;
       }
       return false;

    }

    public function rmCode($code){
       $this->db->where("userid !=", 0);
       $this->db->where("invitationcode", $code);
       $this->db->or_where("expires <", time());
       $this->db->delete($this->_table);
       //echo $this->db->last_query();
    }

    public function genCode($userid){
        //$this->db->where
        $data = array('userid'=>$userid, 'expires'=>time()+86400,'state'=>'new');
        $data['invitationcode'] = sprintf("%7d%03d",  mt_rand(1000000,9999999), $userid%1000);
        $ret = $this->db->insert($this->_table, $data);
        if($ret){
            return $data['invitationcode'];
        }
        return false;

    }


}
