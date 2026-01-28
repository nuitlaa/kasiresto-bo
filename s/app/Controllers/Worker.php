<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Worker extends BaseController{
    protected $userModel;
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global','text']);
        $this->userModel = new \App\Models\UserModel();
    }
    public function index(){ if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));} }
    public function detail($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='worker_detail';

        $data['table']      = 'dashboard';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_petugas';
        $data['subsubmenu'] = ''; 
        $data['hmenu']      = 'petugas';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo view('b/'.$page,$data);
         
    } 

    public function list($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='worker';

        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_petugas';
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'petugas'; 
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }

    public function tambah($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='worker_ae';
        if ($id!=false) {$data['data']   = $db->table('account a')->join('account_store_privilage b','b.account=a.id')->where(['a.id'=>$id])->select('a.*,b.privilage')->get()->getRowArray();}
        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_petugas';
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'petugas';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }


}