<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Belanja extends BaseController{
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
        $page='transaksi_kasir'; 

        $data['pagetitle']  = '💳 Transaksi Kasir';
        $data['table']      = 'account_store';
        $data['menu']       = 'transaksi';
        $data['submenu']    = 'transaksi_kasir';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        $pref = $_SESSION['kasir_view'];
        //echo view($page.'/'.$pref,$data);
        echo backend($page,$data);
    }
    public function belanja(){
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='transaksi_belanja'; 
        $data['pagetitle']  = '💳 Belanja Barang';

        $data['table']      = 'account_store';
        $data['menu']       = 'transaksi';
        $data['submenu']    = 'transaksi_belanja';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        $pref = $_SESSION['kasir_view'];
        //echo view($page.'/'.$pref,$data);
        echo backend($page,$data);
    }
    public function tukar(){
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='transaksi_tukar'; 
        $data['pagetitle']  = '💳 Tukar Produk';

        $data['table']      = 'account_store';
        $data['menu']       = 'transaksi';
        $data['submenu']    = 'transaksi_tukar';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        $pref = $_SESSION['kasir_view'];
        //echo view($page.'/'.$pref,$data);
        echo backend($page,$data);
    }
    public function retur(){
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='transaksi_retur'; 

        $data['pagetitle']  = '💳 Retur Produk';
        $data['table']      = 'account_store';
        $data['menu']       = 'transaksi';
        $data['submenu']    = 'transaksi_retur';
        $data['subsubmenu'] = '';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        $pref = $_SESSION['kasir_view'];
        //echo view($page.'/'.$pref,$data);
        echo backend($page,$data);
    }
    public function grid(){
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
        $pref = $_SESSION['kasir_view'];
        echo view($page.'/grid',$data);
    }

    public function befores(){
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
        $pref = $_SESSION['kasir_view'];
        echo backend($page,$data);
    }
    public function detail($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='product_detail';

        $data['table']      = 'dashboard';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk'; 
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'produk';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo view('b/'.$page,$data);
         
    } 

    public function list($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product';

        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'produk';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }

    public function tambah($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_ae';
        if ($id!=false) {$data['data']   = $db->table('account_store')->where(['id'=>$id])->get()->getRowArray();}
        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'produk';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }


}