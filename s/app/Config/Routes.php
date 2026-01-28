<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->add('/', 'Admin::dashboard');

$routes->add('a/(:any)',                           		'A::$1'); 
$routes->add('a/(:any)/(:any)',                        'A::$1/$2'); 
$routes->add('a/(:any)/(:any)/(:any)',                 'A::$1/$2/$3'); 


$routes->get('login', 			'Admin::login'); // Akses halaman login (GET) → menampilkan form login
$routes->post('login', 			'Admin_::login'); // Submit login (POST) → memproses data login

$routes->get('qr', 'Qr::url');
$routes->get('qr/(:any)', 'Qr::url/$1');


$routes->add('uploadfotoproduk',                       	'File::upload_foto_produk');  
$routes->add('uploadfotoproduk/(:any)',                	'File::upload_foto_produk/$1');  

//$routes->add('login',                                	'Admin::login');  
$routes->add('logout',                                	'Admin::logout');  
$routes->add('signout',                                	'Admin::logout');
 
//$routes->add('kasir',                                	'Admin::kasir');
$routes->add('beranda',                                	'Admin::beranda');

$routes->post('laporan/toko/(:any)', 					'LaporanToko_::$1');
$routes->get('laporan/toko/(:any)', 					'LaporanToko_::$1');

$routes->add('laporan/(:segment)', 						'Laporan::$1');
$routes->add('laporan/(:segment)/(:any)', 				'Laporan::$1/$2');
$routes->add('laporan/(:segment)/(:any)/(:any)', 			'Laporan::$1/$2/$3');
$routes->match(['get','post'], 'laporan/(:segment)/(:any)', 'Laporan_::$1/$2');
$routes->post('laporan/(:any)', 						'Laporan_::$1');
$routes->post('laporan/(:any)/(:any)', 					'Laporan_::$1/$2');
$routes->post('laporan/(:any)/(:any)/(:any)', 			'Laporan_::$1/$2/$3');
$routes->post('laporan/(:any)/(:any)/(:any)/(:any)', 	'Laporan_::$1/$2/$3/$4');


$routes->add('kasir/masukankeranjang/(:any)', 			'Cashier_::masukankeranjang/$1');
$routes->post('kasir/masukankeranjang/(:any)', 			'Cashier_::masukankeranjang/$1');
$routes->post('kasir/dec/(:any)', 						'Cashier_::dec/$1');
$routes->post('kasir/inc/(:any)', 						'Cashier_::inc/$1');
$routes->post('kasir/pelanggan_baru', 					'Cashier_::pelanggan_baru');
$routes->post('kasir/checkout', 						'Cashier_::checkout');
$routes->post('kasir/diskon_baru', 						'Cashier_::discount_add');
$routes->get('kasir/data_diskon', 						'Cashier_::discount_list');
$routes->post('kasir/clear', 							'Cashier_::clear');
$routes->get('kasir/review', 							'Cashier_::review');
$routes->get('kasir/print/(:any)', 						'Cashier_::print/$1');
$routes->get('kasir/ubahpembeli/(:any)', 				'Cashier_::customerchange/$1');
$routes->get('kasir/produk/(:any)', 					'Cashier_::produk/$1');
$routes->get('kasir/clearcust', 						'Cashier_::clearcust');



$routes->add('kasir',                                	'Cashier::index');
$routes->get('kasir/harga/(:any)/(:any)/(:any)/(:any)', 'Cashier::harga/$1/$2/$3/$4');
$routes->get('kasir/tukar',                             'Cashier::tukar');
$routes->get('kasir/belanja',                           'Cashier::belanja');
$routes->get('kasir/retur',                             'Cashier::retur');
$routes->add('kasir_sebelumnya',                        'Cashier::befores');
$routes->get('kasir/grid',                        		'Cashier::grid');

$routes->get('kasir/daftar_pelanggan',                 	'Cashier_::pelanggan_list');



$routes->post('retur',                           		'Belanja_::retur');
$routes->post('tukar',                           		'Belanja_::tukar');

$routes->get('belanja/produk',                 			'Belanja_::produk');
$routes->get('belanja/daftar_supplier',                 'Belanja_::supplier_list');
$routes->add('belanja/masukankeranjang/(:any)', 		'Belanja_::masukankeranjang/$1');
$routes->post('belanja/masukankeranjang/(:any)', 		'Belanja_::masukankeranjang/$1');
$routes->post('belanja/dec/(:any)', 					'Belanja_::dec/$1');
$routes->post('belanja/inc/(:any)', 					'Belanja_::inc/$1');
$routes->post('belanja/supplier_baru', 					'Belanja_::supplier_baru');
$routes->post('belanja/variant_baru', 					'Belanja_::variant_baru');
$routes->post('belanja/checkout', 						'Belanja_::checkout');
$routes->post('belanja/clear', 							'Belanja_::clear');
$routes->get('belanja/review', 							'Belanja_::review');
$routes->get('belanja/print/(:any)', 					'Belanja_::print/$1');



