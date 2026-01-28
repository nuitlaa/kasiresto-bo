<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    session()->set('redirect_worker', current_url());

    $redirecturl = session()->get('redirect_store') ?? site_url('toko/list');
	//session()->remove('redirect_store');
?>
<div class="card mb-5 mb-xl-10" id="thecontent">
	<div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
		<div class="card-title m-0">
			<h3 class="fw-bolder m-0">Data Toko</h3>
		</div>
	</div>
	<div id="kt_account_settings_profile_details" class="collapse show">
		<form id="forming" class="form">
			<input type="hidden" name="save[company]" value="<?=isset($data['company'])?$data['company']:$user['company_id']?>">
			<input type="hidden" name="save[owner]" value="<?=isset($data['owner'])?$data['owner']:$userid?>">
			<input type="hidden" name="id" id="theid" value="<?=isset($data['id'])?enkripsi($data['id']):0?>">
			<div class="card-body border-top p-9">
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label fw-bold fs-6">Foto</label>
					<div class="col-lg-8">
						<div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url(<?=base_url('f/'.sys('uploadfoto'))?>)">
							<div class="image-input-wrapper w-125px h-125px" style="background-image: url(<?=isset($data['foto'])&&$data['foto']!=''?base_url('f/'.$data['foto']):base_url('f/'.sys('uploadfoto'))?>)"></div>
							<label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change foto">
								<i class="bi bi-pencil-fill fs-7"></i>
								<input type="file" name="foto" accept=".png, .jpg, .jpeg" />
								<input type="hidden" name="foto_remove" />
							</label>
							<span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel foto">
								<i class="bi bi-x fs-2"></i>
							</span>
							<span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove foto">
								<i class="bi bi-x fs-2"></i>
							</span>
						</div>
						<div class="form-text">Allowed file types: png, jpg, jpeg.</div>
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label required fw-bold fs-6">Nama Toko</label>
					<div class="col-lg-8">
						<div class="row">
							<div class="col-lg-6 fv-row">
								<input type="text" name="save[name]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Nama Toko" value="<?=isset($data['name'])?$data['name']:''?>" />
							</div>
							<div class="col-lg-6 fv-row">
								
							</div>
						</div>
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label required fw-bold fs-6">Deskripsi Toko</label>
					<div class="col-lg-8 fv-row">
						<input type="text" name="save[description]" class="form-control form-control-lg form-control-solid" placeholder="Deskripsi Toko" value="<?=isset($data['description'])?$data['description']:''?>" />
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label required fw-bold fs-6">Jenis Layanan</label>
					<div class="col-lg-4 fv-row">
						<select class="form-select" data-control="select2" name="save[layanan]" >
							<?php $arr = array('antar'=>'Pesanan diantar Pelayan','ambil'=>'Pemesan Mengambil Sendiri');
							foreach($arr as $k=>$v){
								$sel = isset($data['layanan'])&&$data['layanan']==$k?'selected':'';
								echo '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
							} ?>
						</select>
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label fw-bold fs-6">
						<span class="required">Telepon</span>
						<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i>
					</label>
					<div class="col-lg-8 fv-row">
						<input type="tel" name="save[phone]" class="form-control form-control-lg form-control-solid" placeholder="Telepon" value="<?=isset($data['phone'])?$data['phone']:''?>" />
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label fw-bold fs-6">Alamat Toko</label>
					<div class="col-lg-8 fv-row">
						<input type="text" name="save[address]" class="form-control form-control-lg form-control-solid" placeholder="Alamat Toko" value="<?=isset($data['address'])?$data['address']:''?>" />
					</div>
				</div> 
				<div class="row mb-6">
					<!--begin::Label-->
					<label class="col-lg-4 col-form-label required fw-bold fs-6">Penanggung Jawab</label>
					<!--end::Label-->
					<!--begin::Col-->
					<div class="col-lg-8 fv-row">
						<!--begin::Input-->
						<select name="chief" aria-label="Penanggung Jawab Toko" data-control="select2" data-placeholder="Penanggung Jawab Toko" class="form-select form-select-solid form-select-lg">
							<option value="">Penanggung Jawab Toko</option>
							<?php 
							if ($_SESSION['userty']=='owner') {
								$where['a.company']	= mycompanyid();
							} else {
								$where['a.store'] 	= mystoreid();
							}
							$a = $db->table('account_store_privilage a')->join('account b','b.id=a.account')->where($where)->select('b.id,b.name,a.privilage')->get()->getResultArray();
							foreach ($a as $key => $v) {
								$sel = $v['privilage']=='chief'?'selected':'';
								echo '<option value="'.$v['id'].'" '.$sel.'>'.$v['name'].'</option>';
							} ?>
						</select>
						<!--end::Input-->
						<!--begin::Hint-->
						<div class="form-text">Daftar penanggung jawab di atas, diambil dari data petugas, jika data di atas kosong, harap menambahkan data petugas terlebih dahulu <a href="<?=site_url('petugas/tambah')?>">Disini</a></div>
						<!--end::Hint-->
					</div>
					<!--end::Col-->
				</div>
				<!--end::Input group-->  
				<!--begin::Input group-->
				<div class="row mb-0">
					<!--begin::Label-->
					<label class="col-lg-4 col-form-label fw-bold fs-6">Status</label>
					<!--begin::Label-->
					<!--begin::Label-->
					<div class="col-lg-8 d-flex align-items-center">
						<div class="form-check form-check-solid form-switch fv-row">
							<input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" name="save[status]"  <?=isset($data['status'])?($data['status']=='active'?'checked="checked"':''):'checked="checked"'?> />
							<label class="form-check-label" for="allowmarketing" style="padding-top: 5px;"> Aktif</label>
						</div>
					</div>
					<!--begin::Label-->
				</div>
				<!--end::Input group-->
			</div>
			<!--end::Card body-->
			<!--begin::Actions-->
			<div class="card-footer d-flex justify-content-end py-6 px-9">
				<div onclick="batal()" class="btn btn-light btn-active-light-primary me-2">Batal</div>
				<div onclick="simpan()" class="btn btn-primary me-10" id="simpanform">
				    <span class="indicator-label">
				        Simpan
				    </span>
				    <span class="indicator-progress">
				        Sedang Menyimpan ... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
				    </span>
				</div>
				
				<script>
					let isSubmitting = false;
					var button = document.querySelector("#simpanform");

					function batal(){
						window.location.replace("<?=$redirecturl?>");
					}
					function simpan(){
							button.setAttribute("data-kt-indicator", "on"); 
							if (isSubmitting) return; // ✅ Cegah klik ke-2
	  						isSubmitting = true;

						    let formData = new FormData(document.getElementById("forming"));
						    $.ajax({
						        url: "<?=site_url('toko/simpan')?>",  // atau route CI4 kamu
						        type: "POST",
						        data: formData,
						        processData: false, // WAJIB false
						        contentType: false, // WAJIB false
						        success: function(res){
						        	button.removeAttribute("data-kt-indicator");
						        	isSubmitting = false;
						        	var y = JSON.parse(res);
						        	if (y.status==true) {
						        		Swal.fire({
									        text: y.message,
									        icon: "success",
									        timer: 2000, // 2 detik
										  	showConfirmButton: false
										}).then(() => {
										  	window.location.replace("<?=$redirecturl?>");
										});
						        	} else {
						        		isSubmitting = false;
						        		Swal.fire({
									        text: y.message,
									        icon: "warning",
									        buttonsStyling: false,
									        confirmButtonText: "Ok",
									        customClass: {
									            confirmButton: "btn btn-primary"
									        }
									    }).then((result) => {
										  	if (result.isConfirmed) {
										    	//doSomething(); // ✅ hanya jalan saat OK diklik 
										  	}
										});
						        	}
						        },
						        error: function(err){
			        				isSubmitting = false;
						        	button.removeAttribute("data-kt-indicator");
						        	Swal.fire({
								        text: err,
								        icon: "error",
								        buttonsStyling: false,
								        confirmButtonText: "Ok",
								        customClass: {
								            confirmButton: "btn btn-primary"
								        }
								    }).then((result) => {
									  	if (result.isConfirmed) {
									    	//doSomething(); // ✅ hanya jalan saat OK diklik
									  	}
									});
						            console.log(err);
						        }
						    });
						
					}
				</script>
			</div>
			<!--end::Actions-->
		</form>
		<!--end::Form-->
	</div>
	<!--end::Content-->
</div>