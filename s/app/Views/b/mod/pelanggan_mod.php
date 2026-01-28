<div class="modal fade" id="pelanggan_mod" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-fullscreen p-9">
		<div class="modal-content">
			<div class="modal-header header-bg">
				<h2 class="text-white">Produk
				<small class="ms-2 fs-7 fw-normal text-white opacity-50" id="pelanggan_title">Create, Edit, Manage projects</small></h2>
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
			<div class="modal-body scroll-y m-5">
				<!--begin::Stepper-->
				<div class="stepper stepper-pills d-flex flex-column" id="project_mod_stepper">
					<!--begin::Container-->
					<div class="container">
						<!--begin::Nav-->
						<div class="stepper-nav justify-content-center py-2">
							<!--begin::Step 1--> 

							<div class="stepper-item mx-2 my-4 current" data-kt-stepper-element="nav">
					            <div class="stepper-line w-40px"></div>
					            <div class="stepper-icon w-40px h-40px"> <i class="stepper-check fas fa-check"></i> <span class="stepper-number">1</span> </div>
					            <div class="stepper-label"> <h3 class="stepper-title"> Step 1 </h3> <div class="stepper-desc"> Produk </div> </div>
					        </div>
					        <!--end::Step 1-->

					        <!--begin::Step 2-->
					        <div class="stepper-item mx-2 my-4" data-kt-stepper-element="nav">
					            <div class="stepper-line w-40px"></div>
					            <div class="stepper-icon w-40px h-40px"> <i class="stepper-check fas fa-check"></i> <span class="stepper-number">2</span> </div>
					            <div class="stepper-label"> <h3 class="stepper-title"> Step 2 </h3> <div class="stepper-desc"> Harga </div> </div>
					        </div>
					        <!--end::Step 2-->

					        <!--begin::Step 3-->
					        <div class="stepper-item mx-2 my-4" data-kt-stepper-element="nav">
					            <div class="stepper-line w-40px"></div>
					            <div class="stepper-icon w-40px h-40px"> <i class="stepper-check fas fa-check"></i> <span class="stepper-number">3</span> </div>
					            <div class="stepper-label"> <h3 class="stepper-title"> Step 3 </h3> <div class="stepper-desc"> Selesai </div> </div>
					        </div>
					        <!--end::Step 3-->
 
						</div>
						<!--end::Nav-->
						<!--begin::Form-->
						<form class="mx-auto w-100 mw-600px pt-15 pb-10" novalidate="novalidate" id="pelanggan_mod_form" method="post">
							<!--begin::Settings-->
							<div class="current" data-kt-stepper-element="content">
								<!--begin::Wrapper-->
								<div class="w-100">
									<!--begin::Heading-->
									<div class="pb-12">
										<!--begin::Title-->
										<h1 class="fw-bolder text-dark">Informasi Produk</h1>
										<!--end::Title-->
										<!--begin::Description-->
										<div class="text-muted fw-bold fs-4">Harap Informasi produk lengkap
										<a href="#" class="link-primary">Project Guidelines</a></div>
										<!--end::Description-->
									</div>
									<!--end::Heading-->
									<!--begin::Input group-->
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
										<!--end::Dropzone-->
									</div>
									<!--end::Input group-->
									<!--begin::Input group-->
									<div class="fv-row mb-8">
										<!--begin::Label-->
										<label class="required fs-6 fw-bold mb-2">Toko</label>
										<!--end::Label-->
										<!--begin::Input-->
										<select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Select..." name="settings_customer">
											<option></option>
											<option value="1">Keenthemes</option>
											<option value="2">CRM App</option>
										</select>
										<!--end::Input-->
									</div>
									<!--end::Input group-->
									<!--begin::Input group-->
									<div class="fv-row mb-8">
										<!--begin::Label-->
										<label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
											<span class="required">Nama Produk</span>
											<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Specify project name"></i>
										</label>
										<!--end::Label-->
										<!--begin::Input-->
										<input type="text" class="form-control form-control-solid" placeholder="Enter Project Name" value="StockPro Mobile App" name="settings_name" />
										<!--end::Input-->
									</div>
									<!--end::Input group-->
									<!--begin::Input group-->
									<div class="fv-row mb-8">
										<!--begin::Label-->
										<label class="required fs-6 fw-bold mb-2">Deskripsi Produk</label>
										<!--end::Label-->
										<!--begin::Input-->
										<textarea class="form-control form-control-solid" rows="3" placeholder="Enter Project Description" name="settings_description">Experience share market at your fingertips with TICK PRO stock investment mobile trading app</textarea>
										<!--end::Input-->
									</div>
									<!--end::Input group-->  
  
									<!--begin::Input group-->
									<div class="row g-9 mb-8"> 
										<div class="col-md-6 fv-row">
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
									<!--end::Input group--> 
									<!--begin::Actions-->
									<div class="d-flex flex-stack">
										<button type="button" class="btn btn-lg btn-light me-3" onclick="pelangganmodclose()">Tutup</button>
										<button type="button" class="btn btn-lg btn-primary"  onclick="pelangganmodnext()" id="pelanggan_mod_btn_1">
											<span class="indicator-label">Selanjutnya</span>
											<span class="indicator-progress">Menyimpan...
											<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										</button>
									</div>
									<!--end::Actions-->
								</div>
								<!--end::Wrapper-->
							</div>
							<!--end::Settings-->
							<!--begin::Budget-->
							<div data-kt-stepper-element="content">
								<!--begin::Wrapper-->
								<div class="w-100">
									<!--begin::Heading-->
									<div class="pb-10 pb-lg-12">
										<!--begin::Title-->
										<h1 class="fw-bolder text-dark">Harga</h1>
										<!--end::Title-->
										<!--begin::Description-->
										<div class="text-muted fw-bold fs-4">If you need more info, please check
										<a href="#" class="link-primary">Project Guidelines</a></div>
										<!--end::Description-->
									</div>
									<!--end::Heading-->
									
 
									<div class="fv-row mb-15">
										<div class="d-flex flex-stack">
											<div class="me-5">
												<label class="required fs-6 fw-bold">Variasi</label>
												<div class="fs-7 fw-bold text-muted">harap di isi setidaknya 1 variasi</div>
											</div>
											<div class="d-flex">
												<label class="form-check form-check-custom form-check-solid me-10">
													<input class="form-check-input h-20px w-20px" type="checkbox" value="email" name="settings_notifications[]" />
													<span class="form-check-label fw-bold">Variasi 1</span>
												</label>
												<label class="form-check form-check-custom form-check-solid">
													<input class="form-check-input h-20px w-20px" type="checkbox" value="phone" checked="checked" name="settings_notifications[]" />
													<span class="form-check-label fw-bold">Variasi 2</span>
												</label>
											</div>
										</div>
									</div>
									<!--begin::Input group-->
									<div class="mb-8">
										<!--begin::Label-->
										<label class="fs-6 fw-bold mb-2">Variasi 1</label>
										<!--end::Label-->
										<!--begin::Input-->
										<input type="text" class="form-control form-control-solid" placeholder="Variasi seperti : warna, ukuran" name="variant1" />
										<!--end::Input-->
									</div>
									<div class="mb-8">
										<!--begin::Label-->
										<label class="fs-6 fw-bold mb-2">Variasi 2</label>
										<!--end::Label-->
										<!--begin::Input-->
										<input type="text" class="form-control form-control-solid" placeholder="Variasi seperti : warna, ukuran" name="variant2" />
										<!--end::Input-->
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
									<!--begin::Notice-->
									<div class="d-flex flex-stack mb-15">
										<!--begin::Label-->
										<div class="me-5 fw-bold">
											<label class="fs-6">Adding Users by Team Members</label>
											<div class="fs-7 text-muted">If you need more info, please check budget planning</div>
										</div>
										<!--end::Label-->
										<!--begin::Switch-->
										<label class="form-check form-switch form-check-custom form-check-solid">
											<input class="form-check-input" type="checkbox" value="" checked="checked" />
										</label>
										<!--end::Switch-->
									</div>
									<!--end::Notice-->
									<!--begin::Actions-->
									<div class="d-flex flex-stack">
										<button type="button" class="btn btn-lg btn-light me-3" onclick="pelangganmodprev()">Kembali</button>
										<button type="button" class="btn btn-lg btn-primary" onclick="pelangganmodnext(2)" id="pelanggan_mod_btn_2">
											<span class="indicator-label">Selanjutnya</span>
											<span class="indicator-progress">Menyimpan...
											<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										</button>
									</div>
									<!--end::Actions-->
								</div>
								<!--end::Wrapper-->
							</div>
							<!--end::Harga-->  
							<!--begin::Complete-->
							<div data-kt-stepper-element="content">
								<!--begin::Wrapper-->
								<div class="w-100">
									<!--begin::Heading-->
									<div class="pb-12 text-center">
										<!--begin::Title-->
										<h1 class="fw-bolder text-dark">Project Created!</h1>
										<!--end::Title-->
										<!--begin::Description-->
										<div class="text-muted fw-bold fs-4">If you need more info, please check how to create project</div>
										<!--end::Description-->
									</div>
									<!--end::Heading-->
									<!--begin::Actions-->
									<div class="d-flex flex-center pb-20">
										<button type="button" class="btn btn-lg btn-light me-3" data-kt-element="complete-start">Create New Project</button>
										<a href="" class="btn btn-lg btn-primary" data-bs-toggle="tooltip" title="Coming Soon">View Project</a>
									</div>
									<!--end::Actions-->
									<!--begin::Illustration-->
									<div class="text-center px-4">
										<img src="<?=base_url('t/')?>media/illustrations/sigma-1/9.png" alt="" class="mww-100 mh-350px" />
									</div>
									<!--end::Illustration-->
								</div>
							</div>
							<!--end::Complete-->
						</form>
						<!--end::Form-->
					</div>
					<!--begin::Container-->
				</div>
				<!--end::Stepper-->
			</div>
			<!--end::Modal body-->
		</div>
		<!--end::Modal content-->
	</div>
	<!--end::Modal dialog-->
</div>
<script>
	var element = document.querySelector("#project_mod_stepper");
	var stepper = new KTStepper(element);
	function pelangganmod(id = 0) {
		stepper.goTo(1);
    	$("#pelanggan_mod").modal('show');
	    if (id !== 0) { 
	    	$("#pelanggan_title").html('Ubah Data Produk');
	        $.post(getpelangganurl, { id:id }) // kirim ID agar server tahu data mana yg diminta
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
	    	$("#pelanggan_title").html('Tambah Produk');
	    }
	}
	function pelangganmodclose(){
		$("#pelanggan_mod").modal('hide');
		$(".pelanggan_val").val('');
	}

	function pelangganmodnext(){ 
		stepper.goNext(); // go next step
		document.getElementById("project_mod_stepper").scrollIntoView({behavior: "smooth"});
	}
	function pelangganmodprev(){
		stepper.goPrevious(); // go next step
		document.getElementById("project_mod_stepper").scrollIntoView({behavior: "smooth"});
	}


	// Handle previous step
	stepper.on("kt.stepper.previous", function (stepper) {
	    stepper.goPrevious(); // go previous step
	});





</script>