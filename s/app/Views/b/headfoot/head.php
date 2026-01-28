<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$compani = $db->table('account_company a')->join('account_store_privilage b','b.company=a.id')->where(['a.id'=>$_SESSION['company']])->select('a.*')->get()->getRowArray();
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type, b.foto company_foto,b.address company_address,b.phone company_phone')->where('a.id', $userid)->get()->getRowArray();
    $jumlah['toko'] = $db->table('account a')->join('account_store b', 'b.id = a.id')->where('a.id', $userid)->countAllResults();

    $prodk = $_SESSION['apptype']=='toko'?'Produk':'Menu'
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?=isset($app_name)?$app_name:sys('app-name')?></title>
		<meta charset="utf-8" />
		<meta name="description" content="<?=isset($meta_description)?$meta_description:sys('app-description')?>" />
		<meta name="keywords" content="<?=isset($meta_keyword)?$meta_keyword:sys('app-keyword')?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="<?=isset($meta_type)?$meta_type:'admin'?>" />
		<meta property="og:title" content="<?=isset($app_name)?$app_name:sys('app-name')?>" />
		<meta property="og:url" content="<?=site_url()?>" />
		<meta property="og:site_name" content="<?=sys('app-name')?>" /> 
		<link rel="shortcut icon" href="<?=base_url('f/'.sys('app-icon'))?>" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="<?=base_url('t/')?>/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="<?=base_url('t/')?>/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<script src="<?=base_url('t/')?>/plugins/global/plugins.bundle.js"></script>
		<script src="<?=base_url('t/')?>/js/scripts.bundle.js"></script>
		<style>
			.text-right {text-align: right;}
			td {
				vertical-align: middle;
			}
			th {
				text-align: center;
			}
			.menuicon {
				width: auto !important;
				padding-left: 10px !important;
				padding-right: 10px !important;
				color: whitesmoke !important;
			}

			.menu-sub-dropdown > .active > a {
				color: #009ef7 !important;
			}
			.coping {
				cursor: pointer;
			}
		</style>
	</head>
	<body id="kt_body" class="header-tablet-and-mobile-fixed aside-enabled">
		<div class="d-flex flex-column flex-root">
			<div class="page d-flex flex-row flex-column-fluid">
				 
				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper" style="padding-left: 0px !important;">
					<div id="kt_header" class="header header-bg">
						<div class="container-fluid">
							<div class="header-brand me-5">
								<div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show aside menu">
									<div class="btn btn-icon btn-active-color-primary w-30px h-30px" id="kt_aside_toggle">
										<span class="svg-icon svg-icon-1">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
												<path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
											</svg>
										</span>
									</div>
								</div>
								<a href="<?=site_url()?>" style="margin-right: 30px;">
									<img alt="Logo" src="<?=isset($compani['foto'])&&$compani['foto']!=''?base_url('f/'.$compani['foto']):base_url('f/'.sys('app-icon-text'))?>" class="h-25px h-lg-30px d-none d-md-block" />
									<img alt="Logo" src="<?=isset($compani['foto'])&&$compani['foto']!=''?base_url('f/'.$compani['foto']):base_url('f/'.sys('app-icon-text'))?>" class="h-25px d-block d-md-none" />
								</a>
								<div class="topbar d-flex align-items-stretch">
									<div class="d-flex align-items-center me-2 me-lg-4">
										<a href="<?=site_url('beranda')?>" class="menuicon btn btn-icon btn-borderless btn-active-primary <?=isset($menu)&&$menu=='beranda'?'active':'bg-white'?> bg-opacity-10" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
											<span class="svg-icon svg-icon-1 svg-icon-white">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
												  	<path d="M3 11.2L12 4L21 11.2V20C21 20.55 20.55 21 20 21H4C3.45 21 3 20.55 3 20V11.2Z" fill="currentColor"/>
												  	<path opacity="0.3" d="M9 21V14H15V21H9Z" fill="currentColor"/>
												</svg>
											</span> &nbsp;&nbsp;Beranda
										</a>
									</div>
									<div class="d-flex align-items-center me-2 me-lg-4">
										<a href="#" class="menuicon btn btn-icon btn-borderless btn-active-primary <?=isset($menu)&&$menu=='transaksi'?'active':'bg-white'?> bg-opacity-10" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
											<span class="svg-icon svg-icon-1 svg-icon-white">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
												  	<rect x="2" y="5" width="20" height="14" rx="3" fill="currentColor"/>
												  	<rect opacity="0.3" x="4" y="9" width="16" height="2" rx="1" fill="currentColor"/>
												  	<rect opacity="0.3" x="4" y="13" width="10" height="2" rx="1" fill="currentColor"/>
												</svg>
											</span> &nbsp;&nbsp;Transaksi
										</a> 
										<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
											<div class="menu-item px-3">
												<div class="menu-content d-flex align-items-center px-3">
													Transaksi
												</div>
											</div> 
											<div class="menu-item px-5">
												<a href="<?=site_url('kasir')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='transaksi_kasir'?'active':''?>">Transaksi Kasir</a>
											</div> 
											<div class="menu-item px-5">
												<a href="<?=site_url('kasir/belanja')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='transaksi_belanja'?'active':''?>">Pembelanjaan Barang</a>
											</div> 
											<div class="menu-item px-5">
												<a href="<?=site_url('kasir/tukar')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='transaksi_tukar'?'active':''?>">Tukar Barang</a>
											</div> 
											<div class="menu-item px-5">
												<a href="<?=site_url('kasir/retur')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='transaksi_retur'?'active':''?>">Retur</a>
											</div> 
										</div>
									</div>
									<div class="d-flex align-items-center me-2 me-lg-4">
										<a href="#" class="menuicon btn btn-icon btn-borderless btn-active-primary <?=isset($menu)&&$menu=='data'?'active':'bg-white'?> bg-opacity-10" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
											<span class="svg-icon svg-icon-1 svg-icon-white">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
												  	<rect x="3" y="4" width="18" height="5" rx="2" fill="currentColor"/>
												  	<rect opacity="0.3" x="3" y="10" width="18" height="5" rx="2" fill="currentColor"/>
												  	<rect opacity="0.3" x="3" y="16" width="18" height="4" rx="2" fill="currentColor"/>
												</svg>
											</span> &nbsp;&nbsp;Data Utama
										</a>
										<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
											<div class="menu-item px-3">
												<div class="menu-content d-flex align-items-center px-3">
													Data Utama
												</div>
											</div>
											<div class="separator my-2"></div> 
											<div class="menu-item px-5" data-kt-menu-trigger="hover" data-kt-menu-placement="right-end">
												<a href="#" class="menu-link px-5 <?=isset($submenu)&&$submenu=='data_produk'?'active':''?>">
													<span class="menu-title"><?=$prodk?></span>
													<span class="menu-arrow"></span>
												</a> 
												<div class="menu-sub menu-sub-dropdown w-175px py-4"> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_list'?'active':''?>">
														<a href="<?=site_url('produk/list')?>" class="menu-link px-5">Daftar <?=$prodk?></a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='variant_list'?'active':''?>">
														<a href="<?=site_url('produk/variant')?>" class="menu-link px-5">Daftar Variant</a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_stok'?'active':''?>">
														<a href="<?=site_url('produk/stok')?>" class="menu-link px-5">Stok <?=$prodk?></a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_stok_bahan'?'active':''?>">
														<a href="<?=site_url('produk/stokbahan')?>" class="menu-link px-5">Stok Bahan</a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_harga'?'active':''?>">
														<a href="<?=site_url('produk/harga')?>" class="menu-link px-5">Pengaturan Harga</a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_penjualan'?'active':''?>">
														<a href="<?=site_url('produk/penjualan')?>" class="menu-link d-flex flex-stack px-5">Penjualan
														<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="View your statements"></i></a>
													</div>
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_pembelian'?'active':''?>">
														<a href="<?=site_url('produk/pembelian')?>" class="menu-link px-5">Pembelian</a>
													</div>
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_laporan'?'active':''?>">
														<a href="<?=site_url('produk/laporan')?>" class="menu-link px-5">Laporan</a>
													</div>
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_aktifitas'?'active':''?>">
														<a href="<?=site_url('produk/aktifitas')?>" class="menu-link px-5">Aktifitas</a>
													</div>
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='produk_pengaturan'?'active':''?>">
														<a href="<?=site_url('produk/pengaturan')?>" class="menu-link px-5">Pengaturan</a>
													</div> 
												</div> 
											</div>
											<div class="separator my-2"></div> 
											<div class="menu-item px-5" data-kt-menu-trigger="hover" data-kt-menu-placement="right-end">
												<a href="#" class="menu-link px-5 <?=isset($submenu)&&$submenu=='data_toko'?'active':''?>">
													<span class="menu-title">Toko</span>
													<span class="menu-arrow"></span>
												</a> 
												<div class="menu-sub menu-sub-dropdown w-175px py-4"> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='toko_data'?'active':''?>">
														<a href="<?=site_url('toko/list')?>" class="menu-link px-5">Toko Saya</a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='toko_penjualan'?'active':''?>">
														<a href="<?=site_url('toko/penjualan')?>" class="menu-link px-5">Penjualan</a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='toko_karyawan'?'active':''?>">
														<a href="<?=site_url('toko/karyawan')?>" class="menu-link px-5">Karyawan Toko</a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='toko_aktifitas'?'active':''?>">
														<a href="<?=site_url('toko/aktifitas')?>" class="menu-link px-5">Aktifitas</a>
													</div>
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='toko_pengaturan'?'active':''?>">
														<a href="<?=site_url('toko/pengaturan')?>" class="menu-link px-5">Pengaturan</a>
													</div> 
												</div> 
											</div>
											<div class="separator my-2"></div> 
											<div class="menu-item px-5" data-kt-menu-trigger="hover" data-kt-menu-placement="right-end">
												<a href="#" class="menu-link px-5 <?=isset($submenu)&&$submenu=='data_pelanggan'?'active':''?>">
													<span class="menu-title">Pelanggan</span>
													<span class="menu-arrow"></span>
												</a> 
												<div class="menu-sub menu-sub-dropdown w-175px py-4"> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='pelanggan_data'?'active':''?>">
														<a href="<?=site_url('pelanggan/list')?>" class="menu-link px-5">Daftar Pelanggan</a>
													</div> 
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='pelanggan_laporan'?'active':''?>">
														<a href="<?=site_url('pelanggan/laporan')?>" class="menu-link px-5">Laporan</a>
													</div>
													<div class="menu-item px-3 <?=isset($subsubmenu)&&$subsubmenu=='pelanggan_pengaturan'?'active':''?>">
														<a href="<?=site_url('pelanggan/pengaturan')?>" class="menu-link px-5">Pengaturan</a>
													</div> 
												</div> 
											</div>  
										</div>
									</div>
									<div class="d-flex align-items-center me-2 me-lg-4">
										<a href="#" class="menuicon btn btn-icon btn-borderless btn-active-primary <?=isset($menu)&&$menu=='laporan'?'active':'bg-white'?> bg-opacity-10" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
											<span class="svg-icon svg-icon-1 svg-icon-white">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												  	<rect x="4" y="10" width="3" height="10" fill="black"/>
												  	<rect opacity="0.3" x="10" y="6" width="3" height="14" fill="black"/>
												  	<rect x="16" y="3" width="3" height="17" fill="black"/>
												</svg> 
											</span> &nbsp;&nbsp;Laporan
										</a>
										<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
											<div class="menu-item px-3">
												<div class="menu-content d-flex align-items-center px-3">
													Laporan
												</div>
											</div>
											<div class="separator my-2"></div> 
											<div class="menu-item px-5">
												<a href="<?=site_url('laporan/penjualan')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='laporan_penjualan'?'active':''?>">Penjualan</a>
											</div> 
											<div class="menu-item px-5">
												<a href="<?=site_url('laporan/pembelanjaan')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='laporan_pembelanjaan'?'active':''?>">Pembelanjaan</a>
											</div> 
											<div class="menu-item px-5">
												<a href="<?=site_url('laporan/toko')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='laporan_toko'?'active':''?>">Toko</a>
											</div>  
										</div>
									</div>
									<div class="d-flex align-items-center me-2 me-lg-4">
										<a href="#" class="menuicon btn btn-icon btn-borderless btn-active-primary <?=isset($menu)&&$menu=='pengaturan'?'active':'bg-white'?> bg-opacity-10" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
											<span class="svg-icon svg-icon-1 svg-icon-white">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												  	<path d="M12 8.5C10.067 8.5 8.5 10.067 8.5 12C8.5 13.933 10.067 15.5 12 15.5C13.933 15.5 15.5 13.933 15.5 12C15.5 10.067 13.933 8.5 12 8.5Z" fill="black"/>
												  	<path opacity="0.3" d="M20 13.5V10.5L17.8 10.1C17.6 9.5 17.3 8.9 16.9 8.4L18.2 6.6L16.1 4.5L14.3 5.8C13.8 5.4 13.2 5.1 12.6 4.9L12.2 2.7H9.8L9.4 4.9C8.8 5.1 8.2 5.4 7.7 5.8L5.9 4.5L3.8 6.6L5.1 8.4C4.7 8.9 4.4 9.5 4.2 10.1L2 10.5V13.5L4.2 13.9C4.4 14.5 4.7 15.1 5.1 15.6L3.8 17.4L5.9 19.5L7.7 18.2C8.2 18.6 8.8 18.9 9.4 19.1L9.8 21.3H12.2L12.6 19.1C13.2 18.9 13.8 18.6 14.3 18.2L16.1 19.5L18.2 17.4L16.9 15.6C17.3 15.1 17.6 14.5 17.8 13.9L20 13.5Z" fill="black"/>
												</svg>
											</span> &nbsp;&nbsp;Pengaturan
										</a>
										<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
											<div class="menu-item px-3">
												<div class="menu-content d-flex align-items-center px-3">
													Pengaturan
												</div>
											</div>
											<div class="separator my-2"></div>

											<div class="menu-item px-5">
												<a href="<?=site_url('pengaturan/perusahaan')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='pengaturan_perusahaan'?'active':''?>">Perusahaan</a>
											</div>  
											<div class="menu-item px-5">
												<a href="<?=site_url('pengaturan/jenis_konsumen')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='pengaturan_jenis_konsumen'?'active':''?>">Jenis Konsumen</a>
											</div>  
											<?php if($_SESSION['userty']=='owner'){ ?>
												<div class="menu-item px-5">
													<a href="<?=site_url('pengaturan/kategori')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='pengaturan_kategori'?'active':''?>">Kategori Produk</a>
												</div>  
											<?php } ?>
											<div class="menu-item px-5">
												<a href="<?=site_url('pengaturan/meja')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='pengaturan_meja'?'active':''?>">Meja</a>
											</div>  
											<div class="menu-item px-5">
												<a href="<?=site_url('pengaturan/bahan')?>" class="menu-link px-5 <?=isset($submenu)&&$submenu=='pengaturan_bahan'?'active':''?>">Bahan Mentah Produk</a>
											</div>  

										</div>
									</div>
								</div>
							</div> 
							<?=isset($pagetitle)?'<div class="header-brand me-5"><h1 style="color:white;text-shadow: 1px 1px 1px grey;">'.$pagetitle.'</h1></div>':''?>
							<div class="topbar d-flex align-items-stretch">
								<div class="d-flex align-items-stretch me-2 me-lg-4">
									<div id="kt_header_search__" class="header-search d-flex align-items-center" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-search-responsive="lg" data-kt-menu-trigger="auto" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
										<div data-kt-search-element="toggle" class="d-flex d-lg-none align-items-center">
											<div class="btn btn-icon btn-borderless btn-active-primary bg-white bg-opacity-10">
												<span class="svg-icon svg-icon-1 svg-icon-white">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
														<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
													</svg>
												</span>
											</div>
										</div>
										<form data-kt-search-element="form" class="d-none d-lg-block w-100 position-relative mb-2 mb-lg-0" autocomplete="off">
											<input type="hidden" />
											<span class="svg-icon svg-icon-3 position-absolute top-50 translate-middle-y ms-0 ms-lg-4">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path d="M21.7 18.9L18.6 15.8C17.9 16.9 16.9 17.9 15.8 18.6L18.9 21.7C19.3 22.1 19.9 22.1 20.3 21.7L21.7 20.3C22.1 19.9 22.1 19.3 21.7 18.9Z" fill="black" />
													<path opacity="0.3" d="M11 20C6 20 2 16 2 11C2 6 6 2 11 2C16 2 20 6 20 11C20 16 16 20 11 20ZM11 4C7.1 4 4 7.1 4 11C4 14.9 7.1 18 11 18C14.9 18 18 14.9 18 11C18 7.1 14.9 4 11 4ZM8 11C8 9.3 9.3 8 11 8C11.6 8 12 7.6 12 7C12 6.4 11.6 6 11 6C8.2 6 6 8.2 6 11C6 11.6 6.4 12 7 12C7.6 12 8 11.6 8 11Z" fill="black" />
												</svg>
											</span>
											<input type="text" class="form-control form-control-flush ps-8 ps-lg-12" name="search" value="" placeholder="Search" data-kt-search-element="input" />
											<span class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-lg-5" data-kt-search-element="spinner">
												<span class="spinner-border h-15px w-15px align-middle text-gray-400"></span>
											</span>
											<span class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-lg-4" data-kt-search-element="clear">
												<span class="svg-icon svg-icon-2 svg-icon-lg-1 me-0">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
														<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
													</svg>
												</span>
											</span>
										</form>
										<div data-kt-search-element="content" class="menu menu-sub menu-sub-dropdown w-300px w-md-350px py-7 px-7 overflow-hidden">
											<div class="d-block d-lg-none separator mb-4"></div>
											<div data-kt-search-element="wrapper">
												<div data-kt-search-element="results" class="d-none">
													<div class="scroll-y mh-200px mh-lg-350px">
														<h3 class="fs-5 text-muted m-0 pb-5" data-kt-search-element="category-title">Pelanggan</h3>
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<div class="symbol symbol-40px me-4">
																<img src="<?=base_url('t/')?>/media/avatars/150-1.jpg" alt="" />
															</div>
															<div class="d-flex flex-column justify-content-start fw-bold">
																<span class="fs-6 fw-bold">Karina Clark</span>
																<span class="fs-7 fw-bold text-muted">Marketing Manager</span>
															</div>
														</a>
														<h3 class="fs-5 text-muted m-0 pt-5 pb-5" data-kt-search-element="category-title">Produk</h3>
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<img class="w-20px h-20px" src="<?=base_url('t/')?>/media/svg/brand-logos/volicity-9.svg" alt="" />
																</span>
															</div>
															<div class="d-flex flex-column justify-content-start fw-bold">
																<span class="fs-6 fw-bold">Company Rbranding</span>
																<span class="fs-7 fw-bold text-muted">UI Design</span>
															</div>
														</a>
														<h3 class="fs-5 text-muted m-0 pt-5 pb-5" data-kt-search-element="category-title">Karyawan</h3>
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																			<path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM15 17C15 16.4 14.6 16 14 16H8C7.4 16 7 16.4 7 17C7 17.6 7.4 18 8 18H14C14.6 18 15 17.6 15 17ZM17 12C17 11.4 16.6 11 16 11H8C7.4 11 7 11.4 7 12C7 12.6 7.4 13 8 13H16C16.6 13 17 12.6 17 12ZM17 7C17 6.4 16.6 6 16 6H8C7.4 6 7 6.4 7 7C7 7.6 7.4 8 8 8H16C16.6 8 17 7.6 17 7Z" fill="black" />
																			<path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black" />
																		</svg>
																	</span>
																</span>
															</div>
															<div class="d-flex flex-column">
																<span class="fs-6 fw-bold">Si-Fi Project by AU Themes</span>
																<span class="fs-7 fw-bold text-muted">#45670</span>
															</div>
														</a>
													</div>
												</div>
												<div data-kt-search-element="empty" class="text-center d-none"> 
													<div class="pt-10 pb-10"> 
														<span class="svg-icon svg-icon-4x opacity-50">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<path opacity="0.3" d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" fill="black" />
																<path d="M20 8L14 2V6C14 7.10457 14.8954 8 16 8H20Z" fill="black" />
																<rect x="13.6993" y="13.6656" width="4.42828" height="1.73089" rx="0.865447" transform="rotate(45 13.6993 13.6656)" fill="black" />
																<path d="M15 12C15 14.2 13.2 16 11 16C8.8 16 7 14.2 7 12C7 9.8 8.8 8 11 8C13.2 8 15 9.8 15 12ZM11 9.6C9.68 9.6 8.6 10.68 8.6 12C8.6 13.32 9.68 14.4 11 14.4C12.32 14.4 13.4 13.32 13.4 12C13.4 10.68 12.32 9.6 11 9.6Z" fill="black" />
															</svg>
														</span> 
													</div> 
													<div class="pb-15 fw-bold">
														<h3 class="text-gray-600 fs-5 mb-2">Yang anda cari tidak ditemukan</h3>
														<div class="text-muted fs-7">Silahkan coba lagi dnegan kata kunci yang lain</div>
													</div> 
												</div> 
											</div> 
										</div>
									</div>
								</div>
								<div class="d-flex align-items-center me-2 me-lg-4">
									<a href="#" class="btn btn-icon btn-borderless btn-active-primary bg-white bg-opacity-10 position-relative" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
										<span class="svg-icon svg-icon-1 svg-icon-white">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="black" />
												<path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="black" />
											</svg>
										</span>
										<span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-100 animation-blink"></span>
									</a>
									<div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
										<div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('<?=base_url("t/")?>/media/misc/pattern-1.jpg')">
											<h3 class="text-white fw-bold px-9 mt-10 mb-6">Notifikasi
											<span class="fs-8 opacity-75 ps-3"><label id="jumlah_notif">0</label> pesan</span></h3>
											<ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-bold px-9">
												<li class="nav-item">
													<a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#notif_pesan">Pesan</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#notif_pemberitahuan">Pemberitahuan</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#notif_transaksi">Transaksi</a>
												</li>
											</ul>
										</div>
										<div class="tab-content">
											<div class="tab-pane fade" id="notif_pesan" role="tabpanel">
												<div class="scroll-y mh-325px my-5 px-8" id="notif_pesan_content"></div>
												<div class="py-3 text-center border-top">
													<a href="<?=site_url('notifikasi/pesan')?>" class="btn btn-color-gray-600 btn-active-color-primary">Lihat Semua
														<span class="svg-icon svg-icon-5">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
																<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
															</svg>
														</span>
													</a>
												</div>
											</div>
											<div class="tab-pane fade show active" id="notif_pemberitahuan" role="tabpanel">
												<div class="scroll-y mh-325px my-5 px-8" id="notif_pemberitahuan_content"></div>
												<div class="py-3 text-center border-top">
													<a href="<?=site_url('notifikasi/pemberitahuan')?>" class="btn btn-color-gray-600 btn-active-color-primary">Lihat Semua
														<span class="svg-icon svg-icon-5">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
																<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
															</svg>
														</span>
													</a>
												</div>
											</div>
											<div class="tab-pane fade" id="notif_transaksi" role="tabpanel">
												<div class="scroll-y mh-325px my-5 px-8" id="notif_transaksi_content"></div>
												<div class="py-3 text-center border-top">
													<a href="<?=site_url('notifikasi/transaksi')?>" class="btn btn-color-gray-600 btn-active-color-primary">Lihat Semua
														<span class="svg-icon svg-icon-5">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
																<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
															</svg>
														</span>
													</a>
												</div>
											</div>
										</div>
										<script>
											function timeAgo(dateString) {
											    const date = new Date(dateString);
											    const now = new Date();
											    const diff = Math.floor((now - date) / 1000); // difference in seconds

											    if (diff < 60) {
											        return `${diff} second${diff !== 1 ? 's' : ''} ago`;
											    } else if (diff < 3600) { // less than 60 minutes
											        const mins = Math.floor(diff / 60);
											        return `${mins} minute${mins !== 1 ? 's' : ''} ago`;
											    } else if (diff < 86400) { // less than 24 hours
											        const hours = Math.floor(diff / 3600);
											        return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
											    } else if (diff < 604800) { // less than 7 days
											        const options = { weekday: 'long' };
											        return date.toLocaleDateString(undefined, options); // e.g., "Monday"
											    } else if (diff < 31536000) { // less than 1 year
											        return date.toLocaleDateString(undefined, { day: '2-digit', month: '2-digit', year: '2-digit' });
											    } else {
											        const years = Math.floor(diff / 31536000);
											        return `${years} year${years !== 1 ? 's' : ''} ago`;
											    }
											}
											console.log(timeAgo('2025-12-03 15:00:00'));
											function checkNotif() {
											    fetch('<?=site_url("notif/pcheck")?>')
											        .then(res => res.json())
											        .then(data => {
											            if (data.status) {
											                // Assume you have a container div for notifications
											                const content_pesan = document.querySelector('#notif_pesan_content');

											                data.pesan.list.forEach(item => {
											                    const div = document.createElement('div');
											                    div.className = 'd-flex flex-stack py-4';
											                    let ico = '';
											                    if(item.status === "unread"){ 
											                    	ico = '<div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-primary"><span class="svg-icon svg-icon-2 svg-icon-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M11 6.5C11 9 9 11 6.5 11C4 11 2 9 2 6.5C2 4 4 2 6.5 2C9 2 11 4 11 6.5ZM17.5 2C15 2 13 4 13 6.5C13 9 15 11 17.5 11C20 11 22 9 22 6.5C22 4 20 2 17.5 2ZM6.5 13C4 13 2 15 2 17.5C2 20 4 22 6.5 22C9 22 11 20 11 17.5C11 15 9 13 6.5 13ZM17.5 13C15 13 13 15 13 17.5C13 20 15 22 17.5 22C20 22 22 20 22 17.5C22 15 20 13 17.5 13Z" fill="black" /><path d="M17.5 16C17.5 16 17.4 16 17.5 16L16.7 15.3C16.1 14.7 15.7 13.9 15.6 13.1C15.5 12.4 15.5 11.6 15.6 10.8C15.7 9.99999 16.1 9.19998 16.7 8.59998L17.4 7.90002H17.5C18.3 7.90002 19 7.20002 19 6.40002C19 5.60002 18.3 4.90002 17.5 4.90002C16.7 4.90002 16 5.60002 16 6.40002V6.5L15.3 7.20001C14.7 7.80001 13.9 8.19999 13.1 8.29999C12.4 8.39999 11.6 8.39999 10.8 8.29999C9.99999 8.19999 9.20001 7.80001 8.60001 7.20001L7.89999 6.5V6.40002C7.89999 5.60002 7.19999 4.90002 6.39999 4.90002C5.59999 4.90002 4.89999 5.60002 4.89999 6.40002C4.89999 7.20002 5.59999 7.90002 6.39999 7.90002H6.5L7.20001 8.59998C7.80001 9.19998 8.19999 9.99999 8.29999 10.8C8.39999 11.5 8.39999 12.3 8.29999 13.1C8.19999 13.9 7.80001 14.7 7.20001 15.3L6.5 16H6.39999C5.59999 16 4.89999 16.7 4.89999 17.5C4.89999 18.3 5.59999 19 6.39999 19C7.19999 19 7.89999 18.3 7.89999 17.5V17.4L8.60001 16.7C9.20001 16.1 9.99999 15.7 10.8 15.6C11.5 15.5 12.3 15.5 13.1 15.6C13.9 15.7 14.7 16.1 15.3 16.7L16 17.4V17.5C16 18.3 16.7 19 17.5 19C18.3 19 19 18.3 19 17.5C19 16.7 18.3 16 17.5 16Z" fill="black" /></svg></span></span></div>'; 
											                    } else if (item.status === 'read'){
											                    	ico = '<div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-success"><span class="svg-icon svg-icon-2 svg-icon-success"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M5 15C3.3 15 2 13.7 2 12C2 10.3 3.3 9 5 9H5.10001C5.00001 8.7 5 8.3 5 8C5 5.2 7.2 3 10 3C11.9 3 13.5 4 14.3 5.5C14.8 5.2 15.4 5 16 5C17.7 5 19 6.3 19 8C19 8.4 18.9 8.7 18.8 9C18.9 9 18.9 9 19 9C20.7 9 22 10.3 22 12C22 13.7 20.7 15 19 15H5ZM5 12.6H13L9.7 9.29999C9.3 8.89999 8.7 8.89999 8.3 9.29999L5 12.6Z" fill="black" /><path d="M17 17.4V12C17 11.4 16.6 11 16 11C15.4 11 15 11.4 15 12V17.4H17Z" fill="black" /><path opacity="0.3" d="M12 17.4H20L16.7 20.7C16.3 21.1 15.7 21.1 15.3 20.7L12 17.4Z" fill="black" /><path d="M8 12.6V18C8 18.6 8.4 19 9 19C9.6 19 10 18.6 10 18V12.6H8Z" fill="black" /></svg></span></span></div>'; 
											                    } else {
											                    	ico = '<div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-danger"><span class="svg-icon svg-icon-2 svg-icon-danger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" /><rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" /><rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" /></svg></span></span></div>'; 
											                    }
											                    div.innerHTML = `
											                        <div class="d-flex align-items-center">
											                            ${ico}
											                            <div class="mb-0 me-2">
											                                <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bolder">${item.title}</a>
											                                <div class="text-gray-400 fs-7">${item.description}</div>
											                            </div>
											                        </div>
											                        <span class="badge badge-light fs-8">${timeAgo(item.created)}</span>
											                    `;
											                    content_pesan.prepend(div); // prepend to show newest on top
											                });



											                // Assume you have a container div for notifications
											                const content_pemberitahuan = document.querySelector('#notif_pesan_content');

											                data.pemberitahuan.list.forEach(item => {
											                    const div = document.createElement('div');
											                    div.className = 'd-flex flex-stack py-4';
											                    let ico = '';
											                    if(item.status === "unread"){ 
											                    	ico = '<div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-primary"><span class="svg-icon svg-icon-2 svg-icon-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M11 6.5C11 9 9 11 6.5 11C4 11 2 9 2 6.5C2 4 4 2 6.5 2C9 2 11 4 11 6.5ZM17.5 2C15 2 13 4 13 6.5C13 9 15 11 17.5 11C20 11 22 9 22 6.5C22 4 20 2 17.5 2ZM6.5 13C4 13 2 15 2 17.5C2 20 4 22 6.5 22C9 22 11 20 11 17.5C11 15 9 13 6.5 13ZM17.5 13C15 13 13 15 13 17.5C13 20 15 22 17.5 22C20 22 22 20 22 17.5C22 15 20 13 17.5 13Z" fill="black" /><path d="M17.5 16C17.5 16 17.4 16 17.5 16L16.7 15.3C16.1 14.7 15.7 13.9 15.6 13.1C15.5 12.4 15.5 11.6 15.6 10.8C15.7 9.99999 16.1 9.19998 16.7 8.59998L17.4 7.90002H17.5C18.3 7.90002 19 7.20002 19 6.40002C19 5.60002 18.3 4.90002 17.5 4.90002C16.7 4.90002 16 5.60002 16 6.40002V6.5L15.3 7.20001C14.7 7.80001 13.9 8.19999 13.1 8.29999C12.4 8.39999 11.6 8.39999 10.8 8.29999C9.99999 8.19999 9.20001 7.80001 8.60001 7.20001L7.89999 6.5V6.40002C7.89999 5.60002 7.19999 4.90002 6.39999 4.90002C5.59999 4.90002 4.89999 5.60002 4.89999 6.40002C4.89999 7.20002 5.59999 7.90002 6.39999 7.90002H6.5L7.20001 8.59998C7.80001 9.19998 8.19999 9.99999 8.29999 10.8C8.39999 11.5 8.39999 12.3 8.29999 13.1C8.19999 13.9 7.80001 14.7 7.20001 15.3L6.5 16H6.39999C5.59999 16 4.89999 16.7 4.89999 17.5C4.89999 18.3 5.59999 19 6.39999 19C7.19999 19 7.89999 18.3 7.89999 17.5V17.4L8.60001 16.7C9.20001 16.1 9.99999 15.7 10.8 15.6C11.5 15.5 12.3 15.5 13.1 15.6C13.9 15.7 14.7 16.1 15.3 16.7L16 17.4V17.5C16 18.3 16.7 19 17.5 19C18.3 19 19 18.3 19 17.5C19 16.7 18.3 16 17.5 16Z" fill="black" /></svg></span></span></div>'; 
											                    } else if (item.status === 'read'){
											                    	ico = '<div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-success"><span class="svg-icon svg-icon-2 svg-icon-success"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path opacity="0.3" d="M5 15C3.3 15 2 13.7 2 12C2 10.3 3.3 9 5 9H5.10001C5.00001 8.7 5 8.3 5 8C5 5.2 7.2 3 10 3C11.9 3 13.5 4 14.3 5.5C14.8 5.2 15.4 5 16 5C17.7 5 19 6.3 19 8C19 8.4 18.9 8.7 18.8 9C18.9 9 18.9 9 19 9C20.7 9 22 10.3 22 12C22 13.7 20.7 15 19 15H5ZM5 12.6H13L9.7 9.29999C9.3 8.89999 8.7 8.89999 8.3 9.29999L5 12.6Z" fill="black" /><path d="M17 17.4V12C17 11.4 16.6 11 16 11C15.4 11 15 11.4 15 12V17.4H17Z" fill="black" /><path opacity="0.3" d="M12 17.4H20L16.7 20.7C16.3 21.1 15.7 21.1 15.3 20.7L12 17.4Z" fill="black" /><path d="M8 12.6V18C8 18.6 8.4 19 9 19C9.6 19 10 18.6 10 18V12.6H8Z" fill="black" /></svg></span></span></div>'; 
											                    } else {
											                    	ico = '<div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-danger"><span class="svg-icon svg-icon-2 svg-icon-danger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="black" /><rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="black" /><rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="black" /></svg></span></span></div>'; 
											                    }
											                    div.innerHTML = `
											                        <div class="d-flex align-items-center">
											                            ${ico}
											                            <div class="mb-0 me-2">
											                                <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bolder">${item.title}</a>
											                                <div class="text-gray-400 fs-7">${item.description}</div>
											                            </div>
											                        </div>
											                        <span class="badge badge-light fs-8">${timeAgo(item.created)}</span>
											                    `;
											                    content_pemberitahuan.prepend(div); // prepend to show newest on top
											                });



											                // Assume you have a container div for notifications
											                const content_transaksi = document.querySelector('#notif_pesan_content');

											                data.transaksi.list.forEach(item => {
											                    const div = document.createElement('div');
											                    div.className = 'd-flex flex-stack py-4';
											                    let ico = '';
											                    if(item.status === "unread"){ 
											                    	ico = `<span class="w-70px badge badge-light-primary me-4">${item.status}</span>`; 
											                    } else if (item.status === 'read'){
											                    	ico = `<span class="w-70px badge badge-light-success me-4">${item.status}</span>`; 
											                    } else {
											                    	ico = `<span class="w-70px badge badge-light-danger me-4">${item.status}</span>`; 
											                    }
											                    div.innerHTML = `
											                        <div class="d-flex flex-stack py-4">
																		<div class="d-flex align-items-center me-2">
																			${ico}
																			<a href="#" class="text-gray-800 text-hover-primary fw-bold">${item.title}</a>
																		</div>
																		<span class="badge badge-light fs-8">${timeAgo(item.created)}</span>
																	</div>
											                    `;
											                    content_transaksi.prepend(div); // prepend to show newest on top
											                });
											            }
											            // Immediately re-check
											            checkNotif();
											        }).catch(err => {
											            console.error(err);
											            setTimeout(checkNotif, 10000);
											        });
											}
										</script>
									</div>
								</div>
								<div class="d-flex align-items-center me-2 me-lg-4">
									<a href="#" class="btn btn-icon btn-borderless btn-active-primary bg-white bg-opacity-10" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
										<span class="svg-icon svg-icon-1 svg-icon-white">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<path d="M6.28548 15.0861C7.34369 13.1814 9.35142 12 11.5304 12H12.4696C14.6486 12 16.6563 13.1814 17.7145 15.0861L19.3493 18.0287C20.0899 19.3618 19.1259 21 17.601 21H6.39903C4.87406 21 3.91012 19.3618 4.65071 18.0287L6.28548 15.0861Z" fill="black" />
												<rect opacity="0.3" x="8" y="3" width="8" height="8" rx="4" fill="black" />
											</svg>
										</span>
									</a>
									<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
										<div class="menu-item px-3">
											<div class="menu-content d-flex align-items-center px-3">
												<div class="symbol symbol-50px me-5">
													<img alt="Logo" src="<?=$user['foto']!=''?base_url('f/'.$user['foto']):base_url('f/'.sys('nouser'))?>" />
												</div>
												<div class="d-flex flex-column">
													<div class="fw-bolder d-flex align-items-center fs-5"><?=$user['name']?>
													<span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2"><?=$user['type']?></span></div>
													<a href="<?=site_url('user/profile')?>" class="fw-bold text-muted text-hover-primary fs-7"><?=$user['company_phone']?></a>
													<a href="javascript:;" class="fw-bold text-muted text-hover-primary fs-7 coping d-none" data-copy="<?=$userid?>"><?=$userid?></a>
													<a href="javascript:;" class="fw-bold text-muted text-hover-primary fs-7 coping d-none" data-copy="<?=$_SESSION['usertoken']?>"><?=$_SESSION['usertoken']?></a>
												</div>
											</div>
										</div>
										<div class="separator my-2"></div>
										<div class="menu-item px-5">
											<a href="<?=site_url('user/profile')?>" class="menu-link px-5">Profil</a>
										</div>
										<div class="menu-item px-5">
											<a href="<?=site_url('toko')?>" class="menu-link px-5">
												<span class="menu-text">Toko Saya</span>
												<span class="menu-badge">
													<span class="badge badge-light-danger badge-circle fw-bolder fs-7"><?=$jumlah['toko']?></span>
												</span>
											</a>
										</div> 
										<div class="menu-item px-5">
											<a href="<?=site_url('logout')?>" class="menu-link px-5">Keluar</a>
										</div> 
									</div>
								</div>
								<div class="d-flex align-items-center me-2 me-lg-4">
									<a href="<?=site_url('kasir')?>" class="btn btn-success border-0 px-3 px-lg-6">Transaksi</a>
								</div>
								<div class="d-flex align-items-center">
									<a href="<?=site_url()?>" class="btn btn-icon btn-color-white btn-active-color-primary border-0 me-n3" data-bs-toggle="tooltip" data-bs-placement="left" title="Return to launcher">
										<span class="svg-icon svg-icon-2x">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black" />
												<rect x="7" y="15.3137" width="12" height="2" rx="1" transform="rotate(-45 7 15.3137)" fill="black" />
												<rect x="8.41422" y="7" width="12" height="2" rx="1" transform="rotate(45 8.41422 7)" fill="black" />
											</svg>
										</span>
									</a>
								</div>
							</div>
						</div>
					</div>
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content"> 
						<div class="post d-flex flex-column-fluid" id="kt_post">
							<div id="kt_content_container" class="container-xxl">