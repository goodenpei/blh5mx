<?php
class Block extends CI_Model{
    protected $_fieldlist = "id,pid,rootid,title,content,posttime";

    protected $_tableName;
    function __construct(){
    }

    function init($tablename){
        $this->_tableName = $tablename;
    }

    function getTypeList(){
        $query = $this->db->get("block");
        $ret = array();
        foreach($query->result() as $row){
            $ret[] = array('name'=>$row->name,'cnname'=>$row->cnname);
        }
        return $ret;
    }

    function create($data){
        unset($data['id']);
        unset($data['posttime']);
        $data['state'] = "new";
        if(!isset($data['rootid']) && isset($data['pid']) ){
            $data['rootid'] = $data['pid'];
        }
        $ret = $this->db->insert($this->_tableName, $data);
        if($ret){
            $insertid = $this->db->insert_id();
            $pid = isset($data['pid']) ? $data['pid'] : 0;
            $rid = isset($data['rootid']) ? $data['rootid'] : 0;
            if($pid || $rid){
                $this->updateReplyCount(array($pid, $rid));
            }
            return $insertid;
        }
        return false;
    }

    public function updateReplyCount($idlist){
        $idlist = array_unique($idlist);

        $idstring = join(",", $idlist);

        $this->db->query("update {$this->_tableName} set `replycount`=`replycount`+1 where id in($idstring)");
        //echo $this->db->last_query();
    }

    public function posts($page, $pagesize){
        $this->db->select($this->_fieldlist);
        $this->db->order_by("posttime" ,"desc");
        $this->db->limit( $pagesize, ($page-1)*$pagesize );
        $query = $this->db->get($this->_tableName);
        //echo $this->db->last_query(),"\n";
        $ids = array();
        $result = $query->result();
        foreach($result as $row){
            if($row->rootid > 0){
                $ids[] = $row->rootid;
            }
        }
        $this->db->select($this->_fieldlist);
        $this->db->where_in("id", array_unique($ids));
        $pQuery = $this->db->get($this->_tableName);
        //echo $this->db->last_query(),"\n";
        $rootlist = $this->_obj2hash($pQuery->result(), "id");
        foreach($result as $index=> $row){
            if(isset($rootlist[$row->rootid])){
                $result[$index]->root = $rootlist[$row->rootid];
            }
        }
        return $result;

    }

    //return all list
    public function detailof($id, $page, $pagesize){
        $this->db->select($this->_fieldlist);
        $where = "`state`='new' and (`id`='{$id}' or `rootid`='{$id}')";
        $this->db->where($where, NULL, false);
        $this->db->order_by("rootid" ,"asc");
        $this->db->order_by("posttime" ,"desc");
        $this->db->limit( $pagesize, ($page-1)*$pagesize );
        $query = $this->db->get($this->_tableName);
        //echo $this->db->last_query();

        return $query->result();
    }

    public function checkIds($pid, $rootid){
        if($pid==0 && $rootid==0) return true;
        else if($pid==0 && $rootid!=0) return false;
        $this->db->select("id,pid,rootid");
        $this->db->where_in("id", array($pid, $rootid));
        $query = $this->db->get($this->_tableName);
        if( $query->num_rows() < 1){
            return false;
        }
        $pidOK=$topOK=false;
        foreach($query->result() as $row){
            if($row->id == $pid){
                $pidOK = true;
            }
            if($row->id== $rootid){
                $topOK = true;
            }
        }
        return $pidOK && ($rootid==0 || $topOK);
    }

    protected function _obj2hash($data, $key){
        $ret = array();
        foreach($data as $row){
            $ret[$row->$key] = $row;
        }
        return $ret;
    }
}
