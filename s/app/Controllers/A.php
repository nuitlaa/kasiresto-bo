<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class A extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
    }
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }

    public function del($table,$id,$field='id'){
        $db = db_connect();
        $db->table($table)->where([$field=>$id])->delete();
        $message = 'data telah dihapus';
        $ret['status']  = true;
        $ret['message'] = $message;
        echo json_encode($ret);
    }

    public function remove($table,$id,$field='id',$status='status'){
        $db = db_connect();
        $set[$status]    = 'removed';
        switch ($table) {
            case 'account_type':    $set['default']       = 0; break;
            default: break;
        }
        $db->table($table)->where([$field=>$id])->set($set)->update();
        $message = 'data telah dihapus';
        $ret['status']  = true;
        $ret['message'] = $message;
        echo json_encode($ret);
    }

}