<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    

    $redirecturl = session()->get('redirect_kategori') ?? site_url('pengaturan/kategori');
	$jeniskonsumen = $db->table('account_type')->where(['sales'=>1])->orderBy('id DESC')->get()->getResultArray();

	if ($id!='') {
		$cate = $db->table('product_category')->where(['id'=>$id])->get()->getRowArray();
	}
?>
<div class="card mb-5 mb-xl-10" id="thecontent">
	<div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
		<div class="card-title m-0">
			<h3 class="fw-bolder m-0">Kategori Produk</h3>
		</div>
	</div>
	<div id="kt_account_settings_profile_details" class="collapse show">
		<form id="forming" class="form">
			<input type="hidden" name="id" id="theid" value="<?=isset($cate['id'])&&$cate['id']!=''?enkripsi($cate['id']):0?>">
			<div class="card-body border-top p-9"> 

				<div class="row"> 
					<div class="col-lg-6 col-xs-12"> 
						<div class="row mb-8"> 
							<div class="col-lg-6 col-xs-12">
								<a href="<?=site_url('toko')?>"><label class="required fs-6 fw-bold mb-2">Kelompok</label></a>
								<select name="saver[category]" aria-label="Kelompok" data-control="select2" data-placeholder="Kelompok" class="form-select form-select-solid form-select-lg">
									<?php 
									$a = $db->table('product_category')->groupBy('category')->select('category,id')->get()->getResultArray();
									foreach ($a as $key => $v) {
										$sel = isset($cate['category'])&&$cate['category']==$v['category']?'selected':'';
										echo '<option value="'.$v['category'].'" '.$sel.'>'.$v['category'].'</option>';
									} ?>
								</select>
							</div>
						</div>
						<div class="fv-row mb-8"> 
							<label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
								<span class="required">Nama Kategori</span>
								<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Jenis Konsumen"></i>
							</label> 
							<input type="text" class="form-control form-control-solid" placeholder="Jenis Konsumen" name="saver[name]" value="<?=isset($cate['name'])?$cate['name']:''?>" />
						</div> 
						<div class="fv-row mb-8">

							<label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
								<span class="required">Status</span>
								<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="jika status aktif, maka akan muncul di kategori produk"></i>
							</label>
							<div class="form-check form-switch form-check-custom form-check-solid">
							    <input class="form-check-input" type="checkbox" name="status" value="1" id="flexSwitchChecked" <?=isset($cate['status'])&&$cate['status']=='active'?'checked="checked"':''?> />
							    <label class="form-check-label" for="flexSwitchChecked">
							        Aktif
							    </label>
							</div>
						</div>
                    </div>
 
				</div> 
 
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
						        url: "<?=site_url('pengaturan/kategori')?>",  // atau route CI4 kamu
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


<div class="modal fade" tabindex="-1" id="variant_choose_mod">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Silahkan Pilih Gambar Variant</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close"><span class="svg-icon svg-icon-2x"></span></div>
                <input type="hidden" id="variant_choose_1">
                <input type="hidden" id="variant_choose_2">
            </div>

            <div class="modal-body" id="variant_choose"></div>
        </div>
    </div>
</div>

