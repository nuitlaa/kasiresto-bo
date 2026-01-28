<?php
date_default_timezone_set("Asia/Bangkok");
use App\Models\DesaModel;
use App\Models\BeritaModel;
use App\Models\CatModel;
function privilage($what){
	switch($what){
		case 'waiter': return 'Pelayan'; break;
		case 'chef': return 'Dapur'; break;
		case 'cashier': return 'Petugas Kasir'; break;
		case 'storekeeper': return 'Petugas Gudang'; break;
		case 'employee': return 'Pegawai Lainnya'; break;
		case 'finance': return 'Keuangan'; break;
		case 'manager': return 'Manager'; break; 
		case 'chief': return 'Kepala Toko'; break;
		default : return $what; break;
	}
}
function pref($func,$default="list"){
	$db = db_connect();
	$a = $db->table('preference')->where(['user'=>$_SESSION['userid'],'func'=>$func])->getRowArray();
	if (isset($a['value'])) { return $a['value']; }
	$db->table('preference')->insert(['user'=>$_SESSION['userid'],'func'=>$func,'value'=>$default]);
	return $default;
}
function clean_string($string){
    $string = strtolower($string);
    $string = trim($string);
    $string = preg_replace('/[^a-z0-9\s]/', '', $string);
    $string = preg_replace('/\s+/', '_', $string);
    return $string;
}
function harga($a){
	$price_raw = $a ?? '';
	$price = preg_replace('/[^0-9]/', '', $price_raw);
	return (int) $price;
}
function icon($name){
    $db = db_connect();
    $a = $db->table('icons')->where(['name'=>$name])->get()->getRowArray();
    return isset($a['icon'])?$a['icon']:$name;
}  
function companyid($userid){
    $db = db_connect();
	$a = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('b.id company_id')->where('a.id', $userid)->get()->getRowArray();
	return isset($a['company_id'])?$a['company_id']:false;
}
function storeid($userid){
    $db = db_connect();
	$a = $db->table('account a')->join('account_store_privilage b','b.account=a.id', 'left')->select('b.id store')->where('a.id', $userid)->get()->getRowArray();
	return isset($a['store'])?$a['store']:false;
}
function saldopiutang($supplier){
	$db = db_connect();
	$a = $db->table('account_supplier_lent');
	$a->where(['supplier'=>$supplier]);
	$a->orderBy('id DESC');
	$b = $a->get()->getRowArray();
	return isset($b['akhir'])?$b['akhir']:0;
}
function saldohutang($cust){
	$db = db_connect();
	$a = $db->table('account_customer_debt');
	$a->where(['customer'=>$cust]);
	$a->orderBy('id DESC');
	$b = $a->get()->getRowArray();
	return isset($b['akhir'])?$b['akhir']:0;
}
function uang($v){
    if (!is_numeric($v)) return $v;
    return number_format($v, 0, ',', '.');
}
function unuang($v){
    return preg_replace('/[^0-9]/', '', $v);
}

function enkripsi(string $plaintext): string {
    $key = hex2bin($_SESSION['usertoken'].md5(md5(env('webunik'))));
    $iv = random_bytes(12); // 96-bit recommended for GCM
    $tag = "";
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, "", 16);
    // store iv + tag + ciphertext
    return base64_encode($iv . $tag . $ciphertext);
}

function dekripsi(string $b64): ?string {
    $key = hex2bin($_SESSION['usertoken'].md5(md5(env('webunik'))));
    $raw = base64_decode($b64);
    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $ciphertext = substr($raw, 28);
    $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return $plaintext === false ? null : $plaintext;
}

function mycompanyid(){
    $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
    $user       = $db->table('account_company')->select('id company_id')->where('owner', $userid)->get()->getRowArray();
    return isset($user['company_id'])?$user['company_id']:false;
}

function mystoreid(){
    $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
    $user       = $db->table('account_store_privilage')->select('store store_id')->where('account', $userid)->get()->getRowArray();
    return isset($user['store_id'])?$user['store_id']:false;
}
function frontend($page, $data=false){
	$ret = '';
	$ret .= view('f/template/header',$data);
	$ret .= view('f/'.$page,$data);
	$ret .= view('f/template/footer',$data);
	return $ret;
}
function backend($page, $data=false){
	$ret = '';
	$ret .= view('b/headfoot/head',$data);
	$ret .= view('b/'.$page,$data);
	$ret .= view('b/headfoot/foot',$data);
	return $ret;
}  

function sys($func,$value=''){
	$db = db_connect();
	$a = $db->table('system');
	$a->where(['func'=>$func]);
	$b = $a->get()->getRowArray();
	if (isset($b['value'])) {return $b['value'];} else {
		$c = $db->table('system');
		$c->insert(['func'=>$func,'value'=>$func]);
		return $func;
	}
}
 
