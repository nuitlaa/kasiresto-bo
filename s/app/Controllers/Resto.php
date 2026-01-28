<?php
namespace App\Controllers;
use App\Controllers\BaseController; // <--- INI YANG KURANG
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Resto extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
    }
    public function index($page=false,$param1=false,$param2=false,$param3=false){
        return $this->$page($param1,$param2,$param3);
    }

    public function print($invoice=false){
        $db = db_connect();
        return $this->response->setJSON([
            'status' => true,
            'message' => 'printed',
        ]);
    }
    public function checkout(){
        // {"items":[{"price":8000,"product_id":6,"product_name":"Ayam Fillet Crispi","qty":2,"subtotal":16000,"variant_id":20,"variant_name":"Ayam Fillet Crispi - original"},{"price":8000,"product_id":6,"product_name":"Ayam Fillet Crispi","qty":2,"subtotal":16000,"variant_id":21,"variant_name":"Ayam Fillet Crispi - sambal merah"},{"price":8000,"product_id":6,"product_name":"Ayam Fillet Crispi","qty":2,"subtotal":16000,"variant_id":22,"variant_name":"Ayam Fillet Crispi - hot lava"}],"kembalian":52000,"total_bayar":100000,"total_belanja":48000,"usertoken":"6c3d7b0f7135849283e594b25379dfde464bc887301b84404427f5c664c95c5b"}
        $db = db_connect();

        $request = service('request');

        // ambil JSON body
        $post = $request->getJSON(true);

        $customer = $post['pelanggan'] ?? null;
        $diskon   = $post['diskon'] ?? 0;
        $namadiskon   = $post['namadiskon'] ?? 0;
        $total    = $post['total'] ?? 0;
        $bayar    = $post['total_bayar'] ?? 0;
        $token    = $post['usertoken'] ?? null;
        $paymethod= $post['paymethod'] ?? null;
        $paystatus = (!empty($post['paystatus']) && $post['paystatus'] != '0')? $post['paystatus'] : $paymethod;
        if ($paystatus=='hutang') {$lunas  = 'belum';}
        $paymethodid= $post['paymethodid'] ?? null;
        $items    = $post['items'] ?? [];
 
        $bayar          = intval(preg_replace('/[^0-9]/', '', $bayar));
        $PM             = $db->table('payment_method')->where(['func'=>$paymethod])->get()->getRowArray();
        $payment_method = $PM['id']??1;

        $userid = usertoken($token);
            $p = $db->table('account_store_privilage a')->join('account_store s','s.id=a.store')->where(['a.account'=>$userid])->select('a.*,s.name nama_toko,s.address alamat_toko,s.foto foto_raw,s.phone hp_toko')->get()->getRowArray();
            if (!empty($p['foto_raw'])) {
                $p['foto_toko'] = 'https://store.wiyatajatidiri.com/f/' . str_replace('.', '_thumb.', $p['foto_raw']);
            } else {
                $p['foto_toko'] = 'https://store.wiyatajatidiri.com/f/' . sys('nofoto');
            }

            unset($p['foto_raw']);

            $com = $db->table('account_company')->where(['id'=>$p['company']])->get()->getRowArray();
            
            $store      = $p['store'];
            $company    = $com['id']??'';
            $owner      = $com['owner']??'';
        
        $invoice = 'INV'.date('ymd').'-'.sprintf("%04d",($db->table('order')->where(['date'=>date('Y-m-d')])->countAllResults())% 10000);
        $db->table('order')->insert([
            'date'      => date('Y-m-d'),
            'created'   => date('Y-m-d H:i:s'),
            'owner'     => $owner,
            'bayar'     => $bayar,
            'company'   => $company,
            'store'     => ($store??''),
            'officer'   => $userid,
            'invoice'   => $invoice,
            'lunas'     => $lunas??'lunas',
            'customer'  => $customer,
            'payment'   => $paystatus,
            'payment_method' => $payment_method,
            'status'    => 'ordering',
            'nominal'   => 0,
        ]);

        //{"price":8000,"product_id":6,"product_name":"Ayam Fillet Crispi","qty":2,"subtotal":16000,"variant_id":20,"variant_name":"Ayam Fillet Crispi - original"}

        $orderid = $db->insertID();
        //$cart = $db->table('cart a')->where(['a.user' => $userid, 'a.status' => 'carting'])->get()->getResultArray();
        $totalize = 0;
        foreach($items as $k=>$v){
            $db->table('orders')->insert([
                'order'     =>$orderid,
                'invoice'   =>$invoice,
                'owner'     =>$owner,
                'company'   =>$company,
                'store'     =>($store??''),
                'petugas'   =>$userid,
                'product'   =>$v['product_id'],
                'variant'   =>$v['variant_id'],
                'qty'       =>$v['qty'],
                'nominal'   =>$v['price'],
                'total'     => $v['subtotal'],
                'created'   => date('Y-m-d H:i:s')
            ]);
            $totalize += $v['subtotal'];
            $pv = $db->table('product_variant')->where(['id'=>$v['variant_id']])->select('stock,minstock')->get()->getRowArray();
            $stoksisa   = $pv['stock']-$v['qty'];
            $urgent     = $stoksisa < $pv['minstock'] ? ($pv['minstock'] - $stoksisa) : 0;
            $db->table('product_variant')->where(['id'=>$v['variant_id']])->set(['stock'=>($stoksisa),'urgent'=>$urgent])->update();
            //$db->table('cart')->where(['id'=>$v['id']])->set(['status'=>'ordered','finished'=>date('Y-m-d H:i:s')])->update();

            $reci = $db->table('product_recipe a')->join('product_component b','a.component_id=b.id')->where(['a.variant_id'=>$v['variant_id']])->select('a.id,a.component_id,a.qty,b.stock,b.minstock,b.urgent')->get()->getResultArray();
            foreach($reci as $kkk=>$vvv){
                $stoksisac   = $vvv['stock']-($v['qty']*$vvv['qty']);
                $urgentc     = $stoksisac < $vvv['minstock'] ? ($vvv['minstock'] - $stoksisac) : 0;
                $db->table('product_component_movement')->insert([
                    'company'       => $company,
                    'store'         => ($store??''),
                    'product_id'    => $v['product_id'],
                    'variant_id'    => $v['variant_id'],
                    'component_id'  => $vvv['id'],
                    'awal'          => $vvv['stock'],
                    'io'            => 0,
                    'qty'           => ($v['qty'] * $vvv['qty']),
                    'akhir'         => $stoksisac,
                    'func'          => 'checkout',
                    'table'         => 'order',
                    'tableid'       => $orderid,
                    'created'       => date('Y-m-d H:i:s')
                ]);
                $db->table('product_component')->where(['id'=>$vvv['id']])->set(['stock'=>($stoksisac),'urgent'=>$urgentc])->update();
            }
        }
        //$diskon = $db->table('order_discount')->selectSum('nominal')->where(['user'   => $userid,'status' => 'carting'])->get()->getRowArray();
        $diskon = (int) ($diskon ?? 0);
        $totalize -= $diskon;
        if ($diskon>0) {
            $db->table('order_discount')->insert(['company'=>($company??0),'store'=>($store??0),'title'=>$namadiskon,'user'=>$userid,'status'=>'carting','invoice'=>$invoice,'status'=>'ordered','nominal'=>$diskon]);
        }


        if ($paystatus=='hutang') {
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
        $ord['payment']     = $paystatus;
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
            'total' => $totalize,
            'order'   => $db->table('order')->where(['id'=>$orderid])->get()->getRowArray(),
            'orders'    => $db->table('orders o')->join('product p','p.id=o.product')->join('product_variant pv','pv.id=o.variant')->where(['order'=>$orderid])->select('o.qty,o.nominal,o.total,p.name nama_produk,pv.name nama_variant')->get()->getResultArray(),
            'diskon'    => $db->table('order_discount o')->where(['invoice'=>$invoice])->select('title,nominal')->get()->getResultArray(),
            'customer'   => $db->table('account_customer')->where(['id'=>$customer])->get()->getRowArray(),
            'store'     => $p,
            'bayar'     => $bayar,
            'kembalian' => ($bayar-$totalize),
            'payment'   => $paystatus,
            'paymethod' => $paymethod,
        ]);
    }

    public function pelanggan_tambah($usertoken){
        $db = db_connect();
        $request = service('request');
        $post = $request->getJSON(true);

        $userid = usertoken($usertoken);
        $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray(); 
        $save['sales']      = $userid;
        $save['store']      = $p['store'];
        $save['company']    = $p['company'];
        $save['type']       = 0;
        $save['name']       = $post['name'];
        $save['hp']         = $post['hp'];
        $save['address']    = $post['address'];
        $save['created']    = date('Y-m-d H:i:s');
        $db->table('account_customer')->insert($save);
        $idnya = $db->insertID();
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Pelanggan telah ditambahkan',
            'idnya' => $idnya
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
        $variant = $db->table('product_variant pv')->join('product_price pp','pp.var=pv.id')->where(['pv.id'=>$_POST['variant']])->select('pv.*,pp.price')->get()->getRowArray();
        // Tambahkan ke cart
        $userid = usertoken($_SESSION['usertoken']);
        $c = $db->table('cart')->where(['status'=>'carting','user'=>$userid,'product'=>$idproduct,'variant'=>$_POST['variant']])->get()->getRowArray();
        if(isset($c['id'])){
            $qty = $c['qty'] + $_POST['qty'];
            $db->table('cart')->set(['qty'=>$qty,'price'=>$variant['price'],'subtotal'=>($variant['price'] * $qty)])->where(['id'=>$c['id']])->update();
        } else {
            $db->table('cart')->insert([
                'user'      => $userid,
                'store'     => $product['store'],
                'product'   => $idproduct,
                'variant'   => $variant['id'],
                'qty'       => $_POST['qty'],
                'price'     => $variant['price'],
                'subtotal'  => ($variant['price'] * $_POST['qty']),
                'status'    => 'carting',
                'created'   => date('Y-m-d H:i:s')
            ]);
        }

        
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
        /*
        $cart[] = [
            'product_id' => $idproduct,
            'variant_id' => $variant['id'],
            'qty'        => $_POST['qty'],
            'name'       => $product['name'],
            'variant'    => $variant['var1'].($variant['var2']?' - '.$variant['var2']:'') ?? '',
            'price'      => $variant['price'] ?? 0,
            'subtotal'   => ($variant['price'] ?? 0)*$variant['id']
        ];
        */

        // Simpan ke session
        $session = session();   // ← WAJIB ADA
        $session->set('cart', $cart['items']);

        // 👍 Return cart baru
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Masuk keranjang',
            'cart' => $cart
        ]);

    }

    public function dec($cartid){
        $db = db_connect();
        $cc = $db->table('cart a')->join('product_variant b','b.id=a.variant')->where(['a.id'=>$cartid])->select('a.*,b.stock')->get()->getRowArray();
        if (isset($cc['qty'])){if($cc['qty']>1){$qty=$cc['qty']-1;$db->table('cart')->where(['id'=>$cartid])->set(['qty'=>$qty])->update();}else{$db->table('cart')->where(['id'=>$cartid])->delete();}}


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
        $session = session();   // ← WAJIB ADA
        $session->set('cart', $cart['items']);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Jumlah Produk Dikurangi',
            'cart' => $cart
        ]);
    }
    public function inc($cartid){
        $db = db_connect();$cc = $db->table('cart a')->join('product_variant b','b.id=a.variant')->where(['a.id'=>$cartid])->select('a.*,b.stock')->get()->getRowArray();
        if (isset($cc['qty'])){if($cc['qty']<$cc['stock']){$qty=$cc['qty']+1;$db->table('cart')->where(['id'=>$cartid])->set(['qty'=>$qty])->update();}else{$db->table('cart')->where(['id'=>$cartid])->set(['qty'=>$cc['stock']])->update();}}


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
        $session = session();   // ← WAJIB ADA
        $session->set('cart', $cart['items']);
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Jumlah Produk Ditambah',
            'cart' => $cart
        ]);
    }

    public function produk($pelanggan=false){
        $db = db_connect();
        $data = array();
        
        $request = service('request');
        $data = $request->getJSON(true); // true = array
        $usertoken = $data['usertoken'] ?? null;
        $userty = $data['userty'] ?? null;

        if(isset($_GET['q']) && !empty($_GET['q'])){
            $like['product.name'] = $_GET['q'];
        } else { $like = array(); }
        $userid = usertoken($usertoken);
 

        // Tentukan account type
        if ($pelanggan == false) {
            $pl = $db->table('account_type')->where(['sales'=>1])->orderBy('default DESC')->get()->getRowArray();
            $atype      = $pl['id'] ?? 1;
        } else {
            $pl = $db->table('account_customer')->where(['id'=>$pelanggan])->get()->getRowArray();
            $atype      = $pl['type']??1;
        }

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
                pp.price
            ")
            ->join('product_variant v', 'v.product = product.id', 'left')
            ->join('product_price pp', "pp.product = product.id AND pp.var = v.id AND pp.account_type = {$atype}", 'left');
            // tambahkan join berdasarkan userty
            if ($userty == 'owner') {
                $builder->join('account_company ac', 'ac.id = product.company')->where('ac.owner', $userid);
                $asp        = $db->table('account_company')->where('owner', $userid)->get()->getRowArray();
                $company    = $asp['id'] ?? 4;
            } else {
                $builder->join('account_store_privilage asp', 'asp.store = product.store')->where('asp.account', $userid);
                
                $asp        = $db->table('account_store_privilage')->where('account', $userid)->get()->getRowArray();
                $company    = $asp['company'] ?? 4;
            }
        $query = $builder
            ->like($like) 
            ->orderBy('product.id')
            ->orderBy('v.default', 'DESC')   // default variant berada paling atas
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
                    'harga'    => $row['price'],
                    'stok'     => $row['stock'],
                    'sku'      => $row['sku'],
                    'idvarpro' => $row['variant_id'],
                    'variasi'  => []
                ];
            }

            // Tambahkan variasi
            $result[$pid]['variasi'][] = [
                'id'    => $row['variant_id'],
                'v1'    => $row['var1'],
                'v2'    => $row['var2'],
                'sku'   => $row['sku'],
                'harga' => $row['price'],
                'stok'  => $row['stock'],
                'min'   => $row['minstock'],
            ];
            
            // Set variant default
            if ($row['variant_default'] == 1) {
                $result[$pid]['harga']   = $row['price'];
                $result[$pid]['stok']    = $row['stock'];
                $result[$pid]['sku']     = $row['sku'];
                $result[$pid]['idvarpro']= $row['variant_id'];
                $result[$pid]['kode']    = $row['sku'];
            }
        }

        $data = array_values($result);
        $ret['data']    = $data;


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
        $category = $db->table('product_category')->where(['company'=>$company])->get()->getResultArray();

        $ret['category']    = $category;
        $ret['discount']    = $discount;
        $ret['cart']        = $cart;
        $ret['ok']          = true;
        return $this->response->setJSON($ret);
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
                $save['company']    = $p['id'];
            } elseif ($_SESSION['userty']=='employee'){
                $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
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
            $like['name'] = $_GET['q'];
        } else { $like = array(); }
        $userid = usertoken($_SESSION['usertoken']);
        if($_SESSION['userty']=='owner'){
            $p = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
            $dat = $db->table('account_customer')->where(['company'=>$p['id']])->like($like)->get()->getResultArray();
            $typ = $db->table('account_type')->where(['company'=>$p['id'],'sales'=>1])->orderBy('default DESC')->get()->getResultArray();
        } elseif ($_SESSION['userty']=='employee'){
            $p = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
            $dat = $db->table('account_customer')->where(['store'=>$p['store']])->like($like)->get()->getResultArray();
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
        $db->table('cart')->where(['user'=>$userid,'status'=>'carting'])->delete();
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