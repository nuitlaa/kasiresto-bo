<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$companyid 	= companyid($userid);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    if(isset($id)&&$id!=""){
		include('mod/hmenu_produk.php');
	} 
    //session()->set('redirect_store', current_url());
    session()->set('redirect_worker', current_url());
?>


<div class="card pt-4 mb-6 mb-xl-9"> 
	<div class="card-header border-0">
		
		<div class="card-title">
			<h2 class="fw-bolder mb-0">Daftar Produk</h2>
		</div> 

		<div class="card-toolbar">
			<div class="d-flex align-items-center position-relative my-1" style="margin-right: 20px;">
				<span class="svg-icon svg-icon-1 position-absolute ms-6">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
						<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
					</svg>
				</span>
				<input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari Produk">
			</div>


			<span class="fw-bold text-muted me-2">1 - 50 of 235</span>
			<a href="#" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Previous message">
				<span class="svg-icon svg-icon-2 m-0">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M11.2657 11.4343L15.45 7.25C15.8642 6.83579 15.8642 6.16421 15.45 5.75C15.0358 5.33579 14.3642 5.33579 13.95 5.75L8.40712 11.2929C8.01659 11.6834 8.01659 12.3166 8.40712 12.7071L13.95 18.25C14.3642 18.6642 15.0358 18.6642 15.45 18.25C15.8642 17.8358 15.8642 17.1642 15.45 16.75L11.2657 12.5657C10.9533 12.2533 10.9533 11.7467 11.2657 11.4343Z" fill="black" />
					</svg>
				</span>
			</a>
			<a href="#" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Next message">
				<span class="svg-icon svg-icon-2 m-0">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="black" />
					</svg>
				</span>
			</a>

			<a href="<?=site_url('produk/tambah')?>" class="btn btn-sm btn-flex btn-light-primary">
				<span class="svg-icon svg-icon-3">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black" />
						<rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black" />
						<rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black" />
					</svg>
				</span>Produk Baru
			</a>
		</div>
	</div> 
	<div id="kt_customer_view_payment_method" class="card-body pt-0">
		<?php 
		$graph = '<i class="fa fa-database"></i>';
		$star = icon('star');
		$edit = icon('pencil2');
		$more = icon('more');
		if ($_SESSION['userty']=='owner') {
			$produk = $db->table('product')->where(['company'=>$companyid])->get()->getResultArray();
		} else {
			$st = $db->table('account_store_privilage')->where(['account'=>$userid])->select('store')->get()->getRowArray();
			if (isset($st['store'])) {
				$produk = $db->table('product')->where(['store'=>$st['store']])->get()->getResultArray();
			} else { $produk = array(); }
		}
		$atype = $db->table('account_type')->where(['sales'=>1])->get()->getResultArray();
		$varr = array(1,2);
		foreach($produk as $K=>$v){ 
			$foto = $db->table('product_file')->where(['id'=>$v['cover']])->select('file')->get()->getRowArray();
			?>
			<div class="py-0" data-kt-customer-payment-method="row">
				<div class="py-3 d-flex flex-stack flex-wrap">
					<div class="d-flex align-items-center" onclick="golink('<?=site_url('produk/penjualan/'.$v['id'])?>')">
						<div class="symbol symbol-50 me-4">
							<span class="symbol-label" style="background-image:url(<?=isset($foto['file'])&&$foto['file']!=''?base_url('f/'.$foto['file']):base_url('f/'.sys('nofoto'))?>);"></span>
						</div>
						<div class="pe-5">
							<div class="d-flex align-items-center flex-wrap gap-1">
								<a href="#" class="fw-bolder text-dark text-hover-primary"><?=$v['name']?></a>
								<span class="svg-icon svg-icon-7 svg-icon-success mx-3">
									<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
										<circle fill="#000000" cx="12" cy="12" r="8" />
									</svg>
								</span>
								<span class="text-muted fw-bolder d-none">2 days ago</span>
								<div class="badge badge-light-<?=$v['status']=='active'?'success':'primary'?> ms-5"><?=$v['status']?></div>
							</div>
							<div class="text-muted fw-bold mw-450px" data-kt-inbox-message="preview"><?=$v['description']?></div>
						</div>
					</div>
					<div class="d-flex align-items-center flex-wrap gap-2">
						<span class="fw-bold text-muted text-end me-3"><?=created($v['created'])?></span>
						<div class="d-flex">
							<a href="<?=site_url('produk/penjualan/'.$v['id'])?>" class="btn btn-sm btn-icon btn-clear btn-active-light-primary me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="penjualan"><?=$graph?></a>
							<a href="<?=site_url('produk/edit/'.$v['id'])?>" class="btn btn-icon btn-active-light-primary w-30px h-30px me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_new_card"><?=$edit?></a>
							<a href="<?=site_url('produk/pengaturan/'.$v['id'])?>" class="btn btn-icon btn-active-light-primary w-30px h-30px" data-bs-toggle="tooltip" title="Pengaturan" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"><?=$more?></a>
						</div>
					</div>
				</div>
			</div>
			<div class="separator separator-dashed"></div>
		<?php } ?>
	</div> 
