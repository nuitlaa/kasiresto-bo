<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Cashier_ extends BaseController{
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
    public function checkout(){
        $db = db_connect();
        $customer       = $_POST['customer'];
        $bayar          = intval(preg_replace('/[^0-9]/', '', $_POST['cashInput']));
        $paymethod      = $_POST['paymethod'];

        $userid = usertoken($_SESSION['usertoken']);
        if($_SESSION['userty']=='owner'){
            $p = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
            $dat = $db->table('account_customer')->where(['company'=>$p['id']])->get()->getResultArray();
            $typ = $db->table('account_type')->where(['company'=>$p['id'],'sales'=>1])->orderBy('default DESC')->get()->getResultArray();
            $company    = $p['id'];
            $owner      = $p['owner'];
        } elseif ($_SESSION['userty']=='employee'){
            $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
            $com = $db->table('account_company')->where(['id'=>$p['company']])->get()->getRowArray();
            $dat = $db->table('account_customer')->where(['store'=>$p['store']])->get()->getResultArray();
            $typ = $db->table('account_type')->where(['store'=>$p['store'],'sales'=>1])->orderBy('default DESC')->get()->getResultArray();
            $store      = $p['store'];
            $company    = $com['company']??'';
            $owner      = $com['owner'];
        }
        $invoice = 'INV'.date('ymd').'-'.sprintf("%04d",($db->table('order')->where(['date'=>date('Y-m-d')])->countAllResults())% 10000);
        $cart = $db->table('cart a')->join('product_units u','u.id=a.unit')->where(['a.user' => $userid, 'a.status' => 'carting'])->select('a.*,u.multiplier')->get()->getResultArray();

        $db->table('order')->insert([
            'date'      => date('Y-m-d'),
            'created'   => date('Y-m-d H:i:s'),
            'owner'     => $owner,
            'company'   => $company,
            'store'     => ($store??''),
            'officer'   => $userid,
            'invoice'   => $invoice,
            'customer'  => $cart[0]['customer'],
            'status'    => 'ordering',
            'nominal'   => 0,
        ]);
        $orderid = $db->insertID();
        $totalize = 0;
        foreach($cart as $k=>$v){
            $db->table('orders')->insert([
                'order'     =>$orderid,
                'invoice'   =>$invoice,
                'owner'     =>$owner,
                'company'   =>$company,
                'store'     =>($store??''),
                'petugas'   =>$userid,
                'product'   =>$v['product'],
                'variant'   =>$v['variant'],
                'qty'       =>$v['qty'],
                'unit'      =>$v['unit'],
                'account_type' =>$v['account_type'],
                'nominal'   =>$v['price'],
                'total'     => $v['subtotal'],
                'created'   => date('Y-m-d H:i:s')
            ]);
            $idorders = $db->insertID();
            
            $totalize += $v['subtotal'];
            $db->table('cart')->where(['id'=>$v['id']])->set(['status'=>'ordered','finished'=>date('Y-m-d H:i:s')])->update();

            $theqty = $v['qty'] * $v['multiplier'];

            $pv = $db->table('product_variant')->where(['id'=>$v['variant']])->select('stock,minstock')->get()->getRowArray();
            $stoksisa   = $pv['stock']-$theqty;
            $urgent     = $stoksisa < $pv['minstock'] ? ($pv['minstock'] - $stoksisa) : 0;
            $db->table('product_variant')->where(['id'=>$v['variant']])->set(['stock'=>($stoksisa),'urgent'=>$urgent])->update();


            $db->table('product_io')->insert([
                'product'       => $v['product'],
                'variant'       => $v['variant'],
                'qty'           => $theqty,
                'unit'           => $v['unit'],
                'uqty'           => $v['qty'],
                'awal'          => $pv['stock'],
                'akhir'         => $stoksisa,
                'io'            => 0,
                'created'       => date('Y-m-d H:i:s'),
                'table'         => 'orders',
                'tableid'       => $idorders,
                'func'          => 'penjualan'
            ]);
        }
        $diskon = $db->table('order_discount')->selectSum('nominal')->where(['user' => $userid,'status' => 'carting'])->get()->getRowArray();
        $totalize -= $diskon['nominal'];
        $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->set(['invoice'=>$invoice,'status'=>'ordered'])->update();


        if ($paymethod=='hutang') {
            $debt['customer']   = $customer;
            $debt['status']     = 'hutang';
            $debt['awal']       = saldohutang($customer);
            $debt['nominal']    = $totalize;
            $debt['akhir']      = $debt['awal'] - $debt['nominal'];
            $debt['petugas']    = $userid;
            $debt['created']    = date('Y-m-d H:i:s');
            $debt['invoice']    = $invoice;
            $db->table('account_customer_debt')->insert($debt);
            $ord['lunas']       = 'belum';
        } else {
            $ord['lunas']       = 'lunas';
            $ord['bayar']       = $bayar;
        }
        $ord['payment']     = $paymethod;
        $ord['nominal']     = $totalize;
        $ord['status']      = 'finish';
        $db->table('order')->where(['id'=>$orderid])->set($ord)->update();

        $session = session();
        $session->set('cart', []);

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
        $cartItems = $db->table('cart a')
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

        $discount = $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->select('id,title,description,nominal')->get()->getResultArray();

        $ret['discount'] = $discount;
        $ret['cart']    = $cart;
        $ret['ok']      = true;
        return $this->response->setJSON($ret);
    }


    public function masukankeranjang($idproduct=false){
        $db = db_connect();
        $product = $db->table('product')->where(['id'=>$idproduct])->get()->getRowArray();
        $units  = $db->table('product_units')->where(['id'=>$_POST['unit']])->get()->getRowArray();
        $userid = usertoken($_SESSION['usertoken']);



        // Tentukan account type
        if (!empty($_POST['customer'])) {
            $pl = $db->table('account_customer')->where(['id'=>$_POST['customer']])->get()->getRowArray();
            $atype = $pl['type']??0;
        } else {
            if ($_SESSION['userty']=='owner') {
                $companyid  = companyid($userid);
                $actype = $db->table('account_type')->where(['company'=>$companyid,'status'=>'active'])->orderBy('default DESC, id DESC')->get()->getResultArray();
            } else {
                $st = $db->table('account_store_privilage')->where(['account'=>$userid])->select('store')->get()->getRowArray();
                if (isset($st['store'])) {
                    $actype = $db->table('account_type')->where(['store'=>$st['store'],'status'=>'active'])->orderBy('default DESC, id DESC')->get()->getResultArray();
                } else { $actype = array(); }
            }
            $atype = $actype[0]['id'] ?? 0;
        }
 
        $variant = $db->table('product_variant pv')->join('product_price pp','pp.var=pv.id')->where(['pv.id'=>$_POST['variant'],'pp.account_type'=>$atype,'pp.unit'=>$units['id'] ])->select('pv.*,pp.price')->get()->getRowArray();



        // Tambahkan ke cart
        $c = $db->table('cart')->where(['status'=>'carting','user'=>$userid,'product'=>$idproduct,'variant'=>$_POST['variant'],'unit'=>$_POST['unit']])->get()->getRowArray();
        if(isset($c['id'])){
            $qty = $c['qty'] + $_POST['qty'];
            $db->table('cart')->set(['qty'=>$qty,'price'=>($variant['price']??0),'subtotal'=>(($variant['price']??0) * $qty),'customer'=>$_POST['customer']])->where(['id'=>$c['id']])->update();
        } else {
            $db->table('cart')->insert([
                'user'      => $userid,
                'store'     => $product['store'],
                'product'   => $idproduct,
                'variant'   => $_POST['variant'],
                'qty'       => $_POST['qty'],
                'unit'      => $_POST['unit'],
                'customer'  => $_POST['customer'],
                'price'     => $variant['price']??0,
                'subtotal'  => (($variant['price']??0) * $_POST['qty']),
                'status'    => 'carting',
                'created'   => date('Y-m-d H:i:s')
            ]);
        } 

        // 👍 Return cart baru
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Masuk keranjang',
            'cart' => $this->cartiem($userid)
        ]);

    }

    public function dec($cartid){
        $db = db_connect();
        $cc = $db->table('cart a')->join('product_variant b','b.id=a.variant')->where(['a.id'=>$cartid])->select('a.*,b.stock')->get()->getRowArray();
        if (isset($cc['qty'])){if($cc['qty']>1){$qty=$cc['qty']-1;$db->table('cart')->where(['id'=>$cartid])->set(['qty'=>$qty])->update();}else{$db->table('cart')->where(['id'=>$cartid])->delete();}}
        $userid = usertoken($_SESSION['usertoken']);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Jumlah Produk Dikurangi',
            'cart' => $this->cartiem($userid)
        ]);
    }
    public function inc($cartid){
        $db = db_connect();$cc = $db->table('cart a')->join('product_variant b','b.id=a.variant')->where(['a.id'=>$cartid])->select('a.*,b.stock')->get()->getRowArray();
        if (isset($cc['qty'])){if($cc['qty']<$cc['stock']){$qty=$cc['qty']+1;$db->table('cart')->where(['id'=>$cartid])->set(['qty'=>$qty])->update();}else{$db->table('cart')->where(['id'=>$cartid])->set(['qty'=>$cc['stock']])->update();}}
        $userid = usertoken($_SESSION['usertoken']); 
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Jumlah Produk Ditambah',
            'cart' => $this->cartiem($userid)
        ]);
    }

    public function produk($pelanggan=false){
        $db = db_connect();
        $data = array();
        
        if(isset($_GET['q']) && !empty($_GET['q'])){
            $like['product.name'] = $_GET['q'];
        } else { $like = array(); }
        $userid = usertoken($_SESSION['usertoken']);
 

        $cartItems = $db->table('cart a')->where(['a.user' => $userid, 'a.status' => 'carting'])->select('a.*')->get()->getRowArray();

        if ($_SESSION['userty']=='owner') {
            $companyid  = companyid($userid);
            $actype = $db->table('account_type')->where(['company'=>$companyid,'status'=>'active'])->orderBy('default DESC, id DESC')->get()->getResultArray();
            $defaultpembeli = $actype[0]['id'];
        } else {
            $st = $db->table('account_store_privilage')->where(['account'=>$userid])->select('store')->get()->getRowArray();
            if (isset($st['store'])) {
                $actype = $db->table('account_type')->where(['store'=>$st['store'],'status'=>'active'])->orderBy('default DESC, id DESC')->get()->getResultArray();
                $defaultpembeli = $actype[0]['id']??0;
            } else { $actype = array(); }
        }
        // Tentukan account type
        if ($pelanggan == false) {
            if (!empty($cartItems['customer'])) {
                $cus = $db->table('account_customer')->where(['id'=>$cartItems['customer']])->get()->getRowArray();
                $atype = $cus['type'] ?? 0;
                $aname = $cus['name'];
                $aid    = $cus['id'];
            } else {
                $atype = $actype[0]['id'] ?? 0;
                $aname = 'Umum';
                $aid    = '';
            }
        } else {
            $pl = $db->table('account_customer')->where(['id'=>$pelanggan])->get()->getRowArray();
            $atype = $pl['type']??0;
            $aname = $pl['name'];
            $aid    = $pl['id'];
        }
        //print_r($actype);echo '<br />'.$companyid;echo '<br />'.$userid.'<br />';echo $atype;exit();
        $builder = $db->table('product')
            ->select("
                product.id AS product_id,
                product.name,
                product.description,
                v.id AS variant_id,
                v.var1,
                v.var2,
                v.sku,
                v.stock,
                v.minstock,
                v.default AS variant_default, 
                pu.id AS idunit
            ")
            ->join('(SELECT pu1.* FROM product_units pu1 WHERE pu1.id = ( SELECT pu2.id FROM product_units pu2 WHERE pu2.product_id = pu1.product_id ORDER BY pu2.is_base DESC, pu2.id ASC LIMIT 1 ) ) pu', 'pu.product_id = product.id', 'left')
            ->join('product_variant v', 'v.product = product.id', 'left');
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

        
        $result = [];
        foreach ($query as $row) {
            $pid = $row['product_id'];

            // jika belum ada, buat produk
            if (!isset($result[$pid])) {
                $result[$pid] = [
                    'id'       => $pid,
                    'kode'     => $row['sku'],
                    'nama'     => $row['name'],
                    'kategori' => $row['description'], 
                    'stok'     => $row['stock'],
                    'sku'      => $row['sku'],
                    'idvarpro' => $row['variant_id'],
                    'idunit' => $row['idunit'],
                    'variasi'  => []
                ];
            }

            // Tambahkan variasi
            $vid = $row['variant_id'];
            if (!isset($result[$pid]['_vid'][$vid])) {
                $result[$pid]['variasi'][] = [
                    'id'    => $row['variant_id'], 
                    'v1'    => $row['var1'],
                    'v2'    => $row['var2'],
                    'sku'   => $row['sku'], 
                    'stok'  => $row['stock'],
                    'min'   => $row['minstock'],
                ];
                $result[$pid]['_vid'][$vid] = true;
            }
            
            // Set variant default
            if ($row['variant_default'] == 1) { 
                $result[$pid]['stok']    = $row['stock'];
                $result[$pid]['sku']     = $row['sku'];
                $result[$pid]['idvarpro']= $row['variant_id'];
                $result[$pid]['idunit']  = $row['idunit'];
                $result[$pid]['kode']    = $row['sku'];
            }
        }

        $productIds = array_keys($result); 
        $defaultAccountType = $atype; // account_type default kasir

        $prices = $db->table('product_price')
            ->select('product, var, unit, price')
            ->where('account_type', $defaultAccountType)
            ->whereIn('product', $productIds)
            ->get()
            ->getResultArray();
        $priceMap = [];
        foreach ($prices as $p) {
            $pid  = (int)$p['product'];
            $vid  = (int)$p['var'];
            $unit = (int)$p['unit'];

            if (!isset($priceMap[$pid])) {
                $priceMap[$pid] = [];
            }
            if (!isset($priceMap[$pid][$vid])) {
                $priceMap[$pid][$vid] = [];
            }

            $priceMap[$pid][$vid][$unit] = (int)$p['price'];
        }


        $productUnits = [];
        if (!empty($productIds)) {
            $unitRows = $db->table('product_units')
                ->select('id,product_id, unit_name, multiplier, is_base')
                ->whereIn('product_id', $productIds)
                ->orderBy('is_base DESC, multiplier DESC')
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

        foreach ($result as &$product) {
            $pid = $product['id'];
            $vid = $product['idvarpro']; // variant default
            $unit = $product['idunit']; // unit default (sesuaikan)

            $product['harga'] = $priceMap[$pid][$vid][$unit] ?? 0;
            $product['units'] = $productUnits[$pid] ?? [];

        }
        unset($product);




        if ($pelanggan!=false) {
            $carting = $db->table('cart a')->where(['a.user' => $userid, 'a.status' => 'carting'])->get()->getResultArray();
            foreach($carting as $kk=>$vv){
                $variant = $db->table('product_price')->where(['var'=>$vv['variant'],'product'=>$vv['product'],'account_type'=>$atype,'unit'=>$vv['unit'] ])->select('price')->get()->getRowArray();
                $db->table('cart')->where(['id'=>$vv['id']])->set(['account_type'=>$atype,'price'=>$variant['price']??0,'customer'=>$pelanggan])->update();
            }
        }

        $cart    = $this->cartiem($userid);


        $discount = $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->select('id,title,description,nominal')->get()->getResultArray();

        $data = array_values($result);

        $ret['defaultpembeli'] = $defaultpembeli??0;
        $ret['cart']    = $cart;
        $ret['atype']   = $actype;
        $ret['aname']   = $aname;
        $ret['aid']     = $aid;
        $ret['pembeli'] = $pl['id']??'';
        $ret['data']    = $data;
        $ret['discount'] = $discount;
        $ret['ok']      = true;
        $ret['price_map']       = $priceMap;
        $ret['account_type']    = $atype;
        return $this->response->setJSON($ret);
    }

    public function cartiem($userid){
        $db = db_connect();
        $cartItems = $db->table('cart a')
                        ->select("
                            a.id,
                            a.customer,
                            a.product as product_id,
                            a.variant as variant_id,
                            a.qty,
                            a.unit,
                            u.unit_name as unit_name, 
                            p.name as product_name,
                            v.name as variant_name,
                            a.price,
                            (a.qty * a.price) AS subtotal_item,

                            -- SUM subtotal seluruh cart (window function)
                            SUM(a.qty * a.price) OVER() AS subtotal_all
                        ")
                        ->join('product_units u', 'u.id = a.unit') 
                        ->join('product p', 'p.id = a.product')
                        ->join('product_variant v', 'v.id = a.variant')
                        ->where(['a.user' => $userid, 'a.status' => 'carting'])
                        ->get()
                        ->getResultArray();
        $subtotal = $cartItems[0]['subtotal_all'] ?? 0;
        $diskon             = $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->selectSum('nominal')->get()->getRow()->nominal;
        $cart = [
            'customer'  => $cartItems[0]['customer']??'',
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
                'unit_name'     => $i['unit_name'],
                'product_name'  => $i['product_name'],
                'variant_name'  => $i['variant_name'],
                'price'         => $i['price'],
                'subtotal'      => $i['subtotal_item']
            ];
        }
        $session = session();   // ← WAJIB ADA
        $session->set('cart', $cart['items']);
        return $cart;
    }

    public function clearcust(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $db->table('cart')->where(['user'=>$userid,'status'=>'carting'])->set(['customer'=>''])->update();
    }

    public function discount_add(){
        $db = db_connect();
        $save = $_POST['save'];
        if (!empty($_POST['id'])) {
            $db->table('order_discount')->where(['id'=>$_POST['id']])->set($save)->update();
            $save['id']     = $_POST['id'];
        } else {
            $userid = usertoken($_SESSION['usertoken']);
            $save['user']       = $userid;
            $save['status']     = 'carting';
            if($_SESSION['userty']=='owner'){
                $p = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
                $save['company']    = $p['id'];
            } elseif ($_SESSION['userty']=='employee'){
                $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
                $save['company']    = $p['company'];
                $save['store']      = $p['store'];
            }
            $db->table('order_discount')->insert($save);
            $save['id']     = $db->insertID();
        }
        $ret['status']  = true;
        $ret['data']    = $save;  
        return $this->response->setJSON($ret);
    }

    public function discount_list(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $cartItems = $db->table('cart a')
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
        $ret['status']      = true;
        $ret['data']        = $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->get()->getResultArray(); 
        $ret['subtotal']    = $subtotal;
        $ret['total']       = $subtotal - $diskon;
        return $this->response->setJSON($ret);
    }
 

    public function pelanggan_baru(){
        $db = db_connect();
        $save = $_POST['save'];
        if (!empty($_POST['id'])) {
            $db->table('account_customer')->where(['id'=>$_POST['id']])->set($save)->update();
            $save['id']     = $_POST['id'];
        } else {
            $userid = usertoken($_SESSION['usertoken']);
            if($_SESSION['userty']=='owner'){
                $p = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
                $t = $db->table('account_type')->where(['company'=>$p['id'],'status'=>'active'])->orderBy('default DESC')->get()->getRowArray();
                //$save['type']       = $t['id']??0;
                $save['company']    = $p['id'];
            } elseif ($_SESSION['userty']=='employee'){
                $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
                $t = $db->table('account_type')->where(['store'=>$p['store'],'status'=>'active'])->orderBy('default DESC')->get()->getRowArray();
                //$save['type']       = $t['id']??0;
                $save['company']    = $p['company'];
                $save['store']      = $p['store'];
            }
            $db->table('account_customer')->insert($save);
            $save['id']     = $db->insertID();
        }
        $ret['status']  = true;
        $ret['data']    = $save;  
        return $this->response->setJSON($ret);
    }

    public function pelanggan_list(){
        $db = db_connect();

        if(isset($_GET['q']) && !empty($_GET['q'])){
            $like['a.name'] = $_GET['q'];
        } else { $like = array(); }
        $userid = usertoken($_SESSION['usertoken']);
        if($_SESSION['userty']=='owner'){
            $p = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
            $dat = $db->table('account_customer a')->join('account_type b','b.id=a.type')->select('a.*,b.name nametype')->where(['a.company'=>$p['id'],'a.status'=>'active'])->like($like)->get()->getResultArray(); 
        } elseif ($_SESSION['userty']=='employee'){
            $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
            $dat = $db->table('account_customer a')->join('account_type b','b.id=a.type')->select('a.*,b.name nametype')->where(['a.store'=>$p['store'],'a.status'=>'active'])->like($like)->get()->getResultArray(); 
        }
        $ret['status']  = true;
        $ret['data']    = $dat;  
        return $this->response->setJSON($ret);
    }

    public function clear(){
        $db = db_connect();
        $userid = usertoken($_SESSION['usertoken']);
        $db->table('cart')->where(['user'=>$userid,'status'=>'carting'])->delete();
        $db->table('order_discount')->where(['user'=>$userid,'status'=>'carting'])->delete();
        return $this->response->setJSON([
            'status' => true,
            'message' => 'keranjang telah dihapus', 
        ]);
    } 
}