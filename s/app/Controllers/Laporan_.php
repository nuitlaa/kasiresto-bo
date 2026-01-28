<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Laporan_ extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
    }
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }
    public function pembelanjaan(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $summary = [
            'totalbelanja'      => $this->totalbelanja($db,$userid,$_POST['type'],$_POST['toko']),
            'transaksi'  => $this->TransaksiBelanja($db,$userid,$_POST['type'],$_POST['toko']),
            'item'       => $this->itemBelanja($db,$userid,$_POST['type'],$_POST['toko']), 
        ];
        // ===== CHART =====
        $toko = $_POST['toko'];
        if ($_POST['type'] === 'today') {
            $chart = $this->chartBelanjaHarian($db, $toko);
        } elseif ($_POST['type'] === 'year') {
            $chart = $this->chartBelanjaTahunan($db, $toko);
        } else {
            $chart = $this->chartBelanjaBulanan($db, $toko);
        }
        
        $ret['chart']       = $chart;
        $ret['summary']     = $summary;
        $ret['status']      = true;
        return $this->response->setJSON($ret);
    }
    public function penjualan(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $summary = [
            'omzet'      => $this->omzet($db,$userid,$_POST['type'],$_POST['toko']),
            'transaksi'  => $this->jumlahTransaksi($db,$userid,$_POST['type'],$_POST['toko']),
            'item'       => $this->itemTerjual($db,$userid,$_POST['type'],$_POST['toko']),
            'retur'      => $this->totalRetur($db,$userid,$_POST['type'],$_POST['toko']),
            'tukar'      => $this->totalTukar($db,$userid,$_POST['type'],$_POST['toko']),
        ];
        // ===== CHART =====
        $toko = $_POST['toko'];
        if ($_POST['type'] === 'today') {
            $chart = $this->chartHarian($db, $toko);
        } elseif ($_POST['type'] === 'year') {
            $chart = $this->chartTahunan($db, $toko);
        } else {
            $chart = $this->chartBulanan($db, $toko);
        }
        
        $ret['chart']       = $chart;
        $ret['summary']     = $summary;
        $ret['status']      = true;
        return $this->response->setJSON($ret);
    }

    public function chartHarian($db, $toko){
        $tahun  = (int)date('Y');
        $bulan  = (int)date('m');

        // jumlah hari dalam bulan ini
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            $rows = $db->query("
                SELECT 
                    DAY(`date`) AS hari,
                    SUM(nominal) AS total
                FROM `order`
                WHERE status = 'finish'
                  AND $thefield = ?
                  AND YEAR(`date`) = ?
                  AND MONTH(`date`) = ?
                GROUP BY hari
                ORDER BY hari
            ", [$theid, $tahun, $bulan])->getResultArray();
        } else {
            $rows = $db->query("
                SELECT 
                    DAY(`date`) AS hari,
                    SUM(nominal) AS total
                FROM `order`
                WHERE status = 'finish'
                  AND store = ?
                  AND YEAR(`date`) = ?
                  AND MONTH(`date`) = ?
                GROUP BY hari
                ORDER BY hari
            ", [$toko, $tahun, $bulan])->getResultArray();
        }

        // X-axis: 1 → jumlah hari
        $categories = [];
        $series = [];

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $categories[] = (string)$d;
            $series[$d] = 0; // default
        }

        foreach ($rows as $r) {
            $day = (int)$r['hari'];
            if (isset($series[$day])) {
                $series[$day] = (int)$r['total'];
            }
        }

        // reset index supaya cocok ApexCharts
        $series = array_values($series);

        return [
            'categories' => $categories,
            'revenue'    => $series
        ];
    }
    public function chartBulanan($db, $toko){

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            $rows = $db->query("
                SELECT 
                    MONTH(`date`) AS bulan,
                    SUM(nominal) AS total
                FROM `order`
                WHERE status = 'finish'
                  AND $thefield = ?
                  AND YEAR(`date`) = YEAR(CURDATE())
                GROUP BY bulan
                ORDER BY bulan
            ", [$theid])->getResultArray();
        } else {
            $rows = $db->query("
                SELECT 
                    MONTH(`date`) AS bulan,
                    SUM(nominal) AS total
                FROM `order`
                WHERE status = 'finish'
                  AND store = ?
                  AND YEAR(`date`) = YEAR(CURDATE())
                GROUP BY bulan
                ORDER BY bulan
            ", [$toko])->getResultArray();
        }

        // Label tetap Jan–Des (biar grafik konsisten)
        $categories = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // Isi default 0 (grafik ringan & stabil)
        $series = array_fill(0, 12, 0);

        foreach ($rows as $r) {
            $index = (int)$r['bulan'] - 1;
            $series[$index] = (int)$r['total'];
        }

        return [
            'categories' => $categories,
            'revenue'    => $series
        ];
    }
    public function chartTahunan($db, $toko){
        $tahunSekarang = (int)date('Y');
        $tahunAwal = $tahunSekarang - 5;

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            $rows = $db->query("
                SELECT 
                    YEAR(`date`) AS tahun,
                    SUM(nominal) AS total
                FROM `order`
                WHERE status = 'finish'
                  AND $thefield = ?
                  AND YEAR(`date`) BETWEEN ? AND ?
                GROUP BY tahun
                ORDER BY tahun
            ", [$theid, $tahunAwal, $tahunSekarang])->getResultArray();
        } else {
            $rows = $db->query("
                SELECT 
                    YEAR(`date`) AS tahun,
                    SUM(nominal) AS total
                FROM `order`
                WHERE status = 'finish'
                  AND store = ?
                  AND YEAR(`date`) BETWEEN ? AND ?
                GROUP BY tahun
                ORDER BY tahun
            ", [$toko, $tahunAwal, $tahunSekarang])->getResultArray();
        }
        // X-axis: tahunAwal → tahunSekarang
        $categories = [];
        $series = [];

        for ($y = $tahunAwal; $y <= $tahunSekarang; $y++) {
            $categories[] = (string)$y;
            $series[$y] = 0; // default 0
        }

        // Isi data dari DB
        foreach ($rows as $r) {
            $year = (int)$r['tahun'];
            if (isset($series[$year])) {
                $series[$year] = (int)$r['total'];
            }
        }

        // reset index (penting buat ApexCharts)
        $series = array_values($series);

        return [
            'categories' => $categories,
            'revenue'    => $series
        ];
    }

    public function chartBelanjaHarian($db, $toko){
        $tahun  = (int)date('Y');
        $bulan  = (int)date('m');

        // jumlah hari dalam bulan ini
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            $rows = $db->query("
                SELECT 
                    DAY(`date`) AS hari,
                    SUM(nominal) AS total
                FROM `purchase`
                WHERE status = 'finish'
                  AND $thefield = ?
                  AND YEAR(`date`) = ?
                  AND MONTH(`date`) = ?
                GROUP BY hari
                ORDER BY hari
            ", [$theid, $tahun, $bulan])->getResultArray();
        } else {
            $rows = $db->query("
                SELECT 
                    DAY(`date`) AS hari,
                    SUM(nominal) AS total
                FROM `purchase`
                WHERE status = 'finish'
                  AND store = ?
                  AND YEAR(`date`) = ?
                  AND MONTH(`date`) = ?
                GROUP BY hari
                ORDER BY hari
            ", [$toko, $tahun, $bulan])->getResultArray();
        }

        // X-axis: 1 → jumlah hari
        $categories = [];
        $series = [];

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $categories[] = (string)$d;
            $series[$d] = 0; // default
        }

        foreach ($rows as $r) {
            $day = (int)$r['hari'];
            if (isset($series[$day])) {
                $series[$day] = (int)$r['total'];
            }
        }

        // reset index supaya cocok ApexCharts
        $series = array_values($series);

        return [
            'categories' => $categories,
            'revenue'    => $series
        ];
    }
    public function chartBelanjaBulanan($db, $toko){

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            $rows = $db->query("
                SELECT 
                    MONTH(`date`) AS bulan,
                    SUM(nominal) AS total
                FROM `purchase`
                WHERE status = 'finish'
                  AND $thefield = ?
                  AND YEAR(`date`) = YEAR(CURDATE())
                GROUP BY bulan
                ORDER BY bulan
            ", [$theid])->getResultArray();
        } else {
            $rows = $db->query("
                SELECT 
                    MONTH(`date`) AS bulan,
                    SUM(nominal) AS total
                FROM `purchase`
                WHERE status = 'finish'
                  AND store = ?
                  AND YEAR(`date`) = YEAR(CURDATE())
                GROUP BY bulan
                ORDER BY bulan
            ", [$toko])->getResultArray();
        }

        // Label tetap Jan–Des (biar grafik konsisten)
        $categories = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        // Isi default 0 (grafik ringan & stabil)
        $series = array_fill(0, 12, 0);

        foreach ($rows as $r) {
            $index = (int)$r['bulan'] - 1;
            $series[$index] = (int)$r['total'];
        }

        return [
            'categories' => $categories,
            'revenue'    => $series
        ];
    }
    public function chartBelanjaTahunan($db, $toko){
        $tahunSekarang = (int)date('Y');
        $tahunAwal = $tahunSekarang - 5;

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            $rows = $db->query("
                SELECT 
                    YEAR(`date`) AS tahun,
                    SUM(nominal) AS total
                FROM `purchase`
                WHERE status = 'finish'
                  AND $thefield = ?
                  AND YEAR(`date`) BETWEEN ? AND ?
                GROUP BY tahun
                ORDER BY tahun
            ", [$theid, $tahunAwal, $tahunSekarang])->getResultArray();
        } else {
            $rows = $db->query("
                SELECT 
                    YEAR(`date`) AS tahun,
                    SUM(nominal) AS total
                FROM `purchase`
                WHERE status = 'finish'
                  AND store = ?
                  AND YEAR(`date`) BETWEEN ? AND ?
                GROUP BY tahun
                ORDER BY tahun
            ", [$toko, $tahunAwal, $tahunSekarang])->getResultArray();
        }
        // X-axis: tahunAwal → tahunSekarang
        $categories = [];
        $series = [];

        for ($y = $tahunAwal; $y <= $tahunSekarang; $y++) {
            $categories[] = (string)$y;
            $series[$y] = 0; // default 0
        }

        // Isi data dari DB
        foreach ($rows as $r) {
            $year = (int)$r['tahun'];
            if (isset($series[$year])) {
                $series[$year] = (int)$r['total'];
            }
        }

        // reset index (penting buat ApexCharts)
        $series = array_values($series);

        return [
            'categories' => $categories,
            'revenue'    => $series
        ];
    }





    public function timeFilter($mode, $field='created', $toko='semua') {
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
    public function totalTukar($db, $userid, $mode='today', $toko='semua') {
        $where = $this->timeFilter($mode);
        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            return $db->query("SELECT IFNULL(SUM(qty),0) AS total FROM product_io WHERE func='ditukar' AND $thefield=$theid AND $where")->getRow()->total;
        } else {
            return $db->query("SELECT IFNULL(SUM(qty),0) AS total FROM product_io WHERE func='ditukar' AND store=$toko AND $where")->getRow()->total;
        }
    }
    public function totalRetur($db, $userid, $mode='today', $toko='semua') {
        $where = $this->timeFilter($mode);
        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            return $db->query("SELECT IFNULL(SUM(qty),0) AS total FROM product_io WHERE func='retur' AND $thefield=$theid AND $where")->getRow()->total;
        } else {
            return $db->query("SELECT IFNULL(SUM(qty),0) AS total FROM product_io WHERE func='retur' AND store=$toko AND $where")->getRow()->total;
        } 
    }
    public function itemTerjual($db, $userid, $mode='today', $toko='semua') {
        $where = $this->timeFilter($mode,'ord.created');

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            return $db->query("SELECT IFNULL(SUM(o.qty),0) AS total FROM orders o JOIN `order` ord ON ord.id=o.order WHERE ord.status='finish' AND ord.$thefield=$theid AND $where")->getRow()->total;
        } else {
            return $db->query("SELECT IFNULL(SUM(o.qty),0) AS total FROM orders o JOIN `order` ord ON ord.id=o.order WHERE ord.status='finish' AND ord.store=$toko AND $where")->getRow()->total;
        } 
    }
    public function jumlahTransaksi($db, $userid, $mode='today', $toko='semua') {
        $where = $this->timeFilter($mode);

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            return $db->query("SELECT COUNT(id) AS total FROM `order` WHERE status='finish' AND $thefield=$theid AND $where")->getRow()->total;
        } else {
            return $db->query("SELECT COUNT(id) AS total FROM `order` WHERE status='finish' AND store=$toko AND $where")->getRow()->total;
        } 
    }
    public function omzet($db, $userid, $mode='today', $toko='semua') {
        $where = $this->timeFilter($mode);

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            return $db->query("SELECT IFNULL(SUM(nominal),0) AS total FROM `order` WHERE status='finish' AND $thefield=$theid AND $where")->getRow()->total;
        } else {
            return $db->query("SELECT IFNULL(SUM(nominal),0) AS total FROM `order` WHERE status='finish' AND store=$toko AND $where")->getRow()->total;
        } 
    }
    public function totalbelanja($db, $userid, $mode='today', $toko='semua') {
        $where = $this->timeFilter($mode);

        if ($toko!=0 && $toko!='semua' && $toko!='0') {
            return $db->query("SELECT IFNULL(SUM(nominal),0) AS total FROM `purchase` WHERE status='finish' AND store=$toko AND $where")->getRow()->total;
        } else {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            return $db->query("SELECT IFNULL(SUM(nominal),0) AS total FROM `purchase` WHERE status='finish' AND $thefield=$theid AND $where")->getRow()->total;
        } 
    }
    public function itemBelanja($db, $userid, $mode='today', $toko='semua') {
        $where = $this->timeFilter($mode,'ord.created');

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            return $db->query("SELECT IFNULL(SUM(o.qty),0) AS total FROM purchases o JOIN `purchase` ord ON ord.id=o.purchaseid WHERE ord.status='finish' AND ord.$thefield=$theid AND $where")->getRow()->total;
        } else {
            return $db->query("SELECT IFNULL(SUM(o.qty),0) AS total FROM purchases o JOIN `purchase` ord ON ord.id=o.purchaseid WHERE ord.status='finish' AND ord.store=$toko AND $where")->getRow()->total;
        } 
    }
    public function TransaksiBelanja($db, $userid, $mode='today', $toko='semua') {
        $where = $this->timeFilter($mode);

        if ($toko==0 || $toko=='semua' || $toko=='0') {
            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'store';
            }
            return $db->query("SELECT COUNT(id) AS total FROM `purchase` WHERE status='finish' AND $thefield=$theid AND $where")->getRow()->total;
        } else {
            return $db->query("SELECT COUNT(id) AS total FROM `purchase` WHERE status='finish' AND store=$toko AND $where")->getRow()->total;
        } 
    }


    public function getPenjualan(){
        $db = db_connect();

        $limit = 20;
        $page  = max(1, (int)$this->request->getPost('page'));
        $offset = ($page - 1) * $limit;

        $builder = $db->table('order o')->join('account_customer c','c.id=o.customer','left');
        $builder->select('
                            o.id,
                            o.invoice,
                            o.date,
                            o.customer,
                            o.nominal AS total,
                            o.lunas,
                            COALESCE(c.name, "umum") AS namacustomer,
                            COUNT(oi.id) AS item
                        ');
        $builder->join('orders oi','oi.order = o.id','left');
        $builder->groupBy('o.id');
        $builder->where('o.status', 'finish');

        if ($toko = $this->request->getPost('toko')) {
            $builder->where('o.store', $toko);
        }

        if ($status = $this->request->getPost('status')) {
            $builder->where('o.lunas', $status === 'lunas' ? 1 : 0);
        }

        if ($search = $this->request->getPost('search')) {
            $builder->like('o.invoice', $search);
        }

        if ($date = $this->request->getPost('date')) {
            [$start, $end] = explode(' - ', $date);
            $builder->where('o.date >=', $start);
            $builder->where('o.date <=', $end);
        }

        $rows = $builder
            ->orderBy('o.date', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
 

        return $this->response->setJSON([
            'data' => $rows
        ]);
    }

    public function getPembelian(){
        $db = db_connect();

        $limit = 20;
        $page  = max(1, (int)$this->request->getPost('page'));
        $offset = ($page - 1) * $limit;

        $builder = $db->table('purchase o')->join('account_supplier c','c.id=o.supplier','left');
        $builder->select('
                            o.id,
                            o.invoice,
                            o.date,
                            o.supplier,
                            o.nominal AS total,
                            o.lunas,
                            COALESCE(c.name, "umum") AS namacustomer,
                            COUNT(oi.id) AS item
                        ');
        $builder->join('purchases oi','oi.purchaseid = o.id','left');
        $builder->groupBy('o.id');
        $builder->where('o.status', 'finish');
        $tokonya = $this->request->getPost('toko');
        if (!empty($tokonya) && $tokonya!='semua' && $tokonya!='' && $tokonya!=0) {
            $builder->where('o.store', $toko);
        } else {

            $userid = usertoken($_SESSION['usertoken']);
            if ($_SESSION['userty']=='owner') {
                $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
                $thefield = 'o.company';
            } else {
                $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
                $thefield = 'o.store';
            }
            $builder->where($thefield,$theid);
        }

        if ($status = $this->request->getPost('status')) {
            $builder->where('o.lunas', $status === 'lunas' ? 1 : 0);
        }

        if ($search = $this->request->getPost('search')) {
            $builder->like('o.invoice', $search);
        }

        if ($date = $this->request->getPost('date')) {
            [$start, $end] = explode(' - ', $date);
            $builder->where('o.date >=', $start);
            $builder->where('o.date <=', $end);
        }

        $rows = $builder
            ->orderBy('o.date', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
 

        return $this->response->setJSON([
            'data' => $rows
        ]);
    }

    public function detail($id){
         $db = db_connect();

        // ambil header order
        $order = $db->table('order o')->join('account_customer b','b.id=o.customer', 'left')->select('o.*, COALESCE(b.name, "umum") AS namacustomer')
            ->where('o.id', $id)
            ->get()
            ->getRow();

        if (!$order) {
            return $this->response->setJSON([
                'html' => '<div class="text-danger">Data tidak ditemukan</div>'
            ]);
        }

        // ambil item
        $items = $db->table('orders o')
            ->select('
                o.qty,
                o.nominal,
                u.unit_name AS namaunit,
                (o.qty * o.nominal) AS subtotal,
                p.name AS product_name,
                v.name AS variant_name
            ')
            ->join('product_units u', 'u.id = o.unit', 'left')
            ->join('product p', 'p.id = o.product', 'left')
            ->join('product_variant v', 'v.id = o.variant', 'left')
            ->where('o.order', $id)
            ->get()
            ->getResult();

        $html = view('b/mod/lap_detail_transaksi', [
            'order' => $order,
            'items' => $items
        ]);

        return $this->response->setJSON([
            'html' => $html
        ]);
    }

    public function detailpembelian($id){
         $db = db_connect();

        // ambil header order
        $order = $db->table('purchase o')->join('account_supplier b','b.id=o.supplier', 'left')->select('o.*, COALESCE(b.name, "umum") AS namasupplier')
            ->where('o.id', $id)
            ->get()
            ->getRow();

        if (!$order) {
            return $this->response->setJSON([
                'html' => '<div class="text-danger">Data tidak ditemukan</div>'
            ]);
        }

        // ambil item
        $items = $db->table('purchases o')
            ->select('
                o.qty,
                o.nominal,
                u.unit_name AS namaunit,
                (o.qty * o.nominal) AS subtotal,
                p.name AS product_name,
                v.name AS variant_name
            ')
            ->join('product_units u', 'u.id = o.unit', 'left')
            ->join('product p', 'p.id = o.product', 'left')
            ->join('product_variant v', 'v.id = o.variant', 'left')
            ->where('o.purchaseid', $id)
            ->get()
            ->getResult();

        $html = view('b/mod/lap_detail_pembelian', [
            'order' => $order,
            'items' => $items
        ]);

        return $this->response->setJSON([
            'html' => $html
        ]);

    }


}