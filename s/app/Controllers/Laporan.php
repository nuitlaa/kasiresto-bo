<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Laporan extends BaseController{
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
    public function penjualan(){
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='laporan_penjualan';  
        $data['menu']       = 'laporan';
        $data['submenu']    = 'laporan_penjualan';
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'penjualan';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        $pref = $_SESSION['kasir_view'];
        //echo view($page.'/'.$pref,$data);

        $data['data'] = [
          'today' => [
            'omzet'      => $this->omzet($db,'today'),
            'transaksi'  => $this->jumlahTransaksi($db,'today'),
            'item'       => $this->itemTerjual($db,'today'),
            'retur'      => $this->totalRetur($db,'today'),
            'tukar'      => $this->totalTukar($db,'today'),
          ],
          'month' => [
            'omzet'     => $this->omzet($db,'month'),
            'transaksi' => $this->jumlahTransaksi($db,'month'),
            'item'      => $this->itemTerjual($db,'month'),
            'retur'      => $this->totalRetur($db,'month'),
            'tukar'      => $this->totalTukar($db,'month'),
          ],
          'year' => [
            'omzet'     => $this->omzet($db,'year'),
            'transaksi' => $this->jumlahTransaksi($db,'year'),
            'item'       => $this->itemTerjual($db,'year'),
            'retur'      => $this->totalRetur($db,'year'),
            'tukar'      => $this->totalTukar($db,'year'),
          ]
        ];

        echo backend($page,$data);
    }
    public function pembelanjaan(){
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='laporan_pembelanjaan';  
        $data['menu']       = 'laporan';
        $data['submenu']    = 'laporan_pembelanjaan';
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'pembelanjaan';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        $pref = $_SESSION['kasir_view'];
        //echo view($page.'/'.$pref,$data);

        $data['data'] = [
          'today' => [
            'omzet'      => $this->omzet($db,'today'),
            'transaksi'  => $this->jumlahTransaksi($db,'today'),
            'item'       => $this->itemTerjual($db,'today'),
            'retur'      => $this->totalRetur($db,'today'),
            'tukar'      => $this->totalTukar($db,'today'),
          ],
          'month' => [
            'omzet'     => $this->omzet($db,'month'),
            'transaksi' => $this->jumlahTransaksi($db,'month'),
            'item'      => $this->itemTerjual($db,'month'),
            'retur'      => $this->totalRetur($db,'month'),
            'tukar'      => $this->totalTukar($db,'month'),
          ],
          'year' => [
            'omzet'     => $this->omzet($db,'year'),
            'transaksi' => $this->jumlahTransaksi($db,'year'),
            'item'       => $this->itemTerjual($db,'year'),
            'retur'      => $this->totalRetur($db,'year'),
            'tukar'      => $this->totalTukar($db,'year'),
          ]
        ];

        echo backend($page,$data);
    }
    public function toko(){
        if (isset($_SESSION['userty'])&&$_SESSION['userty']!='') {} else {return redirect()->to(site_url('login'));}
        $data = array();
        $db = db_connect();  
        $page='laporan_toko';  
        $data['menu']       = 'laporan';
        $data['submenu']    = 'laporan_toko';
        $data['subsubmenu'] = '';
        $data['hmenu']      = 'toko';
        $data['filter']['bulan'] = true;
        $data['filter']['group'] = true; 
        $pref = $_SESSION['kasir_view'];
        //echo view($page.'/'.$pref,$data);

        $data['data'] = [
          'today' => [
            'omzet'      => $this->omzet($db,'today'),
            'transaksi'  => $this->jumlahTransaksi($db,'today'),
            'item'       => $this->itemTerjual($db,'today'),
            'retur'      => $this->totalRetur($db,'today'),
            'tukar'      => $this->totalTukar($db,'today'),
          ],
          'month' => [
            'omzet'     => $this->omzet($db,'month'),
            'transaksi' => $this->jumlahTransaksi($db,'month'),
            'item'      => $this->itemTerjual($db,'month'),
            'retur'      => $this->totalRetur($db,'month'),
            'tukar'      => $this->totalTukar($db,'month'),
          ],
          'year' => [
            'omzet'     => $this->omzet($db,'year'),
            'transaksi' => $this->jumlahTransaksi($db,'year'),
            'item'       => $this->itemTerjual($db,'year'),
            'retur'      => $this->totalRetur($db,'year'),
            'tukar'      => $this->totalTukar($db,'year'),
          ]
        ];

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
    public function timeFilter($mode, $field='created') {
        switch ($mode) {
            case 'today':
                return "DATE($field) = CURDATE()";
            case 'month':
                return "YEAR($field)=YEAR(CURDATE()) AND MONTH($field)=MONTH(CURDATE())";
            case 'year':
                return "YEAR($field)=YEAR(CURDATE())";
            default:
                return "1=1";
        }
    }


    public function totalTukar($db, $mode='today') {
        $where = $this->timeFilter($mode);
        return $db->query("
            SELECT IFNULL(SUM(qty),0) AS total
            FROM product_io
            WHERE func='tukar' AND $where
        ")->getRow()->total;
    }
    public function totalRetur($db, $mode='today') {
        $where = $this->timeFilter($mode);
        return $db->query("
            SELECT IFNULL(SUM(qty),0) AS total
            FROM product_io
            WHERE func='retur' AND $where
        ")->getRow()->total;
    }
    public function itemTerjual($db, $mode='today') {
        $where = $this->timeFilter($mode,'ord.created');
        return $db->query("
            SELECT IFNULL(SUM(o.qty),0) AS total
            FROM orders o
            JOIN `order` ord ON ord.id=o.order
            WHERE ord.status='finish' AND $where
        ")->getRow()->total;
    }
    public function jumlahTransaksi($db, $mode='today') {
        $where = $this->timeFilter($mode);
        return $db->query("
            SELECT COUNT(id) AS total
            FROM `order`
            WHERE status='finish' AND $where
        ")->getRow()->total;
    }
    public function omzet($db, $mode='today') {
        $where = $this->timeFilter($mode);
        return $db->query("
            SELECT IFNULL(SUM(nominal),0) AS total
            FROM `order`
            WHERE status='finish' AND $where
        ")->getRow()->total;
    }







}