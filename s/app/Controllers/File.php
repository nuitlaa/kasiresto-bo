<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class File extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
    }
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }

    public function upload_foto_produk($product=null){ 
        $db = db_connect();
        $ret['status'] = false;
        $foto = uploadfile('produk', 'foto');
        if ($foto !== false) {
            $ins['product']     = $product;
            $ins['file']        = $foto;
            $ins['created']     = date('Y-m-d H:i:s');
            $db->table('product_file')->insert($ins);
            $ret['fotoid']      = $db->insertID();
            $ret['status']      = true;
        }
        
        echo json_encode($ret);
    }
}