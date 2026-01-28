<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Product extends BaseController{
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
        $page='product_detail';

        $data['table']      = 'dashboard';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk'; 
        $data['subsubmenu'] = 'produk_list';
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
        $data['subsubmenu'] = 'produk_list';
        $data['hmenu']      = 'produk';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }

    public function stok($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_stok';

        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = 'produk_stok';
        $data['hmenu']      = 'stok';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 

        $userid   = usertoken($_SESSION['usertoken']);
        $usertype = $_SESSION['userty'];
        $where = '';

        if ($usertype == 'owner') {
            $where = "
                AND p.company = (
                    SELECT id FROM account_company WHERE owner = {$userid} LIMIT 1
                )
            ";
        } else {
            $where = "
                AND p.store IN (
                    SELECT store 
                    FROM account_store_privilage 
                    WHERE account = {$userid}
                )
            ";
        }

        $rows = $db->query("
            SELECT
                pv.id,
                p.name AS product_name,
                pv.name AS variant_name,
                pv.sku,
                pv.stock,
                pv.minstock,
                pv.urgent
            FROM product_variant pv
            JOIN product p ON p.id = pv.product
            WHERE pv.status = 'active'
            {$where}
        ")->getResultArray();
        //$data['data']   = compact($rows);
        $data['data']   = $rows;
        echo backend($page,$data);
         
    }

    public function stokbahan($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_stok_bahan';

        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = 'produk_stok_bahan';
        $data['hmenu']      = 'stok_bahan';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        $data['data'] = $db->query("
                            SELECT
                                id,
                                name,
                                stock,
                                minstock,
                                unit,
                                urgent,
                                expired
                            FROM product_component
                            WHERE status = 'active'
                            ORDER BY 
                                (stock <= minstock) DESC,
                                stock ASC
                        ")->getResultArray();
        echo backend($page,$data);
         
    }

    public function harga($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_harga';

        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = 'produk_harga';
        $data['hmenu']      = 'produk';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }

    public function variant($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_variant';

        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = 'variant_list';
        $data['hmenu']      = 'variant';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }

    public function penjualan($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_penjualan';

        $data['id']         = $id;
        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = 'produk_penjualan';
        $data['hmenu']      = 'penjualan';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
    }

    public function pembelian($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_penjualan';

        $data['id']         = $id;
        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = 'produk_pembelian';
        $data['hmenu']      = 'pembelian';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
    }

    public function laporan($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_penjualan';

        $data['id']         = $id;
        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = 'produk_laporan';
        $data['hmenu']      = 'laporan';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
    }

    public function pengaturan($id=false){ 
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('toko'));}
        $data = array();
        $db = db_connect();  
        $page='product_penjualan';

        $data['id']         = $id;
        $data['table']      = 'account_store';
        $data['menu']       = 'data';
        $data['submenu']    = 'data_produk';
        $data['subsubmenu'] = 'produk_pengaturan';
        $data['hmenu']      = 'pengaturan';
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
        $data['subsubmenu'] = 'produk_list';
        $data['hmenu']      = 'produk';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
 
        echo backend($page,$data);
         
    }


}