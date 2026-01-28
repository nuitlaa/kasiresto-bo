<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Pengaturan extends BaseController{
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
        if ($_SESSION['userty']=='owner') {
            $page='pengaturan_perusahaan';
        } else {
            $page='pengaturan_toko';
        }

        $data['table']      = 'dashboard';
        $data['menu']       = 'pengaturan';
        $data['submenu']    = 'pengaturan_perusahaan'; 
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'perusahaan';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo backend(''.$page,$data);
         
    } 
    public function perusahaan($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='pengaturan_perusahaan';

        $data['table']      = 'dashboard';
        $data['menu']       = 'pengaturan';
        $data['submenu']    = 'pengaturan_perusahaan'; 
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'perusahaan';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo backend(''.$page,$data);
         
    } 
    public function jenis_konsumen($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  

        $page = $id==false?'pengaturan_jenis_konsumen':'pengaturan_jenis_konsumen_ae';
        $data['id']         = preg_replace('/\D/', '', $id);
        $data['table']      = 'dashboard';
        $data['menu']       = 'pengaturan';
        $data['submenu']    = 'pengaturan_jenis_konsumen'; 
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'jenis_konsumen';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo backend(''.$page,$data);
         
    } 
    public function kategori($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='pengaturan_kategori';

        $page = $id==false?'pengaturan_kategori':'pengaturan_kategori_ae';
        $data['id']         = preg_replace('/\D/', '', $id);
        $data['table']      = 'dashboard';
        $data['menu']       = 'pengaturan';
        $data['submenu']    = 'pengaturan_kategori'; 
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'kategori';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo backend(''.$page,$data);
         
    } 
    public function meja($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  

        $page = $id==false?'pengaturan_meja':'pengaturan_meja_ae';
        $data['id']         = preg_replace('/\D/', '', $id);
        $data['table']      = 'dashboard';
        $data['menu']       = 'pengaturan';
        $data['submenu']    = 'pengaturan_meja'; 
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'meja';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo backend(''.$page,$data);
         
    } 
    public function bahan($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  

        $page = $id==false?'pengaturan_bahan':'pengaturan_bahan_ae';
        $data['id']         = preg_replace('/\D/', '', $id);
        $data['table']      = 'dashboard';
        $data['menu']       = 'pengaturan';
        $data['submenu']    = 'pengaturan_bahan'; 
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'bahan';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true;  
        
        echo backend(''.$page,$data);
         
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