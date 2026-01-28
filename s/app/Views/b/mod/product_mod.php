<?php $db = db_connect(); $userid = usertoken($_SESSION['usertoken']); ?>
<div class="modal fade" id="product_mod" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-fullscreen p-9">
		<div class="modal-content">
			<div class="modal-header header-bg">
				<h2 class="text-white">Produk
				<small class="ms-2 fs-7 fw-normal text-white opacity-50" id="product_title">Create, Edit, Manage projects</small></h2>
				<!--end::Modal title-->
				<!--begin::Close-->
				<div class="btn btn-sm btn-icon btn-color-white btn-active-color-primary" data-bs-dismiss="modal">
					<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
					<span class="svg-icon svg-icon-1">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
							<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
						</svg>
					</span>
					<!--end::Svg Icon-->
				</div>
				<!--end::Close-->
			</div>
			<!--end::Modal header-->
			<!--begin::Modal body-->
			<div class="modal-body scroll-y m-5" style="padding-top:0px;padding-bottom:0px;">
				<form class="mx-auto w-100" novalidate="novalidate" id="product_mod_form" method="post">
					<!--begin::Settings--> 
					<div class="row">
					    <div class="col-lg-12">
							<div class="pb-6">
								<h1 class="fw-bolder text-dark" style="text-align:center;">Informasi Produk</h1>
								<div class="text-muted fw-bold fs-4" style="text-align:center;">Harap Informasi produk lengkap <a href="#" class="link-primary">Project Guidelines</a></div>
							</div>
					    </div>
						<div class="col-lg-6 col-xs-12"> 
							<div class="fv-row mb-8"> 
								<label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
									<span class="required">Nama Produk</span>
									<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Specify project name"></i>
								</label> 
								<input type="text" class="form-control form-control-solid" placeholder="Nama Produk" value="" name="product[name]" />
							</div> 
							<div class="fv-row mb-8"> 
								<label class="required fs-6 fw-bold mb-2">Deskripsi Produk</label> 
								<textarea class="form-control form-control-solid" rows="3" placeholder="Deskripsi Produk" name="product[description]"></textarea> 
							</div>  
							<div class="fv-row mb-8">
								<!--begin::Dropzone-->
								<div class="dropzone" id="project_mod_settings_logo">
									<div class="dz-message needsclick">
										<span class="svg-icon svg-icon-3hx svg-icon-primary">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM16 12.6L12.7 9.3C12.3 8.9 11.7 8.9 11.3 9.3L8 12.6H11V18C11 18.6 11.4 19 12 19C12.6 19 13 18.6 13 18V12.6H16Z" fill="black" />
												<path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black" />
											</svg>
										</span>
										<div class="ms-4">
											<h3 class="dfs-3 fw-bolder text-gray-900 mb-1">Upload Foto Produk.</h3>
											<span class="fw-bold fs-4 text-muted">maksimal 10 foto</span>
										</div>
									</div>
								</div>
								<script>
								    new Dropzone("#project_mod_settings_logo", {
                                        url: "https://keenthemes.com/scripts/void.php",
                                        paramName: "file",
                                        maxFiles: 10,
                                        maxFilesize: 10,
                                        addRemoveLinks: !0,
                                        accept: function (e, t) {
                                            "justinbieber.jpg" == e.name ? t("Naha, you don't.") : t();
                                        },
                                    });
								</script>
								<!--end::Dropzone-->
							</div>
                        </div>

						<div class="col-lg-6 col-xs-12"> 
							 
							<div class="row mb-8">
								<!--begin::Label-->
								<?php if($_SESSION['userty']=='owner'){ ?>
									<div class="col-lg-6 col-xs-12">
										<a href="<?=site_url('toko')?>"><label class="required fs-6 fw-bold mb-2">Toko</label></a>
										<!--end::Label-->
										<!--begin::Input-->
										<select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Select..." name="settings_customer">
											<option></option>
											<?php foreach ($db->table('account_store')->where(['owner'=>$userid])->get()->getResultArray() as $key => $v) {
												$sel = isset($data['store'])&&$data['store']==$v['id']?'selected':'';
												echo '<option value="'.$v['id'].'" '.$sel.'>'.$v['name'].'</option>';
											} ?>
										</select>
										<!--end::Input-->
									</div>
								<?php } ?>
								
								<div class="col-lg-6 col-xs-12">
									<label class="required fs-6 fw-bold mb-2">Kategori Produk</label>
									<select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Select a Team Member" name="target_assign">
										<option></option>
										<option value="1">Karina Clark</option>
										<option value="2" selected="selected">Robert Doe</option>
										<option value="3">Niel Owen</option>
										<option value="4">Olivia Wild</option>
										<option value="5">Sean Bean</option>
									</select>
								</div> 
							</div>
							<!--begin::Input group-->
							<div class="row mb-8">
								<div class="col-6"> 
								    <style>
								        .nothing {
								            width:100%;
                                            outline: none;
                                            border: none;
                                            box-shadow: none;
								            background: transparent;
								            color: black;
								        }
								        .nothing:focus {
								            width:100%;
                                            outline: none;
                                            border: none;
                                            box-shadow: none;
								            background: transparent;
								        } 

								    </style>
									<label class="fs-6 fw-bold mb-2">
									    <input type="text" placeholder="Variasi 1" class="nothing" />
									</label> 
									<input type="text" class="form-control form-control-solid" placeholder="Variasi seperti : warna, ukuran" name="variant1" id="variant1" /> 
								</div>
								<div class="col-6"> 
									<label class="fs-6 fw-bold mb-2">Variasi 2</label> 
									<input type="text" class="form-control form-control-solid" placeholder="Variasi seperti : warna, ukuran" name="variant2" id="variant2"  /> 
								</div>
								<script>
								    var input1 = document.querySelector("#variant1");
                                    var input2 = document.querySelector("#variant2");
                                    
                                    // Initialize Tagify components on the above inputs
                                    new Tagify(input1);
                                    new Tagify(input2);
								</script>
							</div>
							<!--end::Input group-->
							<!--begin::Input group-->
							<div class="mb-8">
								<!--begin::Label-->
								<div class="fs-6 fw-bold mb-2">Harga</div>
								<!--end::Label-->
								<!--begin::Users-->
								<div class="mh-300px scroll-y me-n7 pe-7">
									<!--begin::User-->
									<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
										<div class="d-flex align-items-center">
											<div class="symbol symbol-35px symbol-circle">
												<img alt="Pic" src="<?=base_url('t/')?>media/avatars/150-1.jpg" />
											</div>
											<div class="ms-5">
												<a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary mb-2">Ukuran</a>
												<div class="fw-bold text-muted">Variasi 1</div>
											</div>
										</div>
										<div class="ms-2 w-100px">
											<input type="text" class="form-control form-control-solid" placeholder="Harga Warung" name="invite_teammates" />
										</div>
										<div class="ms-2 w-100px">
											<input type="text" class="form-control form-control-solid" placeholder="Harga Warung" name="invite_teammates" />
										</div>
									</div>
									<!--end::User-->
									<!--begin::User-->
									<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
										<div class="d-flex align-items-center">
											<!--begin::Avatar-->
											<div class="symbol symbol-35px symbol-circle">
												<span class="symbol-label bg-light-danger text-danger fw-bold">M</span>
											</div>
											<div class="ms-5">
												<a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary mb-2">Warna</a>
												<div class="fw-bold text-muted">Variasi 2</div>
											</div>
										</div>
										<div class="ms-2 w-100px">
											<input type="text" class="form-control form-control-solid" placeholder="Harga Konsumen" name="invite_teammates" />
										</div>
										<div class="ms-2 w-100px">
											<input type="text" class="form-control form-control-solid" placeholder="Harga Konsumen" name="invite_teammates" />
										</div>
									</div>
									<!--end::User--> 
								</div>
								<!--end::Users-->
							</div>
							<!--end::Input group--> 
							<div class="fv-row mb-8">
								<div class="d-flex flex-stack">
									<div class="me-5">
										<label class="fs-6 fw-bold">Publis</label>
										<div class="fs-7 fw-bold text-muted">Jika Tidak publish, maka produk tidak akan muncul di halaman transaksi</div>
									</div>
									<label class="form-check form-switch form-check-custom form-check-solid">
										<input class="form-check-input" type="checkbox" value="1" name="target_allow" checked="checked" />
										<span class="form-check-label fw-bold text-muted">Tampil</span>
									</label>
								</div>
							</div>
							<!--end::Notice-->
						</div>
					</div>
					
				</form>
			</div>
			
			<div class="modal-footer footer-bg">
			    <div class="row">
					<div class="col-lg-12">
						<div class="d-flex flex-stack">
							<button type="button" class="btn btn-lg btn-light me-3" onclick="productmodclose()">Tutup</button>
							<button type="button" class="btn btn-lg btn-primary"  onclick="productmodnext()" id="product_mod_btn_1">
								<span class="indicator-label">Simpan</span>
								<span class="indicator-progress">Menyimpan...
								<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
							</button>
						</div>
					</div>
			    </div>
			</div>
			<!--end::Modal body-->
		</div>
		<!--end::Modal content-->
	</div>
	<!--end::Modal dialog-->
</div>
<script> 
	function projectmod(id = 0) { 
    	$("#product_mod").modal('show');
	    if (id !== 0) { 
	    	$("#product_title").html('Ubah Data Produk');
	        $.post(getproducturl, { id:id }) // kirim ID agar server tahu data mana yg diminta
	            .done(function(res){
	                try {
	                    let data = JSON.parse(res); // jika server return JSON plain
	                    console.log(data);

	                    // contoh: tampilkan data ke form
	                    $("#project_name").val(data.name);
	                    $("#project_desc").val(data.desc);
	                } catch(e) {
	                    console.error("Invalid JSON response", e);
	                }
	            })
	            .fail(function(xhr){
	                console.error("Request gagal:", xhr.status);
	            });
	    } else {
	    	$("#product_title").html('Tambah Produk');
	    }
	}
	function productmodclose(){
		$("#product_mod").modal('hide');
		$(".product_val").val('');
	}
 





</script>