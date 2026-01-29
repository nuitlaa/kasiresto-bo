<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Worker_ extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);

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
        $save['status']     = (isset($save['status'])?'active':'pasive');

        $privilage = $save['privilage']??'';
        unset($save['privilage']);
        $foto = uploadfile('user', 'foto');
        if ($foto !== false) {$save['foto'] = $foto;}
        if (isset($_POST['id'])&&$_POST['id']!=0) {
            $idnya = dekripsi($_POST['id']);
            $c = $db->table('account')->where(['id'=>$idnya])->select('id')->get()->getRowArray();
            if (isset($c['id'])) {
                if ($save['pass']!='') {
                    $save['pass']       = passcreate($save['pass']);
                } else {
                    unset($save['pass']);
                }
                unset($save['user']);
                $db->table('account')->where(['id'=>$idnya])->set($save)->update();

                $cek = $db->table('account_store_privilage')->where(['account'=>$idnya])->get()->getRowArray();
                if (isset($cek['id'])) {
                    if (isset($_POST['store'])) {
                        $db->table('account_store_privilage')->where(['id'=>$cek['id']])->set(['store'=>$_POST['store'],'privilage'=>$privilage])->update();
                    }
                }

                $ret['status']      = true;
                $ret['message']     = 'Data Petugas telah disimpan';
            } else {
                $ret['message']     = 'id tidak ditemukan';
            }
        } else {
            $c = $db->table('account')->where(['user'=>$save['user']])->select('id')->get()->getRowArray();
            if (isset($c['id'])) {
                $ret['message']     = 'username sudah terdaftar, silahkan gunakan yang lain';
            } else {
                
                $save['type']       = 'employee';
                $save['pass']       = passcreate($save['pass']);
                $save['created']    = date('Y-m-d H:i:s');
                $db->table('account')->insert($save);
                $idu = $db->insertID();

                $savep['account']   = $idu;
                $savep['privilage'] = $privilage;
                $savep['created']   = date('Y-m-d H:i:s');
                $savep['company']   = mycompanyid(); 
                if (isset($_POST['store'])) { $savep['store'] = $_POST['store']; }

                $db->table('account_store_privilage')->insert($savep);
                $ret['status']      = true;
                $ret['message']     = 'Petugas baru telah ditambahkan';
            }
        }
        echo json_encode($ret);
    } 

    public function hapus() {
        $db = db_connect();
        $id = $this->request->getPost('id');
        $ret = ['status' => false, 'message' => 'Gagal menghapus data'];

        if ($id) {
             $db->table('account_store_privilage')->where('account', $id)->delete();
             $db->table('account')->where('id', $id)->delete();
             
             $ret['status'] = true;
             $ret['message'] = 'Petugas berhasil dihapus';
        }
        echo json_encode($ret);
    }

    public function multiupload(){
        helper('upload');

        $fotos = uploadfile_multi('produk', 'foto');

        if ($fotos !== false) {
            foreach ($fotos as $img) {
                $save = [
                    'foto'        => $img['original'],
                    'foto_thumb' => $img['thumb'],
                    'foto_small' => $img['small'],
                    'foto_medium'=> $img['medium'],
                    'foto_large' => $img['large'],
                ];

                // simpan ke database
                $this->ProdukModel->insert($save);
            }
        }

        /*
            [
              [
                'original' => 'produk/abc/foto.jpg',
                'thumb'    => 'produk/abc/foto_thumb.jpg',
                'small'    => 'produk/abc/foto_small.jpg',
                'medium'   => 'produk/abc/foto_medium.jpg',
                'large'    => 'produk/abc/foto_large.jpg',
              ],
              [
                'original' => 'produk/xyz/foto2.jpg',
                ...
              ]
            ]
        */

    }
}