<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Belanja_ extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
    }
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }

    public function print($invoice){
        $db = db_connect();
        return $this->response->setJSON([
            'status' => true,
            'message' => 'printed',
        ]);
    }
    public function retur(){
        $db = db_connect();
        $c = $db->table('product_variant v')->join('product p','p.id=v.product')->where(['v.id'=>$_POST['variant']])->select('v.stock,v.minstock,v.product,p.company,p.store')->get()->getRowArray();
        if (isset($c['stock'])) {
            $stok       = $c['stock'] - $_POST['qty'];
            $urgent     = $stok < $c['minstock'] ? ($c['minstock'] - $stok) : 0;
            $db->table('product_variant')->where(['id'=>$_POST['variant']])->set(['stock'=>$stok,'urgent'=>$urgent])->update();
            $db->table('product_io')->insert([
                'company'       => $c['company'],
                'store'         => $c['store'],
                'product'       => $c['product'],
                'variant'       => $_POST['variant'],
                'qty'           => $_POST['qty'],
                'awal'          => $c['stock'],
                'akhir'         => $stok,
                'io'            => 0,
                'created'       => date('Y-m-d H:i:s'),
                'table'         => null,
                'tableid'       => null,
                'func'          => 'retur'
            ]);
        }
        return $this->response->setJSON([
            'status' => true,
            'message' => 'retur telah selesai',
        ]);
    }
    public function tukar(){
        $db = db_connect();
        $c1 = $db->table('product_variant v')->join('product p','p.id=v.product')->where(['v.id'=>$_POST['v1']])->select('v.stock,v.minstock,v.product,p.company,p.store')->get()->getRowArray();
        $c2 = $db->table('product_variant v')->join('product p','p.id=v.product')->where(['v.id'=>$_POST['v2']])->select('v.stock,v.minstock,v.product,p.company,p.store')->get()->getRowArray();
        if (isset($c1['stock']) && isset($c2['stock'])) {
            $stok       = $c1['stock'] - $_POST['q1'];
            $urgent     = $stok < $c1['minstock'] ? ($c1['minstock'] - $stok) : 0;
            $db->table('product_variant')->where(['id'=>$_POST['v1']])->set(['stock'=>$stok,'urgent'=>$urgent])->update();
            $db->table('product_io')->insert([
                'company'       => $c1['company'],
                'store'         => $c1['store'],
                'product'       => $c1['product'],
                'variant'       => $_POST['v1'],
                'qty'           => $_POST['q1'],
                'awal'          => $c1['stock'],
                'akhir'         => $stok,
                'io'            => 0,
                'created'       => date('Y-m-d H:i:s'),
                'table'         => 'product_io',
                'tableid'       => null,
                'func'          => 'ditukar'
            ]);
            $id1 = $db->insertID();

            $stok2       = $c2['stock'] + $_POST['q1'];
            $urgent2     = $stok2 < $c2['minstock'] ? ($c2['minstock'] - $stok2) : 0;
            $db->table('product_variant')->where(['id'=>$_POST['v2']])->set(['stock'=>$stok2,'urgent'=>$urgent2])->update();
            $db->table('product_io')->insert([
                'company'       => $c2['company'],
                'store'         => $c2['store'],
                'product'       => $c2['product'],
                'variant'       => $_POST['v2'],
                'qty'           => $_POST['q1'],
                'awal'          => $c2['stock'],
                'akhir'         => $stok2,
                'io'            => 1,
                'created'       => date('Y-m-d H:i:s'),
                'table'         => 'product_io',
                'tableid'       => $id1,
                'func'          => 'menukar'
            ]);
            $id2 = $db->insertID();
            $db->table('product_io')->where(['id'=>$id1])->set(['tableid'=>$id2])->update();
        }
        return $this->response->setJSON([
            'status' => true,
            'message' => 'penukaran telah selesai',
        ]);
    }
    public function checkout(){
        $db = db_connect();
        $supplier       = $_POST['supplier'];
        $bayar          = intval(preg_replace('/[^0-9]/', '', $_POST['cashInput']));
        $paymethod      = $_POST['paymethod'];

        $userid = usertoken($_SESSION['usertoken']);
        if($_SESSION['userty']=='owner'){
            $p = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
            $company    = $p['id'];
            $owner      = $p['owner'];
        } elseif ($_SESSION['userty']=='employee'){
            $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
            $com = $db->table('account_company')->where(['id'=>$p['company']])->get()->getRowArray();
            $store      = $p['store'];
            $company    = $com['company']??'';
            $owner      = $com['owner'];
        }
        $invoice = 'IPB'.date('ymd').'-'.sprintf("%04d",($db->table('purchase')->where(['date'=>date('Y-m-d')])->countAllResults())% 10000);
        $db->table('purchase')->insert([
            'date'      => date('Y-m-d'),
            'created'   => date('Y-m-d H:i:s'),
            'owner'     => $owner,
            'company'   => $company,
            'store'     => ($store??''),
            'officer'   => $userid,
            'invoice'   => $invoice,
            'supplier'  => $supplier,
            'status'    => 'ordering',
            'nominal'   => 0,
        ]);
        $orderid = $db->insertID();
        $cart = $db->table('purchase_cart a')->join('product_units b','b.id=a.unit')->join('product ppp','ppp.id=a.product')->where(['a.user' => $userid, 'a.status' => 'carting'])->select('a.*,b.unit_name,b.multiplier,ppp.store idstore,ppp.company idcompany')->get()->getResultArray();
        $totalize = 0;
        foreach($cart as $k=>$v){
            $db->table('purchases')->insert([
                'purchaseid' =>$orderid,
                'invoice'   =>$invoice,
                'owner'     =>$owner,
                'company'   =>$company,
                'store'     =>($store??''),
                'petugas'   =>$userid,
                'product'   =>$v['product'],
                'variant'   =>$v['variant'],
                'unit'       =>$v['unit'],
                'qty'       =>$v['qty'],
                'nominal'   =>$v['price'],
                'total'     => $v['subtotal'],
                'created'   => date('Y-m-d H:i:s')
            ]);

            $idpurchases = $db->insertID();
            $totalize += $v['subtotal'];
            $db->table('purchase_cart')->where(['id'=>$v['id']])->set(['status'=>'ordered','finished'=>date('Y-m-d H:i:s')])->update();

            $theqty     = $v['qty'] * $v['multiplier'];
            $pv = $db->table('product_variant')->where(['id'=>$v['variant']])->select('stock,minstock')->get()->getRowArray();
            $stoksisa   = $pv['stock']+$theqty;
            $urgent     = $stoksisa < $pv['minstock'] ? ($pv['minstock'] - $stoksisa) : 0;
            $db->table('product_variant')->where(['id'=>$v['variant']])->set(['stock'=>($stoksisa),'urgent'=>$urgent,'base_price'=>$v['price'] ])->update();

            $db->table('product_io')->insert([
                'company'       => $v['idcompany'],
                'store'         => $v['idstore'],
                'product'       => $v['product'],
                'variant'       => $v['variant'],
                'qty'           => $theqty,
                'awal'          => $pv['stock'],
                'akhir'         => $stoksisa,
                'io'            => 1,
                'created'       => date('Y-m-d H:i:s'),
                'table'         => 'purchases',
                'tableid'       => $idpurchases,
                'func'          => 'belanja'
            ]);
        }
        

        if ($paymethod=='hutang') {
            $debt['supplier']   = $supplier;
            $debt['status']     = 'hutang';
            $debt['awal']       = saldopiutang($supplier);
            $debt['nominal']    = $totalize;
            $debt['akhir']      = $debt['awal'] - $debt['nominal'];
            $debt['petugas']    = $userid;
            $debt['created']    = date('Y-m-d H:i:s');
            $debt['invoice']    = $invoice;
            $db->table('account_supplier_lent')->insert($debt);
            $ord['lunas']       = 'belum';
        } else {
            $ord['lunas']       = 'lunas';
            $ord['bayar']       = $bayar;
        }
        $ord['payment']     = $paymethod;
        $ord['nominal']     = $totalize;
        $ord['status']      = 'finish';
        $db->table('purchase')->where(['id'=>$orderid])->set($ord)->update();

        $session = session();
        $session->set('purchase_cart', []);

        // 👍 Return cart baru
        return $this->response->setJSON([
            'status' => true,
            'message' => 'checkout telah selesai',
            'invoice' => $invoice,
            'total' => $totalize
        ]);
    }


    public function review(){
        $db = db_connect();
        $data = array();
        
        $userid = usertoken($_SESSION['usertoken']);
        $cartItems = $db->table('purchase_cart a')
                        ->select("
                            a.id,
                            a.product as product_id,
                            a.variant as variant_id,
                            a.qty,
                            p.name as product_name,
                            v.name as variant_name,
                            a.price,
                            (a.qty * a.price) AS subtotal_item,

                            -- SUM subtotal seluruh cart (window function)
                            SUM(a.qty * a.price) OVER() AS subtotal_all
                        ")
                        ->join('product p', 'p.id = a.product')
                        ->join('product_variant v', 'v.id = a.variant')
                        ->where(['a.user' => $userid, 'a.status' => 'carting'])
                        ->get()
                        ->getResultArray();
        $subtotal = $cartItems[0]['subtotal_all'] ?? 0;
        $diskon             = $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->selectSum('nominal')->get()->getRow()->nominal;
        $cart = [
            'items'     => [],
            'subtotal'  => $subtotal,
            'discount'  => $diskon,
            'total'     => $subtotal - $diskon
        ];

        foreach ($cartItems as $i) {
            $cart['items'][] = [
                'id'            => $i['id'],
                'product_id'    => $i['product_id'],
                'variant_id'    => $i['variant_id'],
                'qty'           => $i['qty'],
                'product_name'  => $i['product_name'],
                'variant_name'  => $i['variant_name'],
                'price'         => $i['price'],
                'subtotal'      => $i['subtotal_item']
            ];
        }

        $discount = array();

        $ret['discount'] = $discount;
        $ret['cart']    = $cart;
        $ret['ok']      = true;
        return $this->response->setJSON($ret);
    }


    public function masukankeranjang($idproduct=false){
        $db = db_connect();
        $product = $db->table('product')->where(['id'=>$idproduct])->get()->getRowArray();
        
        $variant['id']      = $_POST['variant'];
        $variant['price']   = harga($_POST['price']??'');
        // Tambahkan ke cart
        $userid = usertoken($_SESSION['usertoken']);
        $c = $db->table('purchase_cart')->where(['status'=>'carting','user'=>$userid,'product'=>$idproduct,'variant'=>$_POST['variant'],'unit'=>$_POST['unit']])->get()->getRowArray();
        if(isset($c['id'])){
            $qty                = $c['qty'] + $_POST['qty'];
            $db->table('purchase_cart')->set(['qty'=>$qty,'price'=>$variant['price'],'subtotal'=>($variant['price'] * $qty)])->where(['id'=>$c['id']])->update();
        } else {
            $db->table('purchase_cart')->insert([
                'user'      => $userid,
                'store'     => $product['store'],
                'product'   => $idproduct,
                'variant'   => $variant['id'],
                'qty'       => $_POST['qty'],
                'unit'       => $_POST['unit'],
                'price'     => $variant['price'],
                'subtotal'  => ($variant['price'] * $_POST['qty']),
                'status'    => 'carting',
                'created'   => date('Y-m-d H:i:s')
            ]);
        }

         
 
        $cart    = $this->cartiem($userid);
        // Simpan ke session
        $session = session();   // ← WAJIB ADA
        $session->set('purchase_cart', $cart['items']);

        // 👍 Return cart baru
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Masuk keranjang',
            'cart' => $cart
        ]);

    }

    public function dec($cartid){
        $db = db_connect();
        $cc = $db->table('purchase_cart a')->join('product_variant b','b.id=a.variant')->where(['a.id'=>$cartid])->select('a.*,b.stock')->get()->getRowArray();
        if (isset($cc['qty'])){if($cc['qty']>1){$qty=$cc['qty']-1;$db->table('purchase_cart')->where(['id'=>$cartid])->set(['qty'=>$qty,'subtotal'=>($qty*$cc['price'])])->update();}else{$db->table('purchase_cart')->where(['id'=>$cartid])->delete();}}


        $userid = usertoken($_SESSION['usertoken']); 
        $cart    = $this->cartiem($userid);
        $session = session();   // ← WAJIB ADA
        $session->set('purchase_cart', $cart['items']);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Jumlah Produk Dikurangi',
            'cart' => $cart
        ]);
    }
    public function inc($cartid){
        $db = db_connect();$cc = $db->table('purchase_cart a')->join('product_variant b','b.id=a.variant')->where(['a.id'=>$cartid])->select('a.*,b.stock')->get()->getRowArray();
        if (isset($cc['qty'])){ $qty=$cc['qty']+1;$db->table('purchase_cart')->where(['id'=>$cartid])->set(['qty'=>$qty,'subtotal'=>($qty*$cc['price'])])->update(); }


        $userid = usertoken($_SESSION['usertoken']); 
        $cart    = $this->cartiem($userid);
        $session = session();   // ← WAJIB ADA
        $session->set('purchase_cart', $cart['items']);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Jumlah Produk Ditambah',
            'cart' => $cart
        ]);
    }

    public function produk($pelanggan=false){
        $db = db_connect();
        $data = array();
        
        if(isset($_GET['q']) && !empty($_GET['q'])){
            $like['product.name'] = $_GET['q'];
        } else { $like = array(); }
        $userid = usertoken($_SESSION['usertoken']);
 

        // Tentukan account type
        if ($pelanggan == false) {
            $pl = $db->table('account_type')->where(['sales'=>1,'default'=>1])->get()->getRowArray();
            $atype = $pl['id'] ?? 1;
        } else {
            $pl = $db->table('account_supplier')->where(['id'=>$pelanggan])->get()->getRowArray();
            $atype = $pl['type']??1;
        }

        $builder = $db->table('product')
            ->select("
                product.id AS product_id,
                product.name,
                product.description,
                v.id AS variant_id,
                product.var1 AS varian1,
                product.var2 AS varian2,
                v.var1,
                v.var2,
                v.name AS namavariant,
                v.sku,
                v.stock,
                v.base_price,
                v.minstock,
                v.default AS variant_default,
                pp.price
            ")
            ->join('product_variant v', 'v.product = product.id', 'left')
            ->join('product_price pp', "pp.product = product.id AND pp.var = v.id AND pp.account_type = {$atype}", 'left');
            // tambahkan join berdasarkan userty
            if ($_SESSION['userty'] == 'owner') {
                $builder->join('account_company ac', 'ac.id = product.company')->where('ac.owner', $userid);
            } else {
                $builder->join('account_store_privilage asp', 'asp.store = product.store')->where('asp.account', $userid);
            }
 


        $query = $builder
            ->like($like) 
            ->orderBy('product.id')
            ->orderBy('v.default', 'DESC')   // default variant berada paling atas
            ->where('product.status','active')
            ->get()
            ->getResultArray();

        $productIds = [];
        foreach ($query as $row) {
            $productIds[$row['product_id']] = true;
        }
        $productIds = array_keys($productIds);
        $productUnits = [];
        if (!empty($productIds)) {
            $unitRows = $db->table('product_units')
                ->select('id,product_id, unit_name, multiplier, is_base')
                ->whereIn('product_id', $productIds)
                ->orderBy('multiplier', 'DESC')
                ->get()
                ->getResultArray();

            foreach ($unitRows as $u) {
                $productUnits[$u['product_id']][] = [
                    'id'        => $u['id'],
                    'name'       => $u['unit_name'],
                    'multiplier' => (int)$u['multiplier'],
                    'is_base'    => (int)$u['is_base']
                ];
            }
        }

        $result = [];
        foreach ($query as $row) {
            $productIds[$row['product_id']] = true;
            $pid = $row['product_id'];

            // jika belum ada, buat produk
            if (!isset($result[$pid])) {
                $result[$pid] = [
                    'id'       => $pid,
                    'kode'     => $row['sku'],
                    'nama'     => $row['name'],
                    'kategori' => $row['description'],
                    'harga'    => $row['price'],
                    'stok'     => $row['stock'],
                    'sku'      => $row['sku'],
                    'var2'      => $row['varian2'],
                    'var1'      => $row['varian1'],
                    'idvarpro' => $row['variant_id'],
                    'variasi'  => [],
                    'units' => $productUnits[$pid] ?? []
                ];
            }

            // Tambahkan variasi
            $result[$pid]['variasi'][] = [
                'id'    => $row['variant_id'],
                'v1'    => $row['var1'],
                'v2'    => $row['var2'],
                'namavariant'    => $row['namavariant'],
                'sku'   => $row['sku'],
                'harga' => $row['base_price'],
                'stok'  => $row['stock'],
                'min'   => $row['minstock'],
            ];
            
            // Set variant default
            if ($row['variant_default'] == 1) {
                $result[$pid]['harga']   = $row['base_price'];
                $result[$pid]['stok']    = $row['stock'];
                $result[$pid]['sku']     = $row['sku'];
                $result[$pid]['idvarpro']= $row['variant_id'];
                $result[$pid]['kode']    = $row['sku'];
            }
        }

        $data = array_values($result);
        $ret['data']    = $data; 
        $ret['discount'] = 0;
        $ret['cart']    = $this->cartiem($userid);
        $ret['ok']      = true;
        return $this->response->setJSON($ret);
    }

    public function cartiem($userid){
        $db = db_connect();

        $cartItems = $db->table('purchase_cart a')
                        ->select("
                            a.id,
                            a.product as product_id,
                            a.variant as variant_id,
                            a.qty,
                            p.name as product_name,
                            v.name as variant_name,
                            pu.unit_name as satuan,
                            a.price,
                            (a.qty * a.price) AS subtotal_item,

                            -- SUM subtotal seluruh cart (window function)
                            SUM(a.qty * a.price) OVER() AS subtotal_all
                        ")
                        ->join('product p', 'p.id = a.product')
                        ->join('product_units pu', 'pu.id = a.unit')
                        ->join('product_variant v', 'v.id = a.variant')
                        ->where(['a.user' => $userid, 'a.status' => 'carting'])
                        ->get()
                        ->getResultArray();
        $subtotal = $cartItems[0]['subtotal_all'] ?? 0;
        $diskon             = $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->selectSum('nominal')->get()->getRow()->nominal;
        $cart = [
            'items'     => [],
            'subtotal'  => $subtotal,
            'discount'  => $diskon,
            'total'     => $subtotal - $diskon
        ];

        foreach ($cartItems as $i) {
            $cart['items'][] = [
                'id'            => $i['id'],
                'product_id'    => $i['product_id'],
                'variant_id'    => $i['variant_id'],
                'qty'           => $i['qty'],
                'product_name'  => $i['product_name'],
                'variant_name'  => $i['variant_name'],
                'satuan'        => $i['satuan'],
                'price'         => $i['price'],
                'subtotal'      => $i['subtotal_item']
            ];
        }
        return $cart;
    }

    public function variant_baru(){
        $db = db_connect();
        $pr = $db->table('product')->where(['id'=>$_POST['product']])->get()->getRowArray();
        $ret['name']    = $_POST['var1'].' - '.$_POST['var2'];
        $namanya = $pr['name'].' '.$ret['name'];
        $sku        = clean_string($namanya);
        $db->table('product_variant')->insert([
            'product'      => $_POST['product'],
            'var'           => 1,
            'var1'          => $_POST['var1'],
            'var2'          => $_POST['var2'],
            'name'          => $namanya,
            'sku'           => $sku,
            'minstock'      => $_POST['min']
        ]);

        $ret['idnya']   = $db->insertID();
        $variant['id']              = $ret['idnya'];
        $variant['namavariant']     = $namanya;
        $variant['v1']              = $_POST['var1'];
        $variant['v2']              = $_POST['var2'];
        $variant['sku']             = $sku; 

        $ret['variant']     = $variant;
        $ret['status']  = true;
        $ret['message'] = 'saved';
        return $this->response->setJSON($ret);
    }
 

    public function supplier_baru(){
        $db = db_connect();
        $save = $_POST['save'];
        if (!empty($_POST['id'])) {
            $db->table('account_supplier')->where(['id'=>$_POST['id']])->set($save)->update();
            $save['id']     = $_POST['id'];
        } else {
            $userid = usertoken($_SESSION['usertoken']);
            if($_SESSION['userty']=='owner'){
                $cc = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
                $save['owner']      = $userid;
                $save['company']    = $cc['id'];
            } elseif ($_SESSION['userty']=='employee'){
                $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
                $save['company']    = $p['company'];
                $save['store']      = $p['store'];
            }
            $db->table('account_supplier')->insert($save);
            $save['id']     = $db->insertID();
        }
        $ret['status']  = true;
        $ret['data']    = $save;  
        return $this->response->setJSON($ret);
    }

    public function supplier_list(){
        $db = db_connect();

        if(isset($_GET['q']) && !empty($_GET['q'])){
            $like['name'] = $_GET['q'];
        } else { $like = array(); }
        $userid = usertoken($_SESSION['usertoken']);
        if($_SESSION['userty']=='owner'){
            $p = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
            $dat = $db->table('account_supplier')->where(['company'=>$p['id']])->like($like)->get()->getResultArray();
            $typ = $db->table('account_type')->where(['company'=>$p['id'],'sales'=>1])->orderBy('default DESC')->get()->getResultArray();
        } elseif ($_SESSION['userty']=='employee'){
            $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
            $dat = $db->table('account_supplier')->where(['store'=>$p['store']])->like($like)->get()->getResultArray();
            $typ = $db->table('account_type')->where(['store'=>$p['store'],'sales'=>1])->orderBy('default DESC')->get()->getResultArray();
        }
        $ret['status']  = true;
        $ret['data']    = $dat; 
        $ret['type']    = $typ;
        return $this->response->setJSON($ret);
    }

    public function clear(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $db->table('purchase_cart')->where(['user'=>$userid,'status'=>'carting'])->delete();
        $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->delete();
        return $this->response->setJSON([
            'status' => true,
            'message' => 'keranjang telah dihapus', 
        ]);
    }
      
    public function produk__($pelanggan=false){
        $db = db_connect();
        $data = array();
        
        if(isset($_GET['q']) && !empty($_GET['q'])){
            $like['name'] = $_GET['q'];
        } else { $like = array(); }
        $userid = usertoken($_SESSION['usertoken']);

        if($_SESSION['userty']=='owner'){
            $p = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
            $dat = $db->table('product')->where(['company'=>$p['id']])->like($like)->get()->getResultArray();
        } elseif ($_SESSION['userty']=='employee'){
            $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
            $dat = $db->table('product')->where(['store'=>$p['store']])->like($like)->get()->getResultArray();
        }
        foreach($dat as $k=>$v){
            
            $add['id'] =  $v['id'];
            $add['kode'] =  "-";
            $add['nama'] =  $v['name'];
            $add['kategori'] =  $v['description'];
            $add['harga'] =  45000;
            $add['stok'] =  40;
            
            $var = [];
            foreach($db->table('product_variant')->where(['product'=>$v['id']])->orderBy('default DESC')->get()->getResultArray() as $kk=>$vv){
                if($pelanggan==false){
                    $pl = $db->table('account_type')->where(['sales'=>1,'default'=>1])->get()->getRowArray();
                    $atype= isset($pl['id'])?$pl['id']:1;
                } else {
                    $atype = $pelanggan;
                }
                $pri = $db->table('product_price')->where(['product'=>$v['id'],'var'=>$vv['id'],'account_type'=>$atype])->get()->getRowArray();
                if($kk==0){
                    $add['harga']   = $pri['price'];
                    $add['stok']    = $vv['stock'];
                    $add['sku']     = $vv['sku'];
                    $add['kode']     = $vv['sku'];
                    $add['idvarpro']     = $vv['id'];
                }
                $vari['id']     = $vv['id'];
                $vari['v1']   = $vv['var1'];
                $vari['v2']   = $vv['var2'];
                $vari['sku']  = $vv['sku'];
                $vari['harga']  = $pri['price'];
                $vari['stok']   = $vv['stock'];
                $vari['min']    = $vv['minstock'];
                array_push($var,$vari);
            }
            $add['variasi'] =  $var;
            
            array_push($data,$add);
        }
        return $this->response->setJSON($data);
    }
}