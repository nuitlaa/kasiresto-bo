<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$companyid 	= companyid($userid);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
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
		$atype = $db->table('account_type')->where(['company'=>$companyid])->get()->getResultArray();
		$varr = array(1,2);
		foreach($produk as $K=>$v){ 
			$foto = $db->table('product_file')->where(['id'=>$v['cover']])->select('file')->get()->getRowArray();
			?>
			<div class="py-0" data-kt-customer-payment-method="row">
				<div class="py-3 d-flex flex-stack flex-wrap">
					<div class="d-flex align-items-center collapsible collapsed rotate" data-bs-toggle="collapse" href="#kt_customer_view_payment_method_1" role="button" aria-expanded="false" aria-controls="kt_customer_view_payment_method_1">
						<div class="me-3 rotate-90">
							<span class="svg-icon svg-icon-3">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
									<path d="M12.6343 12.5657L8.45001 16.75C8.0358 17.1642 8.0358 17.8358 8.45001 18.25C8.86423 18.6642 9.5358 18.6642 9.95001 18.25L15.4929 12.7071C15.8834 12.3166 15.8834 11.6834 15.4929 11.2929L9.95001 5.75C9.5358 5.33579 8.86423 5.33579 8.45001 5.75C8.0358 6.16421 8.0358 6.83579 8.45001 7.25L12.6343 11.4343C12.9467 11.7467 12.9467 12.2533 12.6343 12.5657Z" fill="black" />
								</svg>
							</span>
						</div>
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
				<div id="kt_customer_view_payment_method_1" class="collapse fs-6 ps-10" data-bs-parent="#kt_customer_view_payment_method">
					<div class="d-flex flex-wrap py-5">
						
							<div class="flex-equal me-5">
								<table class="table table-flush fw-bold gy-1">
									<thead>
										<tr><th></th><th>Nama</th><th><?=$v['var1']?></th><th><?=$v['var2']?></th><th>SKU</th><th>Stok</th><th class="text-right">Harga Dasar</th><?php foreach($atype as $kkkk=>$vvvv){echo'<th class="text-right">Harga '.$vvvv['name'].'</th>';}?></tr>
									</thead>
									<tbody>
										<?php foreach($db->table('product_variant')->where(['product'=>$v['id']])->orderBy('var1 ASC,var2 ASC')->get()->getResultArray() as $kk => $vv){ 
											$cover = $db->table('product_file')->where(['id'=>$vv['foto']])->get()->getRowArray();
											echo '
											<tr>
												<td>
													<div class="symbol symbol-50 me-4">
														<span class="symbol-label" style="background-image:url('.(isset($cover['file'])&&$cover['file']!=''?base_url('f/'.$cover['file']):base_url('f/'.sys('nofoto'))).');"></span>
													</div>
												</td>
												<td class="text-muted"><input class="form-control" type="text" value="'.$vv['name'].'" /></td>
												<td class="text-muted"><input class="form-control" type="text" value="'.$vv['var1'].'" /></td>
												<td class="text-muted"><input class="form-control" type="text" value="'.$vv['var2'].'" /></td>
												<td class="text-muted"><input class="form-control" type="text" value="'.$vv['sku'].'" /></td>
												<td class="text-gray-800 text-right">'.$vv['stock'].'</td>
												<td class="text-gray-800 text-right uang"><input class="form-control" type="text" value="'.$vv['base_price'].'" /></td>
												';
												foreach($atype as $kkkk=>$vvvv){
													$pp = $db->table('product_price')->where(['product'=>$v['id'],'account_type'=>$vvvv['id'],'var'=>$vv['id']])->get()->getRowArray();
													$price = isset($pp['price'])?$pp['price']:0;
													echo '<td class="text-gray-800 text-right uang"><input class="form-control" type="text" value="'.$price.'" /></td>';
												}
												echo '
											</tr>';
										} ?> 
									</tbody>
								</table>
							</div>
						
					</div>
				</div>
			</div>
			<div class="separator separator-dashed"></div>
		<?php } ?>
	</div> 
</div> 