<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    

    $redirecturl = session()->get('redirect_worker') ?? site_url('petugas/list');
	
?>
<div class="card mb-5 mb-xl-10" id="thecontent">
	<div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
		<div class="card-title m-0">
			<h3 class="fw-bolder m-0">Data Petugas</h3>
		</div>
	</div>
	<div id="kt_account_settings_profile_details" class="collapse show">
		<form id="forming" class="form">
			<input type="hidden" name="id" id="theid" value="<?=isset($data['id'])?enkripsi($data['id']):0?>">
			<div class="card-body border-top p-9">
				<?php if ($_SESSION['userty']=='owner') { ?>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label required fw-bold fs-6">Toko</label>
					<div class="col-lg-8 fv-row">
						<select name="store" aria-label="Toko" data-control="select2" data-placeholder="Toko" class="form-select form-select-solid form-select-lg">
							<?php 
							if (isset($data['id'])) { $pv = $db->table('account_store_privilage')->where(['account'=>$data['id']])->select('store')->get()->getRowArray(); }
							$a = $db->table('account_store')->where(['owner'=>$userid])->select('id,name')->get()->getResultArray();
							foreach ($a as $key => $v) {
								$sel = isset($pv['store'])&&$pv['store']==$v['id']?'selected':'';
								echo '<option value="'.$v['id'].'" '.$sel.'>'.$v['name'].'</option>';
							} ?>
						</select>
						<div class="form-text">Toko tempat petugas ini terdaftar sebagai karyawan</a></div>
					</div>
				</div>
				<?php } elseif ($_SESSION['userty']=='employee') {
					$st = $db->table('account_store_privilage')->where(['account'=>$userid])->select('store')->get()->getRowArray();
					echo '<input type="hidden" name="store" value="'.(isset($st['store'])?$st['store']:'').'" />';
				} ?>
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
					<label class="col-lg-4 col-form-label  fw-bold fs-6">
						<div class="row">
							<div class="col-lg-12 fv-row"><label class="col-form-label  fw-bold fs-6">&nbsp;</label></div>
							<div class="col-lg-12 fv-row"><label class="col-form-label required fw-bold fs-6" style="text-align: left;">Nama Petugas</label></div>
						</div>
					</label>
					<div class="col-lg-5">
						<div class="row">
							<div class="col-lg-12 fv-row"><label class="col-form-label  fw-bold fs-6">&nbsp;</label></div>
							<div class="col-lg-12 fv-row">
								<input type="text" name="save[name]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Nama Lengkap Petugas" value="<?=isset($data['name'])?$data['name']:''?>" />
							</div> 
						</div>
					</div>


					
					<div class="col-lg-3 fv-row">
						<div class="col-lg-12 fv-row"><label class="col-form-label  fw-bold fs-6">Role Petugas</label></div>
						<div class="col-lg-12 fv-row">
							<select class="form-select" data-control="select2" name="save[privilage]" >
								<?php $arr = array('waiter'=>'Pelayan','chef'=>'Dapur','cashier'=>'Petugas Kasir','storekeeper'=>'Petugas Gudang','employee'=>'Pegawai Lainnya','finance'=>'Keuangan','manager'=>'Manager');
								foreach($arr as $k=>$v){
									$sel = isset($data['privilage'])&&$data['privilage']==$k?'selected':'';
									echo '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
								} ?>
							</select>
						</div>
					</div>
				</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label required fw-bold fs-6">Username</label>
					<div class="col-lg-4 fv-row">
						<input type="text" name="save[user]" class="form-control form-control-lg form-control-solid" placeholder="Username" value="<?=isset($data['user'])?$data['user']:''?>" <?=isset($data['user'])&&$data['user']!=''?'readonly':''?>  />
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label required fw-bold fs-6">Password</label>
					<div class="col-lg-8">
						<div class="row">
							<div class="col-lg-6 fv-row">
								<input type="password" name="save[pass]" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Password" id="pass" />
							</div>
							<div class="col-lg-6 fv-row">
								<input type="password" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="Ulangi Password" id="pass2" />
							</div>
						</div>
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label fw-bold fs-6">
						<span class="required">Telepon</span>
						<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Nomor Telepon"></i>
					</label>
					<div class="col-lg-4 fv-row">
						<input type="tel" name="save[phone]" class="form-control form-control-lg form-control-solid" placeholder="Telepon" value="<?=isset($data['phone'])?$data['phone']:''?>" />
					</div>
					<div class="col-lg-4 fv-row">
						<input type="tel" name="save[email]" class="form-control form-control-lg form-control-solid" placeholder="E-Mail" value="<?=isset($data['email'])?$data['email']:''?>"  />
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-4 col-form-label fw-bold fs-6">Alamat Petugas</label>
					<div class="col-lg-8 fv-row">
						<input type="text" name="save[address]" class="form-control form-control-lg form-control-solid" placeholder="Alamat Petugas" value="<?=isset($data['address'])?$data['address']:''?>" />
					</div>
				</div>  
				<div class="row mb-0">
					<!--begin::Label-->
					<label class="col-lg-4 col-form-label fw-bold fs-6">Status</label>
					<!--begin::Label-->
					<!--begin::Label-->
					<div class="col-lg-8 d-flex align-items-center">
						<div class="form-check form-check-solid form-switch fv-row">
							<input class="form-check-input w-45px h-30px" type="checkbox" id="allowmarketing" value="active" name="save[status]"  <?=isset($data['status'])?($data['status']=='active'?'checked="checked"':''):'checked="checked"'?> />
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
						var pass = document.getElementById('pass').value;
						var pass2 = document.getElementById('pass2').value;
						var theid = document.getElementById('theid').value;
						if (parseInt(theid, 10) === 0 && pass.trim() === '') {
							Swal.fire({text: "password tidak boleh kosong",icon: "error",showConfirmButton: true});
						} else if (pass !== pass2) { Swal.fire({text: "password tidak sama",icon: "error",showConfirmButton: true}); } else {
							button.setAttribute("data-kt-indicator", "on"); 
							if (isSubmitting) return; // ✅ Cegah klik ke-2
	  						isSubmitting = true;

						    let formData = new FormData(document.getElementById("forming"));
						    $.ajax({
						        url: "<?=site_url('petugas/simpan')?>",  // atau route CI4 kamu
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
					}
				</script>
			</div>
			<!--end::Actions-->
		</form>
		<!--end::Form-->
	</div>
	<!--end::Content-->
</div>