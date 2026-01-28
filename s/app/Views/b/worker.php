<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$companyid 	= companyid($userid);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    //session()->set('redirect_store', current_url());
    session()->set('redirect_worker', current_url());
?>

<div class="card mb-5 mb-xxl-10" id="thecontent">
	<!--begin::Card header-->
	<div class="card-header">
		<!--begin::Heading-->
		<div class="card-title">
			<h3>Daftar Petugas</h3>
		</div>
		<!--end::Heading-->
		<!--begin::Toolbar-->
		<div class="card-toolbar">
			<div class="my-1 me-4">
				<!--begin::Select-->
				<select class="form-select form-select-sm form-select-solid w-125px" data-control="select2" data-placeholder="Select Hours" data-hide-search="true">
					<option value="all" selected="selected">Semua</option>
					<option value="active">Aktif</option>
					<option value="passive">Tidak Aktif</option>
				</select>
				<!--end::Select-->
			</div>
			<a href="<?=site_url('petugas/tambah')?>" class="btn btn-sm btn-primary my-1">Tambah Petugas</a>
		</div>
		<!--end::Toolbar-->
	</div>
	<!--end::Card header-->
	<!--begin::Card body-->
	<div class="card-body p-0">
		<!--begin::Table wrapper-->
		<div class="table-responsive">
			<!--begin::Table-->
			<table class="table table-flush align-middle table-row-bordered table-row-solid gy-4 gs-9">
				<!--begin::Thead-->
				<thead class="border-gray-200 fs-5 fw-bold bg-lighten">
					<tr>
						<th class="min-w-250px">Nama Petugas</th>
						<th class="min-w-150px">Toko</th>
						<th class="min-w-150px">Jabatan</th>
						<th class="min-w-150px">Telepon</th>
						<th class="min-w-150px">Email</th>
						<th class="min-w-150px">Alamat</th>
						<th class="min-w-100px">Status</th>
						<th class="min-w-150px">Aksi</th>
					</tr>
				</thead>
				<!--end::Thead-->
				<!--begin::Tbody-->
				<tbody class="fw-6 fw-bold text-gray-600">
					<?php 
					$data = $db->table('account a')->join('account_store_privilage b','b.account=a.id')->where(['b.company'=>$companyid])->select('a.*,b.privilage,b.store')->get()->getResultArray();
					foreach($data as $k=>$v){ $store = $db->table('account_store')->where(['id'=>$v['store']])->get()->getRowArray(); ?>
						<tr>
							<td class="d-flex align-items-center">
								<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
									<a href="<?=site_url('petugas/'.$v['id'])?>">
										<div class="symbol-label">
											<img src="<?=$v['foto']!=''?base_url('f/'.str_replace('.', '_thumb.', $v['foto'])):base_url('f/'.sys('nofoto'))?>" alt="Emma Smith" class="w-100" />
										</div>
									</a>
								</div>
								<div class="d-flex flex-column">
									<a href="<?=site_url('petugas/'.$v['id'])?>" class="text-gray-800 text-hover-primary mb-1"><?=$v['name']?></a>
								</div>
							</td>
							<td><a href="<?=site_url('toko/'.$v['store'])?>" class="text text-<?=isset($store['id'])?'primary':'danger'?>"><?=isset($store['name'])?$store['name']:'Belum ada'?></a></td>
							<td><?=privilage($v['privilage'])?></td>
							<td><?=$v['phone']?></td>
							<td><?=$v['email']?></td>
							<td><?=$v['address']?></td>
							<td class="text-<?=$v['status']=='active'?'success':'danger'?>"><?=$v['status']?></td>
							<td class="pe-9">
								<a href="<?=site_url('petugas/ubah/'.$v['id'])?>" class="badge badge-primary"><i class="fa fa-edit text-white">Ubah</i></a>
								<a href="<?=site_urL('petugas/'.$v['id'])?>" class="badge badge-success"><i class="fa fa-eye text-white">Detail</i></a>
							</td>
						</tr>
					<?php } ?> 
				</tbody>
				<!--end::Tbody-->
			</table>
			<!--end::Table-->
		</div>
		<!--end::Table wrapper-->
	</div>
	<!--end::Card body-->
</div>