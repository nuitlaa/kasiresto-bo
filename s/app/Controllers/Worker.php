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

    public function seed() {
        if (php_sapi_name() !== 'cli') {
            die("CLI only");
        }
        
        $db = \Config\Database::connect();
        helper(['global', 'text']);
        
        $company = $db->table('account_company')->get()->getRowArray();
        if (!$company) {
            echo "No company found.";
            return;
        }
        $companyId = $company['id'];
        
        $store = $db->table('account_store')->where('company', $companyId)->get()->getRowArray();
        $storeId = $store ? $store['id'] : 0;

        echo "Seeding 50 workers for Company ID: $companyId...\n";

        for ($i = 1; $i <= 50; $i++) {
            $username = 'petugas' . $i . '_' . time();
            $password = '123456';
            
            $save = [
                'user' => $username,
                'pass' => passcreate($password),
                'name' => 'Petugas Dummy ' . $i,
                'type' => 'employee',
                'status' => 'active',
                'created' => date('Y-m-d H:i:s'),
                'foto' => '',
            ];
            
            $db->table('account')->insert($save);
            $userId = $db->insertID();
            
            $savePriv = [
                'account' => $userId,
                'company' => $companyId,
                'store' => $storeId,
                'privilage' => 'employee',
                'created' => date('Y-m-d H:i:s')
            ];
            
            $db->table('account_store_privilage')->insert($savePriv);
        }
        echo "Done. 50 users created.\n";
    }

    public function seedweb() {
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $db = db_connect();
        helper(['global','text']);
        $userid     = usertoken($_SESSION['usertoken']);
        $companyId  = companyid($userid);
        if (!$companyId) { echo "Company not found."; return; }
        $store      = $db->table('account_store')->where('company', $companyId)->get()->getRowArray();
        $storeId    = $store ? $store['id'] : 0;
        for ($i = 1; $i <= 50; $i++) {
            $username = 'petugas' . $i . '_' . time();
            $password = '123456';
            $save = [
                'user' => $username,
                'pass' => passcreate($password),
                'name' => 'Petugas Dummy ' . $i,
                'type' => 'employee',
                'status' => 'active',
                'created' => date('Y-m-d H:i:s'),
                'foto' => '',
            ];
            $db->table('account')->insert($save);
            $userId = $db->insertID();
            $savePriv = [
                'account' => $userId,
                'company' => $companyId,
                'store' => $storeId,
                'privilage' => 'employee',
                'created' => date('Y-m-d H:i:s')
            ];
            $db->table('account_store_privilage')->insert($savePriv);
        }
        return redirect()->to(site_url('petugas/list?status=active'));
    }
}
