<?php
namespace App\Controllers;
use App\Helpers\global_helper; 
date_default_timezone_set("Asia/Bangkok");
class Resto_ extends BaseController{
    public function __construct() {
        $session = \Config\Services::session(); 
        helper(['global', 'upload', 'text']);
    }
    public function index(){
        $data = array();
        $db = db_connect(); 
        echo frontend('home',$data);
    }

    public function setharga(){
        $db = db_connect();
        $product = $_POST['product'];
        $variant = $_POST['variant'];
        $unit = $_POST['unit'];
        $type = $_POST['type'];
        $price = $_POST['price'];
        $c = $db->table('product_price')->where(['product'=>$product,'var'=>$variant,'unit'=>$unit,'account_type'=>$type])->get()->getRowArray();
        if (isset($c['id'])) {
            $db->table('product_price')->where(['id'=>$c['id']])->set(['price'=>$price,'updated'=>date('Y-m-d H:i:s')])->update();
        } else {
            $db->table('product_price')->insert([
                'product'   => $product,
                'var'   => $variant,
                'unit'   => $unit,
                'account_type'   => $type,
                'price'   => $price,
                'created'=>date('Y-m-d H:i:s'),
                'updated'=>date('Y-m-d H:i:s')
            ]);
        }
        $ret['status']      = true;
        $ret['message']     = 'harga sudah di rubah';
        echo json_encode($ret);
    }

