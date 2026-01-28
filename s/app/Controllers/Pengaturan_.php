<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Pengaturan_ extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
    }
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }

    public function simpan($what='owner'){ 
        $db = db_connect();
        $ret['status'] = false; 

        if ($what=='owner') {
            $table  = 'account';
            $saver  = $_POST['owner'];
            if (isset($_POST['pass'])&&$_POST['pass']!='') { 
                $saver['pass'] = make_password($_POST['pass']);
            }
        } else {
            $table  = 'account_company';
            $saver  = $_POST['company'];
        }
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK && $_FILES['foto']['size'] > 0 ) {
            $foto = uploadfile(($what=='owner'?'user':'toko'), 'foto');
            if ($foto !== false) {$saver['foto'] = $foto;}
        }
        $product['publish']     = (isset($product['publish'])?1:0);
        if (isset($_POST['id'])&&$_POST['id']!=0&&$_POST['id']!='') {
            $idnya = dekripsi($_POST['id']);
            $db->table($table)->where(['id'=>$idnya])->set($saver)->update();
            $ret['idnya']       = $idnya;
            $ret['status']      = true;
            $ret['message']     = 'Data '.$what.' telah disimpan';
        } else {
            $db->table($table)->insert($saver);
            $idproduct = $db->insertID();
            $ret['idnya']       = $idproduct;
            $ret['status']      = true;
            $ret['message']     = $what.' baru telah ditambahkan';
        }
        echo json_encode($ret);
    }


    public function jenis_konsumen(){
        $db = db_connect();
        $ret['status'] = false; 
        $saver  = $_POST['saver'];
        $table = 'account_type';
        $what = 'Jenis Konsumen';
        $comp = $db->table('account_store')->where(['id'=>$saver['store']])->select('company,owner')->get()->getRowArray();
        if (isset($comp['company'])) {
            $saver['company']   = $comp['company'];
            $saver['owner']     = $comp['owner'];
        }
        if (isset($_POST['default'])&&$_POST['default']==1) {
            $saver['default']   = 1;
            $db->table($table)->where(['company'=>$saver['company'],'store'=>$saver['store']])->set(['default'=>0])->update();
        } else {
            $saver['default']   = 0;
        }
        if (isset($_POST['id'])&&$_POST['id']!=0&&$_POST['id']!='') {
            $idnya = dekripsi($_POST['id']);
            $db->table($table)->where(['id'=>$idnya])->set($saver)->update();
            $ret['idnya']       = $idnya;
            $ret['status']      = true;
            $ret['message']     = 'Data '.$what.' telah disimpan';
        } else {
            $db->table($table)->insert($saver);
            $idproduct = $db->insertID();
            $ret['idnya']       = $idproduct;
            $ret['status']      = true;
            $ret['message']     = $what.' baru telah ditambahkan';
        }
        echo json_encode($ret);
    }

    public function kategori(){
        $db             = db_connect();
        $userid         = usertoken($_SESSION['usertoken']);
        $ret['status']  = false; 
        $saver          = $_POST['saver'];
        $table          = 'product_category';
        $what           = 'Kategori';
        
        if (isset($_POST['status'])&&$_POST['status']==1) {
            $saver['status']   = 'active'; 
        } else {
            $saver['status']   = 'non active';
        }
        if (isset($_POST['id'])&&$_POST['id']!=0&&$_POST['id']!='') {
            $idnya = dekripsi($_POST['id']);
            $db->table($table)->where(['id'=>$idnya])->set($saver)->update();
            $ret['idnya']       = $idnya;
            $ret['status']      = true;
            $ret['message']     = 'Data '.$what.' telah disimpan';
        } else {
            $db->table($table)->insert($saver);
            $idproduct = $db->insertID();
            $ret['idnya']       = $idproduct;
            $ret['status']      = true;
            $ret['message']     = $what.' baru telah ditambahkan';
        }
        echo json_encode($ret);
    }


    public function meja($func=false,$id=''){
        $db = db_connect();
        $ret['status'] = false; 
        $table = 'account_store_table';
        if ($func==false) {
            $saver  = $_POST['saver']??[];
            $what = 'meja';
            $comp = $db->table('account_store')->where(['id'=>$saver['store']])->select('company,owner')->get()->getRowArray();
            if (isset($comp['company'])) {
                $saver['company']   = $comp['company'];
            }
            $ret['func']    = $func;
            if (isset($_POST['id'])&&$_POST['id']!=0&&$_POST['id']!='') {
                $idnya = dekripsi($_POST['id']);
                $db->table($table)->where(['id'=>$idnya])->set($saver)->update();
                $ret['idnya']       = $idnya;
                $ret['status']      = true;
                $ret['message']     = 'Data '.$what.' telah disimpan';
            } else {
                $db->table($table)->insert($saver);
                $idproduct = $db->insertID();
                $code = $this->generateMejaCode($saver['store'],$idproduct);
                $db->table($table)->where(['id'=>$idproduct])->set(['code'=>$code])->update();
                $ret['idnya']       = $idproduct;
                $ret['status']      = true;
                $ret['message']     = $what.' baru telah ditambahkan';
            }
        } elseif($func=='code') {
            $meja = $db->table($table)->where(['id'=>$id])->get()->getRowArray();
            if ($meja['code']!='') {
                $ret['code']    = $meja['code'];
            } else {
                $code = $this->generateMejaCode($meja['store'],$meja['id']);
                $db->table($table)->where(['id'=>$id])->set(['code'=>$code])->update();
                $ret['code']    = $code;
            }
            $ret['status']      = true;
        }
        echo json_encode($ret);
    }

    public function generateMejaCode($tokoId, $mejaId){
        $secret = env('app.key'); // APP_KEY dari .env

        $payload = $tokoId . '|' . $mejaId . '|' . time();

        return substr(
            str_replace(['+', '/', '='], '', base64_encode(
                hash_hmac('sha256', $payload, $secret, true)
            )),
            0,
            10
        );
    }



    public function bahan($what='owner'){ 
        $db = db_connect();
        $ret['status'] = false; 
        $saver = $_POST['saver'];
        $table  = 'product_component';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK && $_FILES['foto']['size'] > 0 ) {
            $foto = uploadfile('bahan', 'foto');
            if ($foto !== false) {$saver['foto'] = $foto;}
        }
        
        if (isset($_POST['id'])&&$_POST['id']!=0&&$_POST['id']!='') {
            $idnya = dekripsi($_POST['id']);
            $db->table($table)->where(['id'=>$idnya])->set($saver)->update();
            $ret['idnya']       = $idnya;
            $ret['status']      = true;
            $ret['message']     = 'Data bahan telah disimpan';
        } else {
            $str = $db->table('account_store')->where(['id'=>$saver['store']])->get()->getRowArray();
            $saver['company']   = $str['company']??0;

            $db->table($table)->insert($saver);
            $idbahan = $db->insertID();



            $names = [];
            $multiplier = 1;
            $db->table('product_component_units')->insert([
                'component_id'  => $idbahan,
                'unit_name'     => $_POST['base_unit'],
                'multiplier'    => $multiplier,
                'is_base'       => 1
            ]);
            $units = $_POST['units'] ?? [];
            foreach ($units as $unit) {
                if (in_array($unit['name'], $names)) {
                    $mesage = 'Nama satuan tidak boleh sama';
                    //die($mesage);

                    $ret['status']  = false;
                    $ret['message'] = $mesage;
                    echo json_encode($ret);
                    return; // ⛔ stop total
                }
                $names[] = $unit['name'];

                $unitName  = trim($unit['name']);
                $unitValue = (int)$unit['value'];
                // validasi
                if ($unitName === '' || $unitValue <= 0) {
                    continue;
                }

                $multiplier *= $unitValue;
                $uni['component_id']   = $idbahan;
                $uni['unit_name']    = $unitName;
                $uni['multiplier']   = $multiplier;
                $uni['is_base']      = 0;
                $db->table('product_component_units')->insert($uni);
            }
            $ret['idnya']       = $idbahan;
            $ret['status']      = true;
            $ret['message']     = 'bahan baru telah ditambahkan';
        }
        echo json_encode($ret);
    }
    public function cat($cat,$store,$ok){
        $db = db_connect();
        $c = $db->table('product_category_store')->where(['category'=>$cat,'store'=>$store])->get()->getRowArray();
        if (isset($c['id'])) {
            $db->table('product_category_store')->where(['id'=>$c['id']])->set(['status'=>$ok])->update();
        } else {
            $x = $db->table('account_store')->where(['id'=>$store])->select('company')->get()->getRowArray();
            $db->table('product_category_store')->insert([
                'company'   => (isset($x['company'])?$x['company']:''),
                'store'     => $store,
                'category'   => $cat,
                'status'    => $ok,
            ]);
        }
        $ret['data']        = $cat.' - '.$store.' - '.$ok;
        $ret['status']      = false;
        $ret['message']     = 'kategori setlah disetting'; 
        echo json_encode($ret);
    }
}