<?php
namespace App\Controllers;
use App\Helpers\global_helper;
date_default_timezone_set("Asia/Bangkok");
class Notif extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper('global');
        helper('text');
    }
    public function index(){
        if (isset($_SESSION['userid'])&&$_SESSION['userid']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='admin';

        $data['table']      = 'dashboard';
        $data['menu']       = 'dashboard';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo backend($page,$data);
    }
    public function welcome($token=false){
        $html = '';
        $ret['status']      = true;
        $ret['message']     = 'notif loaded';
        $ret['html']        = $html;
        echo json_encode($ret);
    }

    public function getfirst(){
        $db = db_connect();
        $html = '';
        $userid = usertoken($_SESSION['usertoken']);
        $pesan          = $db->table('notif')->where(['category'=>'message','for'=>$userid])->orderBy('id DESC')->limit(10)->get()->getResultArray();
        $pemberitahuan  = $db->table('notif')->whereIn('category', ['product','user','transfer'])->where('for', $userid)->orderBy('id DESC')->limit(10)->get()->getResultArray();
        $transaction    = $db->table('notif')->where(['category'=>'transaction','for'=>$userid])->orderBy('id DESC')->limit(10)->get()->getResultArray();

        $ret['pesan']['list']           = $pesan;
        $ret['pesan']['total']          = count($pesan);
        $ret['pesan']['unread']         = $db->table('notif')->where(['category'=>'message','for'=>$userid,'status'=>'unread'])->countAllResults();

        $ret['pemberitahuan']['list']   = $pemberitahuan;
        $ret['pemberitahuan']['total']  = count($pemberitahuan);
        $ret['pemberitahuan']['unread'] = $db->table('notif')->whereIn('category', ['product','user','transfer'])->where('for', $userid)->where('status','unread')->countAllResults();

        $ret['transaksi']['list']       = $transaction;
        $ret['transaksi']['total']      = count($transaction);
        $ret['transaksi']['unread']     = $db->table('notif')->where(['category'=>'transaction','for'=>$userid,'status'=>'unread'])->countAllResults();
        
        $ret['status']  = true;
        $ret['message'] = 'new notif';
        $ret['html']    = $html;
        echo json_encode($ret);
    }

    public function pcheck($token=false){  
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        while (true) {
            $pesan          = $db->table('notif')->where(['category'=>'message','for'=>$userid,'status'=>'unload'])->orderBy('id DESC')->get()->getResultArray();
            $pemberitahuan  = $db->table('notif')->whereIn('category', ['product','user','transfer'])->where('status','unload')->where('for', $userid)->orderBy('id DESC')->get()->getResultArray();
            $transaction    = $db->table('notif')->where(['category'=>'transaction','for'=>$userid,'status'=>'unload'])->orderBy('id DESC')->get()->getResultArray();
            if (count($pesan)>0 || count($pemberitahuan)>0 || count($transaction)>0) {
                    $ret['pesan']['list']           = $pesan;
                    $ret['pesan']['total']          = count($pesan);

                    $ret['pemberitahuan']['list']   = $pemberitahuan;
                    $ret['pemberitahuan']['total']  = count($pemberitahuan);

                    $ret['transaksi']['list']       = $transaction;
                    $ret['transaksi']['total']      = count($transaction);
                    
                    $ret['status']  = true;
                    $ret['message'] = 'new notif';
                    $ret['html']    = $html;


                    $db->table('notif')->where(['category'=>'message','for'=>$userid,'status'=>'unload'])->set(['status'=>'unread'])->update();
                    $db->table('notif')->whereIn('category', ['product','user','transfer'])->where('status','unload')->where('for', $userid)->set(['status'=>'unread'])->update();
                    $db->table('notif')->where(['category'=>'transaction','for'=>$userid,'status'=>'unload'])->set(['status'=>'unread'])->update()

                    echo json_encode($ret);
                    flush();
                return;
            }
            sleep(10); // recheck tiap detik
        }
    }

    public function check($token=false){  
        $db = db_connect();
        $html = ''; 
        $ret['status']  = true;
        $ret['message'] = 'new notif';
        $ret['html']    = $html;
        echo json_encode($ret);
        sleep(50); // recheck tiap detik
        return false;
    }
}