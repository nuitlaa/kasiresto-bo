<?php
namespace App\Controllers;
use App\Helpers\global_helper;
use App\Libraries\Faskes_lib;
date_default_timezone_set("Asia/Bangkok");
class Admin extends BaseController{
    protected $userModel;
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global','text']);
        $this->userModel = new \App\Models\UserModel();
    }
    public function index(){
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='admin';

        $data['table']      = 'dashboard';
        $data['menu']       = 'dashboard';
        $data['submenu']    = '';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo backend($page,$data);
    }

    public function dashboard($func=false, $id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {if($_SESSION['userty']=='employee'){return redirect()->to(site_url('kasir'));}} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='dashboard';
        

        $data['table']      = 'dashboard';
        $data['menu']       = 'dashboard';
        $data['submenu']    = '';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo view('b/'.$page,$data);
         
    } 

    public function kasir($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='kasir'; 

        $data['table']      = 'account_store';
        $data['menu']       = 'kasir';
        $data['submenu']    = '';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }

    public function beranda($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='beranda_'.$_SESSION['userty']; 
        $data['table']      = 'account_store';
        $data['menu']       = 'beranda';
        $data['submenu']    = '';
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'beranda';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        echo backend($page,$data);
    }

    public function pengaturan($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='pengaturan_'.$_SESSION['userty']; 

        $data['table']      = 'account_store';
        $data['menu']       = 'pengaturan';
        $data['submenu']    = '';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        echo backend($page,$data);
    }

    public function store($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  

        $data['table']      = 'account_store';
        $data['menu']       = 'store';
        $data['submenu']    = '';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 

        if ($id==false) { $page='store'; } else {
            $page='store_detail';
        }
        echo backend($page,$data);
         
    }
    public function login(){
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {return redirect()->to(site_url());} else {}
        $data = array();
        echo view('b/login',$data);
        //echo view('b/login',$data);
    }
    public function logout(){
        session()->destroy(); // hapus semua session
        return redirect()->to('/'); // arahkan ke login
    }

    public function up(){
        $file = $this->request->getFile('photo');
        $user_id = session('user_id');

        $path = safe_upload($file, $user_id);


        if ($path) {
            echo "File disimpan di: " . $path;
        } else {
            echo "Upload gagal";
        }

    }
    public function upmulti(){
        $files = $this->request->getFiles()['photos'];
        $user_id = session('user_id');

        $paths = safe_multi_upload($files, $user_id);

    }

    public function safedel(){
        safe_delete("file/user/xxxx/2025/11/namafile.png");

    }

}