$routes->add('kasir/(:segment)', 						'Cashier::$1');
$routes->add('kasir/(:segment)/(:any)', 				'Cashier::$1/$2');
$routes->add('kasir/(:segment)/(:any)/(:any)', 			'Cashier::$1/$2/$3');
$routes->match(['get','post'], 'kasir/(:segment)/(:any)', 'Cashier::$1/$2');



$routes->match(['get','post'], 'kasir/(:any)', 					'Cashier_::$1');
$routes->match(['get','post'], 'kasir/(:any)/(:any)', 			'Cashier_::$1/$2');
$routes->match(['get','post'], 'kasir/(:any)/(:any)/(:any)', 	'Cashier_::$1/$2/$3');

$routes->post('kasir/(:any)(.*)', 						'Cashier_::$1$2');
$routes->get('kasir/(:any)(.*)', 						'Cashier_::$1$2');

$routes->match(['get','post'], 'daftar_pelanggan', 					'Cashier_::pelanggan');
$routes->match(['get','post'], 'daftar_pelanggan/(:segment)/(:any)', 'Cashier_::pelanggan/$1/$2');

$routes->add('toko', 									'Store::detail');
$routes->add('toko/ubah/(:any)',						'Store::tambah/$1');
$routes->add('toko/(:any)(.*)', 						'Store::$1$2');
$routes->post('toko/(:any)(.*)', 						'Store_::$1$2');

$routes->add('petugas', 								'Worker::detail');
$routes->add('petugas/ubah/(:any)',						'Worker::tambah/$1');
$routes->add('petugas/(:any)(.*)', 						'Worker::$1$2');
$routes->post('petugas/(:any)(.*)', 					'Worker_::$1$2');

$routes->add('produk', 									'Product::detail');
$routes->add('produk/ubah/(:any)',						'Product::tambah/$1');
$routes->add('produk/(:any)(.*)', 						'Product::$1$2');
$routes->post('produk/(:any)(.*)', 						'Product_::$1$2');
$routes->post('produk/setharga', 						'Product_::setharga');
$routes->get('produk/ajax', 							'Product_::ajaxList');
$routes->get('produk/produkstok', 						'Product_::produkstok');
$routes->get('produk/datastokbahan', 					'Product_::stokbahan');


$routes->add('daftar_bahan/(:any)',						'Product_::daftar_bahan/$1');
$routes->get('daftar_bahan/(:any)',						'Product_::daftar_bahan/$1');


$routes->add('pengaturan', 									'Pengaturan::detail');
$routes->add('pengaturan/ubah/(:any)',						'Pengaturan::tambah/$1');
$routes->add('pengaturan/(:any)(.*)', 						'Pengaturan::$1$2');
$routes->post('pengaturan/(:any)(.*)', 						'Pengaturan_::$1$2');

$routes->add('admin',                                	'Admin::index');


$routes->add('resto/(:any)',                           	'Resto::index/$1'); 
$routes->add('resto/(:any)/(:any)',                    	'Resto::$1/$2'); 
$routes->add('resto/(:any)/(:any)/(:any)',             	'Resto::$1/$2/$3'); 
$routes->add('resto/(:any)/(:any)/(:any)/(:any)',      	'Resto::$1/$2/$3/$4'); 

$routes->add('notif/(:any)',                           	'Notif::$1'); 
$routes->add('notif/(:any)/(:any)',                    	'Notif::$1/$2'); 
$routes->add('notif/(:any)/(:any)/(:any)',             	'Notif::$1/$2/$3'); 
$routes->add('notif/(:any)/(:any)/(:any)/(:any)',      	'Notif::$1/$2/$3/$4'); 

$routes->add('admin/(:any)',                           	'Admin::$1'); 
$routes->add('admin/(:any)/(:any)',                    	'Admin::$1/$2'); 
$routes->add('admin/(:any)/(:any)/(:any)',             	'Admin::$1/$2/$3'); 
$routes->add('admin/(:any)/(:any)/(:any)/(:any)',      	'Admin::$1/$2/$3/$4'); 

$routes->add('admin_/(:any)',                           	'Admin_::$1'); 
$routes->add('admin_/(:any)/(:any)',                    	'Admin_::$1/$2'); 
$routes->add('admin_/(:any)/(:any)/(:any)',             	'Admin_::$1/$2/$3'); 
$routes->add('admin_/(:any)/(:any)/(:any)/(:any)',      	'Admin_::$1/$2/$3/$4'); 


$routes->add('a/(:any)',                           	'A::$1'); 
$routes->add('a/(:any)/(:any)',                    	'A::$1/$2'); 
$routes->add('a/(:any)/(:any)/(:any)',             	'A::$1/$2/$3'); 
$routes->add('a/(:any)/(:any)/(:any)/(:any)',      	'A::$1/$2/$3/$4'); 