function usertoken($usertoken=false,$select='id'){
	$db = db_connect();

	$a = $db->table('account_login');
	$a->where(['token'=>$usertoken,'status'=>'active']);
	$a->select('user,expired');
	$arr = $a->get()->getRowArray();

	$ret['status'] 	= false;
	if (isset($arr['user'])) {
	    if($arr['expired']<date('Y-m-d H:i:s')){ 
	        $ret['status']  = false;
	        $ret['message'] = 'token telah ulang';
	        return "{token telah usang}";
	    } else {
    		$b = $db->table('account');
    		$b->where(['id'=>$arr['user']]);
    		$b->select($select);
    		$brr = $b->get()->getRowArray();
    		if(isset($brr)){
    		    return $brr[$select];
    			$ret['status'] 	= true;
    			$ret['message'] = 'user found';
    			$ret 						= $brr;
    		} else { 
    		    return "{user tidak ditemukan}";
    			$ret['message'] 	= 'user tidak ditemukan';
    		}
	    }
	} else { 

		$a = $db->table('account');
		$a->where(['id'=>$usertoken]);
		$a->select('id');
		$arr = $a->get()->getRowArray();

		$ret['status'] 	= false;
		if (isset($arr['id'])) {
		    return $arr['id'];
			$ret['status'] 	= true;
			$ret['message'] = 'user found';
			$ret 			= $arr;
		} else {
		    return "{token tidak ditemukan}";
			$ret['message'] 	= 'token tidak ditemukan';
		}
	}
	return $ret;
}
   

function passcreate($password) {
    // PEPPER: simpan di .env atau environment variable server!
    $pepper = $_ENV['PEPPER_KEY'] ?? 'Red_Chilli_Carolina_Pepper';

    // Gabungkan password + pepper
    $pwd_peppered = hash_hmac("sha256", $password, $pepper);

    // Hash kuat Argon2ID
    return password_hash($pwd_peppered, PASSWORD_ARGON2ID, [
        'memory_cost' => 1<<17,  // 128MB
        'time_cost'   => 4,
        'threads'     => 2,
    ]);
}
function passverify($password, $hashed) {
    $pepper = $_ENV['PEPPER_KEY'] ?? 'Red_Chilli_Carolina_Pepper';
    $pwd_peppered = hash_hmac("sha256", $password, $pepper);
    return password_verify($pwd_peppered, $hashed);
}


function created($datetime){
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $tanggal = date('j', strtotime($datetime)); // tanpa leading zero
    $bulanAngka = date('n', strtotime($datetime));
    $tahun = date('Y', strtotime($datetime));
    $jam = date('G:i', strtotime($datetime)); // jam tanpa nol depan

    return $tanggal . ' ' . $bulan[$bulanAngka] . ' ' . $tahun . ', ' . $jam;
}


function getlink($func,$id){
	$db = db_connect(); $a = $db->table('linker'); $a->where(['func'=>$func,'tableid'=>$id]);$b=$a->get()->getRowArray();return isset($b['link'])?site_url($b['link']):'javascript:;';
}  
function nowa($a){
	$a = str_replace('+', '', $a);
	$a = str_replace(' ', '', $a);
	$a = str_replace('.', '', $a);
	$a = str_replace('-', '', $a);
	$b = str_split($a);
	if ($b[0]=='0') {
		$wa = '62';
		for ($i=0; $i < count($b); $i++) { 
			if ($i!=0) {
				$wa .= $b[$i];
			}
		}
	} else {
		$wa = '';
		for ($i=0; $i < count($b); $i++) { 
			if ($i!=0) {
				$wa .= $b[$i];
			}
		}
	}
	return $wa;
}

function saldo($faskes=1){
	$db = db_connect();
	$a = $db->table('layanan_io');
	$a->where(['status'=>'ok','faskes'=>$faskes]);
	$a->orderBy('id DESC');
	$b = $a->get()->getRowArray();
	return isset($b['akhir'])?$b['akhir']:0;
}

function normalize($a, $digit=0){
	$a = str_replace('Rp', '', $a);
	$a = str_replace('_', '', $a);
	$a = str_replace('.', '', $a);
	$a = str_replace(' ', '', $a);
	$a = $a + 0;
	$a = number_format((float)$a, 0, '.', '');
	return $a;
}