    public function simpan(){ 
        $db = db_connect();
        $ret['status'] = false;
        $product = $_POST['product'];

        $foto = uploadfile('toko', 'foto');
        if ($foto !== false) {$save['foto'] = $foto;}
        $product['publish']     = (isset($product['publish'])?1:0);
        if (isset($_POST['id'])&&$_POST['id']!=0&&$_POST['id']!='') {
            $idnya = dekripsi($_POST['id']);
            $c = $db->table('product')->where(['id'=>$idnya])->select('id')->get()->getRowArray();
            $idproduct = $idnya;
        } else {
                $store = $db->table('account_store')->where(['id'=>$product['store']])->get()->getRowArray();
                if (isset($store['company'])) {
                    $product['company']     = $store['company'];
                    $product['owner']     = $store['owner'];
                }
                if (!empty($_POST['variant1'])) {
                    $arr1 = json_decode($_POST['variant1'], true); // decode to array
                    $result1 = implode(';', array_column($arr1, 'value'));
                    $product['variant1']   = $result1;
                }
                if (!empty($_POST['variant2'])) {
                    $arr2 = json_decode($_POST['variant2'], true); // decode to array
                    $result2 = implode(';', array_column($arr2, 'value'));
                    $product['variant2']   = $result2;
                }
                $product['created']    = date('Y-m-d H:i:s');
                $db->table('product')->insert($product);
                $idproduct = $db->insertID();
                $fp = explode(';', $_POST['fotoproduk']);
                if (isset($fp[0])) {
                    foreach($fp as $k=>$v){
                        if (!empty($v)) {
                            $db->table('product_file')->where(['id'=>$v])->set(['product'=>$idproduct,'status'=>'ok'])->update();
                        }
                    }
                }

                if (isset($_POST['variant'])) {
                    foreach ($_POST['variant'] as $vari1 => $sizes) {          // putih, hitam
                        foreach ($sizes as $vari2 => $data) {           // s, m, l
                            if ($vari2=='-') {
                                $vari['var']        = 1;
                                $vari['name']       = $product['name'].' - '.$vari1;
                            } else {
                                $vari['var']        = 2;
                                $vari['name']       = $product['name'].' - '.$vari1.' '.$vari2;
                            }
                            $vari['var1']       = $vari1;
                            $vari['var2']       = $vari2;
                            $vari['product']    = $idproduct;

                            $vari['sku']        = $data['sku'];
                            $vari['foto']       = $data['foto'];
                            $vari['stock']      = $data['stok'];
                            $vari['minstock']   = $data['min'];
                            $vari['base_price'] = $data['base_price'];
                            $vari['created']    = date('Y-m-d H:i:s');
                            $vari['minbuy']     = 0;


                            $optionsJson = $data['option'] ?? '[]';
                            $options = json_decode($optionsJson, true);
                            $label = '';$nn=0;
                            if (isset($options)&&!empty($options)) {
                                foreach ($options as $opt) {
                                    $label .= ($nn==0?'':';').$opt['value']; $nn++;
                                }
                            }
                            $vari['options']    = $label;

                            $db->table('product_variant')->insert($vari);
                            $idvariant = $db->insertID();

                            if (isset($data['harga'])) {
                                // harga has 2 levels
                                foreach($data['harga'] as $kkk=>$vvv){
                                    $varprice['product']        = $vari['product'];
                                    $varprice['account_type']   = $kkk;
                                    $varprice['var']            = $idvariant;
                                    $varprice['min_qty']        = $vari['minbuy'];
                                    $varprice['price']          = $vvv;
                                    $varprice['created']        = date('Y-m-d H:i:s');
                                    $db->table('product_price')->insert($varprice);
                                }
                            }
                            if (isset($data['composition'])) {
                                foreach($data['composition'] as $kkk=>$vvv){
                                    $varprice['variant_id']     = $idvariant;
                                    $varprice['component_id']   = $vvv['component_id'];
                                    $varprice['qty']            = $vvv['qty'];
                                    $db->table('product_recipe')->insert($varprice);
                                }
                            }
                            //$harga_1 = $data['harga'][1] ?? 0;
                            //$harga_2 = $data['harga'][2] ?? 0;

                        }
                    }
                } else {
                    $vari['var1']       = '';
                    $vari['var2']       = '';
                    $vari['product']    = $idproduct;
                    $vari['name']       = $product['name'];
                    $vari['sku']        = str_replace(' ','_',$product['name']);
                    $vari['foto']       = $product['cover'];
                    $vari['stock']      = 0;
                    $vari['minstock']   = 10;
                    $vari['base_price'] = 0;
                    $vari['created']    = date('Y-m-d H:i:s');
                    $vari['minbuy']     = 0;

                    $db->table('product_variant')->insert($vari);
                    $idvariant = $db->insertID();

                    //$ret['status']  = false;
                    //$ret['message'] = 'Varian harus di isi terlebih dahulu';
                    //echo json_encode($ret);
                    //return; // ⛔ stop total
                }

                $names = [];
                $multiplier = 1;
                $db->table('product_units')->insert([
                    'product_id'    => $idproduct,
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
                    $uni['product_id']   = $idproduct;
                    $uni['unit_name']    = $unitName;
                    $uni['multiplier']   = $multiplier;
                    $uni['is_base']      = 0;
                    $db->table('product_units')->insert($uni);
                }
            
                $ret['status']      = true;
                $ret['message']     = 'Produk baru telah ditambahkan';

                
        }

        echo json_encode($ret);
    }

    public function listfoto(){
        $db = db_connect();
        $list = explode(';', $_POST['list']);
        $ret['status']  = false;
        if (isset($list[0])) {
            $data = array();
            foreach ($list as $key => $v) {
                $fi = $db->table('product_file')->where(['id'=>$v])->get()->getRowArray();
                if (isset($fi['file'])) {
                    $rr['id']   = $fi['id'];
                    $rr['file']   = $fi['file'];
                    array_push($data, $rr);
                    $ret['status']  = true;
                }
            }
            $ret['data']    = $data;
        }
        echo json_encode($ret);
    }

    public function daftar_bahan($storeid){
        $db = db_connect();
        if (isset($_GET['q'])&&!empty($_GET['q'])) {
            $c = $db->table('product_component pc')->join('product_component_units pcu','pcu.component_id=pc.id')->where(['pc.store'=>$storeid])->like('pc.name',$_GET['q'])->orderBy('is_base DESC')->select('pc.id,pc.name,pcu.unit_name unit')->get()->getResultArray();
        } else {
            $c = $db->table('product_component pc')->join('product_component_units pcu','pcu.component_id=pc.id')->where(['pc.store'=>$storeid])->orderBy('is_base DESC')->select('pc.id,pc.name,pcu.unit_name unit,pcu.id unitid')->get()->getResultArray();
        }
        return $this->response->setJSON($c);
    }

    public function ajaxList(){
        $page   = (int) ($this->request->getGet('page') ?? 1);
        $limit  = (int) ($this->request->getGet('limit') ?? 10);
        $search = trim($this->request->getGet('search'));

        $offset = ($page - 1) * $limit;

        $data  = $this->getProduk($limit, $offset, $search);
        $total = $$this->countProduk($search);

        return $this->response->setJSON([
            'data'  => $data,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit
        ]);
    }

    public function getProduk($limit, $offset, $search = '')
    {
        $db = db_connect();
        $qb = $db->table('product p')
            ->select('p.id, p.name, p.description, p.status, p.created, pf.file as cover')
            ->join('product_file pf', 'pf.id = p.cover', 'left')
            ->where('p.status', 'active');

        if ($search !== '') {
            $qb->groupStart()
                ->like('p.name', $search)
                ->orLike('p.description', $search)
            ->groupEnd();
        }

        return $qb->orderBy('p.created', 'DESC')
                  ->limit($limit, $offset)
                  ->get()
                  ->getResultArray();
    }

    public function countProduk($search = '')
    {
        $db = db_connect();
        $qb = $db->table('product p')
            ->select('p.id, p.name, p.description, p.status, p.created, pf.file as cover')
            ->join('product_file pf', 'pf.id = p.cover', 'left')
            ->where('p.status', 'active');

        if ($search !== '') {
            $qb->groupStart()
                ->like('p.name', $search)
                ->orLike('p.description', $search)
            ->groupEnd();
        }

        return $qb->countAllResults();
    }


    public function produkstok(){
        $db = db_connect();

        $userid   = (int) usertoken($_SESSION['usertoken']);
        $usertype = session('userty');

        $search = $_GET['search']['value'] ?? '';
        $order  = $_GET['order'][0] ?? null;
        $start  = (int) ($_GET['start'] ?? 0);
        $length = (int) ($_GET['length'] ?? 10);

        $join  = "";
        $where = "WHERE pv.status = 'active'";

        if ($usertype === 'owner') {
            $join .= " JOIN account_company ac ON ac.id = p.company ";
            $where .= " AND ac.owner = {$userid}";
        } else {
            $join .= " JOIN account_store_privilage asp ON asp.store = p.store ";
            $where .= " AND asp.account = {$userid}";
        }

        if ($search) {
            $search = esc($search);
            $where .= " AND (
                p.name LIKE '%{$search}%'
                OR pv.name LIKE '%{$search}%'
                OR pv.sku LIKE '%{$search}%'
            )";
        }

        // sorting whitelist
        $columns = [
            0 => 'p.name',
            1 => 'pv.name',
            2 => 'pv.sku',
            3 => 'pv.stock',
            4 => 'pv.minstock'
        ];

        $orderSql = "ORDER BY pv.stock ASC";
        if ($order) {
            $col = $columns[$order['column']] ?? 'pv.stock';
            $dir = ($order['dir'] === 'desc') ? 'DESC' : 'ASC';
            $orderSql = "ORDER BY {$col} {$dir}";
        }

        $sql = "
            SELECT
                p.name AS product_name,
                pv.name AS variant_name,
                pv.sku,
                pv.stock,
                pv.minstock
            FROM product_variant pv
            JOIN product p ON p.id = pv.product
            {$join}
            {$where}
            {$orderSql}
            LIMIT {$start}, {$length}
        ";

        $data = $db->query($sql)->getResultArray();

        $total = $db->query("
            SELECT COUNT(*) total
            FROM product_variant pv
            JOIN product p ON p.id = pv.product
            {$join}
            {$where}
        ")->getRow()->total;

        return $this->response->setJSON([
            'draw' => intval($_GET['draw'] ?? 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    

    public function stokbahan(){
        $db = db_connect();

        $userid   = (int) usertoken($_SESSION['usertoken']);
        $usertype = session('userty');

        $search = $_GET['search']['value'] ?? '';
        $order  = $_GET['order'][0] ?? null;
        $start  = (int) ($_GET['start'] ?? 0);
        $length = (int) ($_GET['length'] ?? 10);

        $join  = "";
        $where = "WHERE pc.status = 'active'";

        if ($usertype === 'owner') {
            $join  .= " JOIN account_company ac ON ac.id = pc.company ";
            $where .= " AND ac.owner = {$userid}";
        } else {
            $join  .= " JOIN account_store_privilage asp ON asp.store = pc.store ";
            $where .= " AND asp.account = {$userid}";
        }

        if ($search) {
            $search = esc($search);
            $where .= " AND (
                pc.name LIKE '%{$search}%'
                OR pc.unit LIKE '%{$search}%'
            )";
        }

        // whitelist sorting
        $columns = [
            0 => 'pc.name',
            1 => 'pc.stock',
            2 => 'pc.minstock',
            3 => 'pc.unit'
        ];

        $orderSql = "ORDER BY pc.stock ASC";
        if ($order) {
            $col = $columns[$order['column']] ?? 'pc.stock';
            $dir = ($order['dir'] === 'desc') ? 'DESC' : 'ASC';
            $orderSql = "ORDER BY {$col} {$dir}";
        }

        $sql = "
            SELECT
                pc.name,
                pc.stock,
                pc.minstock,
                pc.unit,
                CASE 
                    WHEN pc.stock <= pc.minstock THEN 'danger'
                    ELSE 'normal'
                END AS status_stock
            FROM product_component pc
            {$join}
            {$where}
            {$orderSql}
            LIMIT {$start}, {$length}
        ";

        $data = $db->query($sql)->getResultArray();

        $total = $db->query("
            SELECT COUNT(*) total
            FROM product_component pc
            {$join}
            {$where}
        ")->getRow()->total;

        return $this->response->setJSON([
            'draw' => intval($_GET['draw'] ?? 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }



}