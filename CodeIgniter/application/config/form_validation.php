<?php
$config = array(
        'account/reg' => array(
            array(
                'field' => 'nickname',
                'label' => 'nickname',
                'rules' => 'required'
                ),
            array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'required|valid_email'
                ),
            array(
                'field' => 'passwd',
                'label' => 'passwd',
                'rules' => 'required'
                ),
            array(
                'field' => 'passwdconf',
                'label' => 'passwdconf',
                'rules' => 'required'
                ),
            array(
                'field' => 'invitationcode',
                'label' => 'invitationcode',
                'rules' => 'required'
            )
     ),
     "account/login"=> array(
        array(
                'field' => 'email',
                'label' => 'email',
                'rules' => 'trim|required|xss_clean|valid_email'
        ),
        array(
                'field' => 'passwd',
                'label' => 'passwd',
                'rules' => 'trim|required|callback_password_check'
        ),

     ),
     "blocks/create"=> array(
        array(
                'field' => 'title',
                'label' => 'title',
                'rules' => 'trim|required|xss_clean'
        ),
        array(
                'field' => 'content', 
                'label' => 'content', 
                'rules' => 'trim|required|xss_clean', 
        ),
        array(
                'field' => 'pid', 
                'label' => 'pid', 
                'rules' => 'trim|callback_pids_check', 
        ),
     ),
);
