<?php
class Blocks extends BLH_Controller{
    protected $_tableName;
    public function __construct(){
        parent::__construct(true);
    }


    protected function _setTableName($blockName){
        $this->_tableName = "{$blockName}blockpost";
    }

    public function types(){
        $this->load->model("Block");
        $ret = $this->Block->getTypeList();
        echo json_encode($ret);
    }

    public function posts($blockname, $page=1, $pagesize=10){
        $this->_setTableName($blockname);

        $page = intval($page);
        $pagesize = intval($pagesize);

        $ret= false;
        if($page>=1 && $pagesize>=1){
            $this->load->model("Block");
            $this->Block->init($this->_tableName);
            $ret = $this->Block->posts($page, $pagesize);
        }
        echo json_encode($ret);
    }


    public function reply($blockname){
        $this->add($blockname);
    }

    public function add($blockname){
        $this->_setTableName($blockname);

        $ret= array('status'=>false);
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
        if($this->form_validation->run("blocks/create") == false){
            $ret['errcode'] = 1;
            $ret['errmsg'] = validation_errors();
        }else{
            $data = $this->input->post(NULL, true);
            $data['userid'] = $this->_userid;
            $id = $this->Block->create($data);
            if(!$id){
                $ret['errcode'] = 2;
            }else{
                $ret['status'] = true;
                $ret['id'] = $id;
            }
        }
        echo json_encode($ret);
    }

    public function detail($blockname, $rid ,$page=1, $pagesize=10){
        $this->_setTableName($blockname);
        $ret = false;
        $page = intval($page);
        $pagesize=intval($pagesize);

        if($rid && $page>=1 && $pagesize>=1){
            $this->load->model("Block");
            $this->Block->init($this->_tableName);
            $ret = $this->Block->detailof($rid, $page, $pagesize);
        }
        echo json_encode($ret);
    }

    function pids_check($pid=null){
        $rootid = $this->input->post("rootid");
        $pid = $pid ? $pid:0;
        $rootid = $rootid ? $rootid : $pid;
        $this->load->model("Block");
        $this->Block->init($this->_tableName);
        $ret = $this->Block->checkIds($pid, $rootid);
        if(!$ret){
            $this->form_validation->set_message('pids_check', 'invalid pid or rootid');
        }
        return $ret;
    }

}
