<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class LaporanToko_ extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
    }
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }
    public function chart(){
        $type = $this->request->getGet('type');
        return $this->response->setJSON(
            match($type) {
                'today'   => $this->chartHarian(),
                'year'  => $this->chartTahunan(),
                default   => $this->chartBulanan(),
            }
        );
    }
    private function chartBulanan(){
        $db = db_connect();
        $year = date('Y');
        $userid = usertoken($_SESSION['usertoken']);
        if ($_SESSION['userty']=='owner') {
            $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
            $thefield = 'company';
        } else {
            $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
            $thefield = 'store';
        }
        $stores = $db->table('account_store')->where($thefield, $theid)->get()->getResultArray();
        $categories = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $series = [];

        foreach ($stores as $s) {

            $data = array_fill(0, 12, 0);

            $rows = $db->query("
                SELECT MONTH(`date`) bulan, SUM(nominal) total
                FROM `order`
                WHERE status='finish'
                  AND store=?
                  AND YEAR(`date`)=?
                GROUP BY bulan
            ", [$s['id'], $year])->getResultArray();

            foreach ($rows as $r) {
                $data[$r['bulan'] - 1] = (int)$r['total'];
            }

            $series[] = [
                'name' => $s['name'],
                'data' => $data
            ];
        }

        return compact('categories','series');
    }
    private function chartHarian(){
        $db = db_connect();

        $year  = date('Y');
        $month = date('m');
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // kategori: 1 - jumlah hari
        $categories = range(1, $daysInMonth);

        $userid = usertoken($_SESSION['usertoken']);
        if ($_SESSION['userty']=='owner') {
            $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
            $thefield = 'company';
        } else {
            $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
            $thefield = 'store';
        }
        // ambil toko milik company
        $stores = $db->table('account_store')
            ->where($thefield, $theid)
            ->get()->getResultArray();

        $series = [];

        foreach ($stores as $s) {

            // default 0
            $data = array_fill(0, $daysInMonth, 0);

            $rows = $db->query("
                SELECT 
                    DAY(`date`) AS hari,
                    SUM(nominal) AS total
                FROM `order`
                WHERE status='finish'
                  AND store=?
                  AND YEAR(`date`)=?
                  AND MONTH(`date`)=?
                GROUP BY hari
                ORDER BY hari
            ", [$s['id'], $year, $month])->getResultArray();

            foreach ($rows as $r) {
                $index = (int)$r['hari'] - 1;
                $data[$index] = (int)$r['total'];
            }

            $series[] = [
                'name' => $s['name'],
                'data' => $data
            ];
        }

        return [
            'categories' => $categories,
            'series'    => $series
        ];
    }
    private function chartTahunan(){
        $db = db_connect();

        $currentYear = (int)date('Y');
        $startYear   = $currentYear - 4;

        // kategori tahun
        $categories = range($startYear, $currentYear);

        $userid = usertoken($_SESSION['usertoken']);
        if ($_SESSION['userty']=='owner') {
            $theid = $db->table('account_company')->where(['owner'=>$userid])->get()->getRow()->id??0;
            $thefield = 'company';
        } else {
            $theid = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRow()->store??0;
            $thefield = 'store';
        }
        $stores = $db->table('account_store')
            ->where($thefield, $theid)
            ->get()->getResultArray();

        $series = [];

        foreach ($stores as $s) {

            $data = array_fill(0, count($categories), 0);

            $rows = $db->query("
                SELECT 
                    YEAR(`date`) AS tahun,
                    SUM(nominal) AS total
                FROM `order`
                WHERE status='finish'
                  AND store=?
                  AND YEAR(`date`) BETWEEN ? AND ?
                GROUP BY tahun
                ORDER BY tahun
            ", [$s['id'], $startYear, $currentYear])->getResultArray();

            foreach ($rows as $r) {
                $index = array_search((int)$r['tahun'], $categories);
                if ($index !== false) {
                    $data[$index] = (int)$r['total'];
                }
            }

            $series[] = [
                'name' => $s['name'],
                'data' => $data
            ];
        }

        return [
            'categories' => $categories,
            'series'    => $series
        ];
    }




    public function summary(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $db->table('order')
              ->select("
                COUNT(*) AS transaksi,
                SUM(nominal) AS omzet,
                SUM(bayar) AS bayar,
                SUM(CASE WHEN lunas = 0 THEN nominal ELSE 0 END) AS piutang,
                SUM(orders.qty) AS item
              ")
              ->join('orders', 'orders.order = order.id', 'left')
              ->where('company', $companyId)
              ->where('date >=', $start)
              ->where('date <=', $end)
              ->get()->getRow();

        return $this->response->setJSON($ret);
    }
    public function per(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $db->table('order')
              ->select("
                store,
                COUNT(*) AS transaksi,
                SUM(nominal) AS omzet,
                SUM(CASE WHEN lunas = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100 AS persen_lunas,
                SUM(CASE WHEN lunas = 0 THEN nominal ELSE 0 END) AS piutang
              ")
              ->where('company', $companyId)
              ->groupBy('store')
              ->get()->getResult();

        return $this->response->setJSON($ret);
    }

    public function chart_harian(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $db->table('order')
          ->select("DATE(date) AS tanggal, SUM(nominal) AS omzet")
          ->where('company', $companyId)
          ->groupBy('DATE(date)')
          ->orderBy('tanggal')
          ->get()->getResult();

        return $this->response->setJSON($ret);
    }

    public function chart_per(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);

        return $this->response->setJSON($ret);

    }

    public function chart_pembayaran(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);

        return $this->response->setJSON($ret);

    }

    public function chart_jam(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $db->table('order')
          ->select("HOUR(created) AS jam, COUNT(*) AS total")
          ->where('company', $companyId)
          ->groupBy('jam')
          ->get()->getResult();

        return $this->response->setJSON($ret);

    }

    public function insight(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);

        return $this->response->setJSON($ret);

    }

    public function transaksi(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);

        return $this->response->setJSON($ret);

    }

}