</div> 


<div class="card pt-4 mb-6 mb-xl-9">
	<div class="card-header border-0">
		<div class="card-title">
			<h2>Rekap Penjualan</h2>
		</div>
		<div class="card-toolbar">
			<div class="d-flex align-items-center position-relative my-1" style="margin-right: 20px;">
				<span class="svg-icon svg-icon-1 position-absolute ms-6">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
						<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
					</svg>
				</span>
				<input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Tanggal">
			</div>
			<div class="d-flex align-items-center position-relative my-1" style="margin-right: 20px;">
				<span class="svg-icon svg-icon-1 position-absolute ms-6">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
						<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
					</svg>
				</span>
				<input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari">
			</div>
			<button type="button" class="btn btn-sm btn-flex btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_payment">
				<span class="svg-icon svg-icon-3">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black" />
						<rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black" />
						<rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black" />
					</svg>
				</span>
				Add payment
			</button>
		</div>
	</div>
	<div class="card-body pt-0 pb-5">
		<table class="table align-middle table-row-dashed gy-5" id="kt_table_customers_payment">
			<thead class="border-bottom border-gray-200 fs-7 fw-bolder">
				<tr class="text-start text-muted text-uppercase gs-0">
					<th class="min-w-100px">Invoice No.</th>
					<th>Status</th>
					<th>Amount</th>
					<th class="min-w-100px">Date</th>
					<th class="text-end min-w-100px pe-4">Actions</th>
				</tr>
			</thead>
			<tbody class="fs-6 fw-bold text-gray-600">
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">5206-8956</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-success">Successful</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$1,200.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>14 Dec 2020, 8:43 pm</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">3049-8433</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-success">Successful</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$79.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>01 Dec 2020, 10:12 am</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">3117-8643</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-success">Successful</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$5,500.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>12 Nov 2020, 2:01 pm</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">8773-4216</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-warning">Pending</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$880.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>21 Oct 2020, 5:54 pm</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">4637-6806</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-success">Successful</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$7,650.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>19 Oct 2020, 7:32 am</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">3081-9475</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-success">Successful</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$375.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>23 Sep 2020, 12:38 am</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">4127-5714</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-success">Successful</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$129.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>11 Sep 2020, 3:18 pm</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">4145-6498</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-danger">Rejected</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$450.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>03 Sep 2020, 1:08 am</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
				<!--begin::Table row-->
				<tr>
					<!--begin::Invoice=-->
					<td>
						<a href="#" class="text-gray-600 text-hover-primary mb-1">9989-2971</a>
					</td>
					<!--end::Invoice=-->
					<!--begin::Status=-->
					<td>
						<span class="badge badge-light-warning">Pending</span>
					</td>
					<!--end::Status=-->
					<!--begin::Amount=-->
					<td>$8,700.00</td>
					<!--end::Amount=-->
					<!--begin::Date=-->
					<td>01 Sep 2020, 4:58 pm</td>
					<!--end::Date=-->
					<!--begin::Action=-->
					<td class="pe-0 text-end">
						<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
						<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
						<span class="svg-icon svg-icon-5 m-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="../../demo10/dist/apps/customers/view.html" class="menu-link px-3">View</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</td>
					<!--end::Action=-->
				</tr>
				<!--end::Table row-->
			</tbody>
		</table>
	</div>
</div>
<script src="<?=base_url('t/')?>/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function () {

	    const tableEl = document.querySelector('#kt_table_customers_payment');
	    if (!tableEl) return;

	    tableEl.querySelectorAll('tbody tr').forEach(row => {
	        const cell = row.querySelectorAll('td')[3];
	        cell.setAttribute(
	            'data-order',
	            moment(cell.innerHTML, 'DD MMM YYYY, LT').format()
	        );
	    });

	    $('#kt_table_customers_payment').DataTable({
	        info: false,
	        order: [],
	        pageLength: 5,
	        lengthChange: false,
	        columnDefs: [{ orderable: false, targets: 4 }]
	    });

	});

</script>