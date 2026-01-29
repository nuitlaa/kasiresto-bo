<?php
namespace App\Controllers;
use App\Helpers\global_helper;
use App\Libraries\Faskes_lib;
date_default_timezone_set("Asia/Bangkok");
class Store_ extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
        public function hapus() {
        $db = db_connect();
        $id = $this->request->getPost('id');
        $ret = ['status' => false, 'message' => 'Gagal menghapus toko'];

        if ($id) {
             // Cek ketergantungan data jika perlu (misal transaksi, stok, dll)
             // Untuk saat ini hapus soft delete atau hard delete sesuai permintaan user sebelumnya (hard delete)
             
             // Hapus privilage/karyawan toko
             $db->table('account_store_privilage')->where('store', $id)->delete();
             
             // Hapus toko
             $db->table('account_store')->where('id', $id)->delete();
             
             $ret['status'] = true;
             $ret['message'] = 'Toko berhasil dihapus';
        }
        echo json_encode($ret);
    }
}
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }

    public function simpan(){ 
        $db = db_connect();
        $ret['status'] = false;
        $save = $_POST['save'];

        $foto = uploadfile('toko', 'foto');
        if ($foto !== false) {$save['foto'] = $foto;}
        $save['status']     = (isset($save['status'])?'active':'pasive');
        if (isset($_POST['id'])&&$_POST['id']!=0) {
            $idnya = dekripsi($_POST['id']);
            $c = $db->table('account_store')->where(['id'=>$idnya])->select('id')->get()->getRowArray();
            if (isset($c['id'])) { 
                $db->table('account_store')->where(['id'=>$idnya])->set($save)->update();
                if (!empty($_POST['chief'])) {
                    $db->table('account_store_privilage')->where(['store'=>$c['id']])->set(['privilage'=>'employee'])->update();
                    $cek = $db->table('account_store_privilage')->where(['account'=>$_POST['chief']])->get()->getRowArray();
                    if (isset($cek['id'])) {
                        $savep['privilage'] = 'chief';
                        $savep['store']     = $c['id'];
                        $db->table('account_store_privilage')->set($savep)->where(['id'=>$cek['id']])->update();
                    } else {
                        $savep['account']   = $_POST['chief'];
                        $savep['privilage'] = 'chief';
                        $savep['created']   = date('Y-m-d H:i:s');
                        $savep['company']   = mycompanyid();
                        $savep['store']     = $idnya;
                        $db->table('account_store_privilage')->insert($savep);
                    }
                }
                $ret['idnya']       = $idnya;
                $ret['status']      = true;
                $ret['message']     = 'Data Toko telah disimpan';
            } else {
                $ret['message']     = 'id tidak ditemukan';
            }
        } else {
            
                $save['created']    = date('Y-m-d H:i:s');
                $db->table('account_store')->insert($save);
                $idu = $db->insertID();

                $c = $db->table('account_store_privilage')->where(['account'=>$_POST['chief']])->get()->getRowArray();
                if (isset($c['id'])) {
                    $savep['privilage'] = 'chief';
                    $savep['store']     = $idu;
                    $db->table('account_store_privilage')->set($savep)->where(['id'=>$c['id']])->update();
                } else {
                    $savep['account']   = $_POST['chief'];
                    $savep['privilage'] = 'chief';
                    $savep['created']   = date('Y-m-d H:i:s');
                    $savep['company']   = mycompanyid();
                    $savep['store']     = $idu;
                    $db->table('account_store_privilage')->insert($savep);
                }
                $ret['status']      = true;
                $ret['message']     = 'Toko baru telah ditambahkan';
            
        }
        echo json_encode($ret);
    }
}