<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$company 	= $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
	$owner  	= $db->table('account')->where(['id'=>$userid])->get()->getRowArray();
	if (isset($company['id'])) {
		$companyid = $company['id'];
	} else {
		$db->table('account_company')->insert(['owner'=>$userid]);
		$companyid = $db->insertID();
	}
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_pengaturan.php'); 
    //session()->set('redirect_store', current_url());
    session()->set('redirect_worker', current_url());
    $redirecturl = session()->get('redirect_worker') ?? site_url('pengaturan/perusahaan');
?>


<div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
	<div class="card-header cursor-pointer">
		<div class="card-title m-0">
			<h3 class="fw-bolder m-0">Data Owner</h3>
		</div>
		<a href="<?=site_url('user/perusahaan')?>" class="btn btn-primary align-self-center">Ubah Data</a>
	</div>
	<form id="owner_form" class="form" method="post">
		<div class="card-body p-9">
			<input type="hidden" name="id" value="<?=isset($userid)?enkripsi($userid):''?>">
				<div class="row mb-7">
					<label class="col-lg-3 fw-bold text-muted">Foto Owner</label>
					<div class="col-lg-9">
						<span class="fw-bolder fs-6 text-gray-800">
							<div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url(<?=isset($owner['foto'])&&$owner['foto']!=''?base_url('f/'.$owner['foto']):base_url('f/'.sys('nofoto'))?>)">
								<div class="image-input-wrapper w-125px h-125px" style="background-image: url(<?=isset($owner['foto'])&&$owner['foto']!=''?base_url('f/'.$owner['foto']):base_url('f/'.sys('nofoto'))?>)"></div>
								<label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
									<i class="bi bi-pencil-fill fs-7"></i>
									<input type="file" name="foto" accept=".png, .jpg, .jpeg" />
									<input type="hidden" name="owner[foto]" value="<?=isset($owner['foto'])?$owner['foto']:''?>" />
								</label>
								<span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
									<i class="bi bi-x fs-2"></i>
								</span>
								<span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
									<i class="bi bi-x fs-2"></i>
								</span>
							</div>
							<div class="form-text">Allowed file types: png, jpg, jpeg.</div>
						</span>
					</div>
				</div>
				<div class="row mb-7">
					<label class="col-lg-3 fw-bold text-muted">Nama Owner</label>
					<div class="col-lg-9">
						<span class="fw-bolder fs-6 text-gray-800"><input type="text" name="owner[name]" class="form-control form-control-lg form-control-solid" placeholder="Nama Lengkap" value="<?=isset($owner['name'])?$owner['name']:''?>" /></span>
					</div>
				</div>
				<div class="row mb-7">
					<label class="col-lg-3 fw-bold text-muted">E-Mail</label>
					<div class="col-lg-9 fv-row">
						<span class="fw-bold text-gray-800 fs-6"><input type="text" name="owner[email]" class="form-control form-control-lg form-control-solid" placeholder="E-Mail" value="<?=isset($owner['email'])?$owner['email']:''?>" /></span>
					</div>
				</div>
				<div class="row mb-7">
					<label class="col-lg-3 fw-bold text-muted">Username</label>
					<div class="col-lg-9 fv-row">
						<span class="fw-bold text-gray-800 fs-6"><input type="text" name="user" class="form-control form-control-lg form-control-solid" placeholder="Username" value="<?=isset($owner['user'])?$owner['user']:''?>" readonly /></span>
					</div>
				</div>
				<div class="row mb-7"> 
					<label class="col-lg-3 fw-bold text-muted">Password</label>
					<div class="col-lg-9">
						<div class="row">
							<div class="col-lg-6 fv-row">
								<input type="text" name="pass" id="pass" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="" placeholder="password"/>
							</div>
							<div class="col-lg-6 fv-row">
								<input type="text" name="pass2" id="pass2" class="form-control form-control-lg form-control-solid" value="" placeholder="Ulangi Password"/>
							</div>
						</div>
					</div>
				</div>
				<div class="row mb-7">
					<label class="col-lg-3 fw-bold text-muted">Telepon
					<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Telepon Harus Aktif"></i></label>
					<div class="col-lg-9 d-flex align-items-center">
						<span class="fw-bolder fs-6 text-gray-800 me-2"><input type="text" name="owner[phone]" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon" value="<?=isset($owner['phone'])?$owner['phone']:''?>" /></span>
					</div>
				</div>
				<div class="row mb-7">
					<label class="col-lg-3 fw-bold text-muted">Alamat</label>
					<div class="col-lg-9">
						<a href="#" class="fw-bold fs-6 text-gray-800 text-hover-primary"><input type="text" name="owner[address]" class="form-control form-control-lg form-control-solid" placeholder="Alamat" value="<?=isset($owner['address'])?$owner['address']:''?>" /></a>
					</div>
				</div>
		</div>
		<div class="card-footer d-flex justify-content-end py-6 px-9">
			<button type="reset" class="btn btn-light btn-active-light-primary me-2">Batal</button> 
			<div onclick="saveowner()" class="btn btn-primary me-10" id="simpanownerform">
			    <span class="indicator-label">
			        Simpan
			    </span>
			    <span class="indicator-progress">
			        Sedang Menyimpan ... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
			    </span>
			</div>
			<script>
				let isSubmitting = false;
				var buttonowner = document.querySelector("#simpanownerform");
				function saveowner(){
					var a = document.getElementById('pass').value;
					var b = document.getElementById('pass2').value;
					if(a !== "" && a !== b) { Swal.fire({ text: "password tidak sama", icon: "error", timer: 2000, showConfirmButton: false });  return false; } 
				    buttonowner.setAttribute("data-kt-indicator", "on"); 
						if (isSubmitting) return; // ✅ Cegah klik ke-2
							isSubmitting = true;

					    let formData = new FormData(document.getElementById("owner_form"));
					    $.ajax({
					        url: "<?=site_url('pengaturan/simpan/owner')?>",  // atau route CI4 kamu
					        type: "POST",
					        data: formData,
					        processData: false, // WAJIB false
					        contentType: false, // WAJIB false
					        success: function(res){
					        	buttonowner.removeAttribute("data-kt-indicator");
					        	isSubmitting = false;
					        	var y = JSON.parse(res);
					        	if (y.status==true) {
					        		Swal.fire({
								        text: y.message,
								        icon: "success",
								        timer: 2000, // 2 detik
									  	showConfirmButton: false
									}).then(() => {
									  	//window.location.replace("<?=$redirecturl?>");
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
					        	buttonowner.removeAttribute("data-kt-indicator");
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
	</form>
</div> 


<div class="card mb-5 mb-xl-10">
	<div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
		<div class="card-title m-0">
			<h3 class="fw-bolder m-0">Profil Perusahaan</h3>
		</div>
	</div>
	<div id="kt_account_settings_profile_details" class="collapse show">
		<form id="company_form" class="form" method="post">
			<input type="hidden" name="id" value="<?=isset($companyid)?enkripsi($companyid):''?>">
			<div class="card-body border-top p-9">
				<div class="row mb-6">
					<label class="col-lg-3 col-form-label fw-bold fs-6">Logo Perusahaan</label>
					<div class="col-lg-9">
						<div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url(<?=isset($company['foto'])&&$company['foto']!=''?base_url('f/'.$company['foto']):base_url('f/'.sys('nofoto'))?>)">
							<div class="image-input-wrapper w-125px h-125px" style="background-image: url(<?=isset($company['foto'])&&$company['foto']!=''?base_url('f/'.$company['foto']):base_url('f/'.sys('nofoto'))?>)"></div>
							<label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
								<i class="bi bi-pencil-fill fs-7"></i>
								<input type="file" name="foto" accept=".png, .jpg, .jpeg" />
								<input type="hidden" name="company[foto]" value="<?=isset($company['foto'])?$company['foto']:''?>" />
							</label>
							<span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
								<i class="bi bi-x fs-2"></i>
							</span>
							<span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
								<i class="bi bi-x fs-2"></i>
							</span>
						</div>
						<div class="form-text">Allowed file types: png, jpg, jpeg.</div>
					</div>
				</div> 
				<div class="row mb-6">
					<label class="col-lg-3 col-form-label required fw-bold fs-6">Nama Perusahaan</label>
					<div class="col-lg-6 fv-row">
						<input type="text" name="company[name]" class="form-control form-control-lg form-control-solid" placeholder="Nama Perusahaan" value="<?=isset($company['name'])?$company['name']:''?>" />
					</div>
					<div class="col-lg-3 fv-row">
						<select class="form-select" data-control="select2" name="company[type]">
							<?php $arr = array('restoran','toko'); 
							foreach ($arr as $key => $value) {
								$sel = isset($company['type'])&&$company['type']==$value?'selected':'';
								echo '<option value="'.$value.'" '.$sel.'>'.$value.'</option>';
							} ?>
						</select>
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-3 col-form-label required fw-bold fs-6">Deskripsi Perusahaan</label>
					<div class="col-lg-9 fv-row">
						<input type="text" name="company[description]" class="form-control form-control-lg form-control-solid" placeholder="Deskripsi Perusahaan" value="<?=isset($company['description'])?$company['description']:''?>" />
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-3 col-form-label fw-bold fs-6">
						<span class="required">Telepon</span>
						<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Nomor Telepon"></i>
					</label>
					<div class="col-lg-9 fv-row">
						<input type="text" name="company[phone]" class="form-control form-control-lg form-control-solid" placeholder="Nomor Telepon" value="<?=isset($company['phone'])?$company['phone']:''?>" />
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-3 col-form-label fw-bold fs-6">Alamat</label>
					<div class="col-lg-9 fv-row">
						<input type="text" name="company[address]" class="form-control form-control-lg form-control-solid" placeholder="Alamat" value="<?=isset($company['address'])?$company['address']:''?>" />
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-3 col-form-label fw-bold fs-6">Website</label>
					<div class="col-lg-9 fv-row">
						<input type="text" name="company[website]" class="form-control form-control-lg form-control-solid" placeholder="Website" value="<?=isset($company['website'])?$company['website']:''?>" />
					</div>
				</div>
				<div class="row mb-6">
					<label class="col-lg-3 col-form-label fw-bold fs-6">E-Mail</label>
					<div class="col-lg-9 fv-row">
						<input type="text" name="company[email]" class="form-control form-control-lg form-control-solid" placeholder="E-Mail" value="<?=isset($company['email'])?$company['email']:''?>" />
					</div>
				</div>
			</div>
			<div class="card-footer d-flex justify-content-end py-6 px-9">
				<button type="reset" class="btn btn-light btn-active-light-primary me-2">Batal</button>
				<div onclick="savecompany()" class="btn btn-primary me-10" id="simpancompanyform">
				    <span class="indicator-label">
				        Simpan
				    </span>
				    <span class="indicator-progress">
				        Sedang Menyimpan ... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
				    </span>
				</div>
				<script>
					let isSubmittingc = false;
					var buttoncompany = document.querySelector("#simpancompanyform");
					function savecompany(){
					    buttoncompany.setAttribute("data-kt-indicator", "on"); 
							if (isSubmittingc) return; // ✅ Cegah klik ke-2
								isSubmittingc = true;

						    let formData = new FormData(document.getElementById("company_form"));
						    $.ajax({
						        url: "<?=site_url('pengaturan/simpan/company')?>",  // atau route CI4 kamu
						        type: "POST",
						        data: formData,
						        processData: false, // WAJIB false
						        contentType: false, // WAJIB false
						        success: function(res){
						        	buttoncompany.removeAttribute("data-kt-indicator");
						        	isSubmittingc = false;
						        	var y = JSON.parse(res);
						        	if (y.status==true) {
						        		Swal.fire({
									        text: y.message,
									        icon: "success",
									        timer: 2000, // 2 detik
										  	showConfirmButton: false
										}).then(() => {
										  	//window.location.replace("<?=$redirecturl?>");
										});
						        	} else {
						        		isSubmittingc = false;
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
			        				isSubmittingc = false;
						        	buttoncompany.removeAttribute("data-kt-indicator");
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
		</form>
	</div>
</div>