function when($date){
	$t = explode(' ', $date);
	if (isset($t[1])) {
		$tgl = $t[0];
	} else {
		$tgl = $date;
	}
	$now = time(); // or your date as well
	$your_date = strtotime($tgl);
	$datediff = $now - $your_date;

	$ret = round($datediff / (60 * 60 * 24));
	if ($ret>=7) {
		return format_tanggalku2($tgl);			
	} elseif ($ret<=1) {
		if (isset($t[1])) {
			$time = explode(':', $t[1]);
			$h1 = date('H');
			$h2 = $time[0];
			$m1 = date('i');
			$m2 = $time[1];
			$s1 = date('s');
			$s2 = $time[2];
			if (($h1-$h2)>=1) {
				$return = $h1-$h2;
				return $return.' hour ago';
			} else {
				if (($m1-$m2)>=1) {
					$return = $m1-$m2;
					return $return.' minute ago';
				} else {
					$return = $s1-$s2;
					return $return.' second ago';
				}
			}
		} else {
			return format_tanggalku2($tgl);			
		}
	} else {
		return $ret.' day ago';
	}
}
  
function bulan($a){
	$a = $a+0;
	if (is_int($a)) {
		switch(($a+0)){
			case 1 : return 'Januari'; break;
			case 2 : return 'Februari'; break;
			case 3 : return 'Maret'; break;
			case 4 : return 'Aprile'; break;
			case 5 : return 'Mei'; break;
			case 6 : return 'Juni'; break;
			case 7 : return 'Juli'; break;
			case 8 : return 'Agustus'; break;
			case 9 : return 'September'; break;
			case 10 : return 'Oktober'; break;
			case 11 : return 'November'; break;
			case 12 : return 'Desember'; break;
		}
	} else {
		return $a;
	}
}
 
function hp($a){
	$a = str_replace(' ','',$a);
	$a = str_replace('-','',$a);
	$a = str_replace('+','',$a);
	$b = str_split($a);
	if (isset($b[0])) {
		$hp = ''; $i=0;
		if ($b[0]=='6'&&$b[1]=='2') {
			foreach($b as $k=>$v){
				if ($k==0) {} elseif ($k==1) {} else {
					$c[$i] = $v; $i++;
				} 
			}
		} else {
			foreach($b as $k=>$v){
				$c[$i] = $v; $i++;
			}
		}
		foreach($c as $k=>$v){
			if ($k==0) {
				if ($v==0) {}
				elseif ($v=='0'){}
				else {
					$hp .= $v;
				}
			} else {
				$hp .= $v;
			}
		}
		return $hp;
	} else { return $a; }
}

function hpwa($a){
	$a = str_replace(' ','',$a);
	$a = str_replace('-','',$a);
	$a = str_replace('+','',$a);
	$b = str_split($a);
	if (isset($b[0])) {
		$hp = ''; $i=0;
		if ($b[0]=='6'&&$b[1]=='2') {
			foreach($b as $k=>$v){
				if ($k==0) {} elseif ($k==1) {} else {
					$c[$i] = $v; $i++;
				} 
			}
		} else {
			foreach($b as $k=>$v){
				$c[$i] = $v; $i++;
			}
		}
		foreach($c as $k=>$v){
			if ($k==0) {
				if ($v==0) {}
				elseif ($v=='0'){}
				else {
					$hp .= $v;
				}
			} else {
				$hp .= $v;
			}
		}
		return '62'.$hp;
	} else { return $a; }
}
 
function sisahari($bts){ 
	$batas = explode(' ', $bts);
	if (isset($batas[0])) {
		$now = time(); // or your date as well
		$your_date = strtotime($batas[0]);
		$datediff = $your_date-$now;
		return round($datediff / (60 * 60 * 24));
	} else { 
		$exp = explode('-', $bts);
		if (isset($exp[0])) {
			$now = time(); // or your date as well
			$your_date = strtotime($bts);
			$datediff = $your_date-$now;
			return round($datediff / (60 * 60 * 24));
		} else { return $bts; }
	} 
}
 

function transport($a){
	$db = db_connect();
	$b = $db->table('z_kec');
	$b->where(['id'=>$a]);
	$bb = $b->get()->getRowArray();
	if (isset($bb['transport'])) { return $bb['transport']; }
	else {
		$c = $db->table('z_kec');
		$c->where(['name'=>$a]);
		$c->like('kota_id','32','after');
		$cc = $c->get()->getRowArray();
		return isset($cc['transport'])?$cc['transport']:10000;
	}
}
  


function counting($table, $where=false, $like=false, $group=false){
	$db = db_connect();
	$b = $db->table($table);
	if ($where!=false && !empty($where)) { $b->where($where); } 
	if ($like!=false && !empty($like)) { $b->like($like); }
	if ($group!=false && !empty($group)) { $b->groupBy($group); }
	return $b->countAllResults();
}

function summing($table, $sumfield, $where=false, $like=false, $group=false){
	$db = db_connect();
	$b = $db->table($table);
	if ($where!=false && !empty($where)) { $b->where($where); } 
	if ($like!=false && !empty($like)) { $b->like($like); } 
	if ($group!=false && !empty($group)) { $b->groupBy($group); }
	$b->selectSum($sumfield);
	$query = $b->get()->getRowArray();
	return $query[$sumfield];
}