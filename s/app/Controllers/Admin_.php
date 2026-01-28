<?php
namespace App\Controllers;
use App\Helpers\global_helper;
use App\Libraries\Faskes_lib;
date_default_timezone_set("Asia/Bangkok");
class Admin_ extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper('global');
        helper('text');
    }
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }

    public function register($username,$password){ 
        $db = db_connect(); 
        helper('password_helper');

        $plain = (isset($_POST['password'])?$_POST['password']:$password);
        $hash = make_password($plain);

        // simpan $hash ke kolom password di tabel users
        $db->table('account')->insert([
            'user' => (isset($_POST['username'])?$_POST['username']:$username),
            'pass' => $hash,
            'type' => 'owner',
            'name' => $username,
        ]); 
        $idnya = $db->insertID();
        $db->table('account_company')->insert([
            'owner'     => $idnya,
            'owner_name'=> $username
        ]);
    }
    public function login(){
        $db = db_connect(); 
        $ret['status']  = false;
        helper('password_helper');
        $user = $db->table('account')->where('user', trim($_POST['username']))->select('id,pass,type,name')->get()->getRowArray();
        if (!$user) { $ret['message'] = 'username tidak ditemukan'; } else {
            $verify = verify_password(trim($_POST['password']), $user['pass']);
            if ($verify['ok']) {
                // success login
                $ret['message']     = 'login berhasil';
                if ($verify['rehash'] && $verify['new_hash']) {
                    // update DB with new hash (auto-upgrade)
                    $db->table('account')->where('id', $user['id'])->update(['pass' => $verify['new_hash']]);
                    $ret['message']     = 'login berhasil. ok';
                } 

                // 📌 Generate token login
                $token = bin2hex(random_bytes(32));

                $request = \Config\Services::request();
                $agent   = $request->getUserAgent();
                 
                $ip   = $this->request->getIPAddress();
                //$json = file_get_contents("http://ip-api.com/json/{$ip}");
                //$geo  = json_decode($json,true); 
                $json = null;
                $geo = [];
                // 📌 Update token ke database
                $db->table('account_login')->where(['user'=>$user['id'],'status'=>'active'])->set(['status'=>'prepare out','logout_when'=>date('Y-m-d H:i:s'),'logout_why'=>'someone loggedin'])->update();
                $today = date('Y-m-d');
                $expired = date('Y-m-d', strtotime("+".sys('session-expired')." days"));

                //$db->table('account_login')->insert(['user'=>$user['id'],'expired'=>$expired,'status'=>'active','created'=>date('Y-m-d H:i:s'),'token'=>$token,'ip'=>$request->getIPAddress(), 'browser'=>$agent->getBrowser(),'platform'=>$agent->getPlatform(),'device'=>($agent->isMobile() ? "Mobile" : "Desktop"),'agent_raw'=>$agent->getAgentString(),'apidata'=>$json,"country"=>$geo['country'],"countryCode"=>$geo['countryCode'],"region"=>$geo['region'],"regionName"=>$geo['regionName'],"city"=>$geo['city'],"zip"=>$geo['zip'],"lat"=>$geo['lat'],"lon"=>$geo['lon'],"timezone"=>$geo['timezone'],"isp"=>$geo['isp'],"org"=>$geo['org'],"as"=>$geo['as'],"query"=>$geo['query']]);
                $db->table('account_login')->insert(['user'=>$user['id'],'expired'=>$expired,'status'=>'active','created'=>date('Y-m-d H:i:s'),'token'=>$token,'ip'=>$request->getIPAddress(), 'browser'=>$agent->getBrowser(),'platform'=>$agent->getPlatform(),'device'=>($agent->isMobile() ? "Mobile" : "Desktop"),'agent_raw'=>$agent->getAgentString(),'apidata'=>$json]);

                $tokenid = $db->insertID();
                $db->table('account_login')->where(['user'=>$user['id'],'status'=>'prepare out'])->set(['logout_by'=>$tokenid,'status'=>'logged out'])->update();
                // 📌 Set session login

                $funcs = 'kasir_view';
                $aaa = $db->table('preference')->where(['user'=>$user['id'],'func'=>$funcs])->get()->getRowArray();
                if (isset($aaa['value'])) { 
                    $kv = $aaa['value']; 
                } else {
                    $default = 'list';
                    $db->table('preference')->insert(['user'=>$user['id'],'func'=>$funcs,'value'=>$default]);
                    $kv = $default;
                }

                if ($user['type']=='owner') {
                    $comp = $db->table('account_company')->where(['owner'=>$user['id']])->get()->getRowArray();
                } else {
                    $ap = $db->table('account_store_privilage ')->where(['account'=>$user['id']])->get()->getRowArray();
                    $storeid = $ap['store']??0;
                    $comp = $db->table('account_company')->where(['id'=>$ap['company']])->get()->getRowArray();
                }
                session()->regenerate();

                session()->set([
                    //'userid'      => $user['id'],
                    'userty'        => $user['type'],
                    'name'          => $user['name'],
                    'usertoken'     => $token,
                    'apptype'       => $comp['type']??'toko',
                    'company'       => $comp['id']??0,
                    'store'         => $storeid??0,
                    'logged_in'     => true,
                    'kasir_view'    => $kv,
                    'login_at' => time(),
                ]);
                $ret['status']      = true;
            } else {
                $ret['message'] = 'password yang anda masukan salah';
            }
        }
        echo json_encode($ret);
    }

    public function sample(){
        $user = model('UserModel')->getting([
            'where' => ['email' => 'test@mail.com']
        ]);
        $user = model('UserModel')->gettings([
            'where' => ['email' => 'test@mail.com']
        ]);
        $users = model('UserModel')->getpage([
            'like' => ['name' => 'john'],
            'order' => ['id' => 'DESC']
        ], 20);
        $total = model('UserModel')->counting([
            'where' => ['status' => 'active']
        ]);
        $totalAmount = model('UserModel')->summing('balance', [
            'where' => ['status' => 'paid']
        ]);


    }

}