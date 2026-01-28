<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$companyid 	= companyid($userid);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    

    $redirecturl = session()->get('redirect_worker') ?? site_url('Produk/list');
	if ($_SESSION['userty']=='owner') {
		$jeniskonsumen = $db->table('account_type')->where(['company'=>$companyid,'status'=>'active'])->orderBy('id DESC')->get()->getResultArray();
	} else {
		$st = $db->table('account_store_privilage')->where(['account'=>$userid])->select('store')->get()->getRowArray();
		if (isset($st['store'])) {
			$idtokonya = $st['store'];
			$jeniskonsumen = $db->table('account_type')->where(['store'=>$st['store'],'status'=>'active'])->orderBy('id DESC')->get()->getResultArray();
		} else { $jeniskonsumen = array(); }
	}
?>
<style>
	.fotoproduklist {
		margin: 10px;
		width: 100px;

	}
	.fotoproduklist_select {
		border: 3px solid dodgerblue;
		padding: 2px;
	}
	.fotoproduklistvar {
		margin: 10px;
		width: 125px;
	}
	.fotoproduklistvar_select {
		border: 3px solid dodgerblue;
		padding: 2px;
	}
    .nothing {
        width:100%;
        outline: none;
        border: none;
        box-shadow: none;
        background: transparent;
        color: black;
        padding:5px;
        border-bottom: 1px solid silver;
    }
    .nothing:focus {
        width:100%;
        outline: none;
        border: none;
        box-shadow: none;
        background: transparent;
        padding:5px;
        border-bottom: 1px solid grey;
    }
    th {text-align: center;}

</style>
<div class="card mb-5 mb-xl-10" id="thecontent">
	<div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
		<div class="card-title m-0">
			<h3 class="fw-bolder m-0">Data Produk</h3>
		</div>
	</div>
	<div id="kt_account_settings_profile_details" class="collapse show">
		<form id="forming" class="form">
			<input type="hidden" name="id" id="theid" value="<?=isset($data['id'])?enkripsi($data['id']):0?>">
			<div class="card-body border-top p-9"> 

				<div class="row"> 
					<div class="col-lg-6 col-xs-12"> 
						<div class="row mb-8"> 
							<?php if ($_SESSION['userty']=='owner') { ?>
								
									<div class="col-lg-6 col-xs-12">
										<a href="<?=site_url('toko')?>"><label class="required fs-6 fw-bold mb-2">Toko</label></a>
										<select name="product[store]" id="thetoko" aria-label="Toko" data-control="select2" data-placeholder="Toko" class="form-select form-select-solid form-select-lg">
											<?php 
											if (isset($data['id'])) { $pv = $db->table('account_store_privilage')->where(['account'=>$data['id']])->select('store')->get()->getRowArray(); }
											$a = $db->table('account_store')->where(['owner'=>$userid])->select('id,name')->get()->getResultArray();
											foreach ($a as $key => $v) {
												$sel = isset($pv['store'])&&$pv['store']==$v['id']?'selected':'';
												echo '<option value="'.$v['id'].'" '.$sel.'>'.$v['name'].'</option>';
											} ?>
										</select>
									</div>
								
							<?php } elseif ($_SESSION['userty']=='employee') {
								$st = $db->table('account_store_privilage')->where(['account'=>$userid])->select('store')->get()->getRowArray();
								echo '<input type="hidden" name="product[store]" value="'.(isset($st['store'])?$st['store']:'').'" />';
							} ?>
							
							<div class="col-lg-6 col-xs-12">
								<label class="required fs-6 fw-bold mb-2">Kategori Produk</label>
								<select class="form-select form-select-solid form-select-lg" data-control="select2" data-hide-search="false" data-placeholder="Kategori Produk" name="product[category]">
									<?php $catx=''; 
									if ($_SESSION['userty']=='owner') {
										$pl = $db->table('account_company')->where(['owner'=>$userid])->get()->getRowArray();
										$compid = $pl['id']??'1';
										$whrr['b.company'] 		= $compid;
									} else {
										$pl = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
										$compid = $pl['company']??'1';
										$whrr['b.store'] 		= $pl['store'];
									}
									$whrr['b.status'] 	= 'ok';
									foreach($db->table('product_category a')->join('product_category_store b','b.category=a.id')->where($whrr)->groupBy('id')->orderBy('a.category ASC')->select('a.*')->get()->getResultArray() as $K => $V){
										if ($K==0) { echo '<optgroup label="'.$V['category'].'">'; }
										elseif ($catx!=$V['category']) { echo '</optgroup><optgroup label="'.$V['category'].'">'; }
										$catx = $V['category'];
										$sel = isset($data['category'])&&$data['category']==$V['id']?'selected':'';
										echo '<option value="'.$V['id'].'" '.$sel.'>'.$V['name'].'</option>';
									} echo '</optgroup>'; ?>
								</select>
							</div> 
						</div>
						<div class="fv-row mb-8"> 
							<label class="d-flex align-items-center fs-6 fw-bold form-label mb-2">
								<span class="required">Nama Produk</span>
								<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Nama Produk anda"></i>
							</label> 
							<input type="text" class="form-control form-control-solid" id="nama_produk" placeholder="Nama Produk" value="" name="product[name]" />
						</div> 
						<div class="fv-row mb-8"> 
							<label class="required fs-6 fw-bold mb-2">Deskripsi Produk</label> 
							<textarea class="form-control form-control-solid" rows="3" placeholder="Deskripsi Produk" name="product[description]"></textarea> 
						</div>  
                    </div>

					<div class="col-lg-6 col-xs-12"> 
						<div class="fv-row mb-8">
							<!--begin::Dropzone-->
							<div class="dropzone" id="foto_produk">
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
							<div class="col-12" id="fotoproduklist"></div>
							<input type="hidden" id="fotoproduk" name="fotoproduk">
							<input type="hidden" id="cover" name="product[cover]">
							<script>
							    new Dropzone("#foto_produk", {
							    	headers: { "X-CSRF-TOKEN": "<?= csrf_hash() ?>" },
                                    url: "<?=site_url('uploadfotoproduk'.(isset($data['id'])?'/'.$data['id']:''))?>",
                                    paramName: "foto",
                                    maxFiles: 10,
                                    maxFilesize: 10,
                                    addRemoveLinks: !0,
                                    accept: function (e, t) {
                                        "<?=base_url('f/'.sys('nofoto'))?>" == e.name ? t("Naha, you don't.") : t();
                                    },
                                    success: function(file, response) {
								        // Jika response masih string
								        if (typeof response === "string") {
								            response = JSON.parse(response);
								        }

								        if (response.status === true) {
								        	var fp = document.getElementById('fotoproduk').value;
								            $("#fotoproduk").val(fp+response.fotoid+';'); // simpan ke input
								            console.log("Foto ID:", response.fotoid);
								            // ✅ LANGSUNG HAPUS TAMPILAN FILE DARI DROPZONE
            								this.removeFile(file);
            								fotoproduku();
								        }
								    },

								    error: function(file, errorMessage) {
								        console.error("Upload failed:", errorMessage);
								    },
								    /*
								    removedfile: function(file) {
								        if (!file.fotoid) return;

								        fetch("<?= site_url('hapusfotoproduk') ?>", {
								            method: "POST",
								            headers: {
								                "Content-Type": "application/json",
								                "X-CSRF-TOKEN": "<?= csrf_hash() ?>"
								            },
								            body: JSON.stringify({ fotoid: file.fotoid })
								        });

								        var fp = $("#fotoproduk").val();
								        fp = fp.replace(file.fotoid + ";", "");
								        $("#fotoproduk").val(fp);

								        let preview = file.previewElement;
								        if (preview) preview.remove();
								    }*/
                                });
							    $(document).ready(function(){fotoproduku()});
                                function fotoproduku(){
                                	var a = document.getElementById('fotoproduk').value.trim();
									if (a === '') {
									    console.log('Input masih kosong');
									} else {
									    console.log('Input terisi:', a);
									    $.ajax({
										  	url: '<?=site_url('produk/listfoto')?>',
										  	type: 'POST',
										  	data: { list: document.getElementById('fotoproduk').value },
										  	dataType: 'json',
										  	success: function(res){

											    console.log('RES OBJECT:', res);

											    if (res.status === true && res.data.length > 0) {
											        let html = '';
											        let htmlvar = '';

											        res.data.forEach(function(ret){
											            html += `<img src="<?=base_url('f/')?>${ret.file}" class="fotoproduklist" onclick="selectcoverproduk(${ret.id})" data-id="${ret.id}" id="fotoproduklist${ret.id}">`;
											            htmlvar += `<img src="<?=base_url('f/')?>${ret.file}" class="fotoproduklistvar" onclick="selectimagevar(${ret.id})" data-id="${ret.id}" id="fotoproduklistvar${ret.id}">`;
											        });

											        $("#variant_choose").html(htmlvar);
											        $("#fotoproduklist").html(html);
											    } else {
											        $("#variant_choose").html('<h5 style="text-align:center;margin-top:20px;">Belum ada foto produk</h5>');
											        $("#fotoproduklist").html('<h5 style="text-align:center;margin-top:20px;">Belum ada Foto Produk</h5>');
											    }
										  	}
										});

									}
                                }
                                function selectcoverproduk(id){
                                	$(".fotoproduklist").removeClass('fotoproduklist_select');
                                	$("#fotoproduklist"+id).addClass('fotoproduklist_select');
                                	$("#cover").val(id);
                                } 
							</script>
							<!--end::Dropzone-->
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-xs-12"> 
						<div class="row mb-8" style="margin-top: 10px;padding-top: 10px;border-top: 1px dashed silver;">
							<div class="col-6"> 
								<label class="fs-6 fw-bold mb-2">
								    <input type="text" placeholder="Variasi 1" class="nothing" name="product[var1]" id="var1" />
								</label> 
								<input type="text" class="form-control form-control-solid" placeholder="Variasi seperti : warna, ukuran" name="variant1" id="variant1" /> 
								<div class="form-text">gunakan "," (koma) sebagai pemisah antar varian</a></div>
							</div>
							<div class="col-6"> 
								<label class="fs-6 fw-bold mb-2">
								    <input type="text" placeholder="Variasi 2" class="nothing" name="product[var2]" id="var2" />
								</label> 
								<input type="text" class="form-control form-control-solid" placeholder="Variasi seperti : warna, ukuran" name="variant2" id="variant2"  /> 
								<div class="form-text">gunakan "," (koma) sebagai pemisah antar varian</a></div>
							</div>
							<script>
							    var input1 = document.querySelector("#variant1");
								var input2 = document.querySelector("#variant2");

								var tagify1 = new Tagify(input1);
								var tagify2 = new Tagify(input2);

                                function generateVariants() {
                                	$("#title_var1").html($("#var1").val());
                                	$("#title_var2").html($("#var2").val());
								    const v1 = tagify1.value.map(v => v.value.toLowerCase().trim());
								    const v2 = tagify2.value.map(v => v.value.toLowerCase().trim());
								    var pro  = document.getElementById('nama_produk').value;
								    let html = '';

								    if(v1.length > 0 && v2.length > 0){
								        v1.forEach(vv1 => {
								            v2.forEach(vv2 => {
								                const vvv1 = capitalize(vv1) + ' - ' + vv2.toUpperCase();
								                const vvv2 = `variant[${vv1}][${vv2}]`;
								                var xv1 = vv1.trim().replace(/[^a-z0-9]+/g, "_").replace(/^_|_$/g, "").replace(/^[0-9]/, "_$&");
								                var xv2 = vv2.trim().replace(/[^a-z0-9]+/g, "_").replace(/^_|_$/g, "").replace(/^[0-9]/, "_$&");
								                var sku = stringnormalize(pro+'_'+xv1+'_'+xv2);
								                html += `
								                	<tr>
								                		<td>
								                			<div class="symbol symbol-35px symbol-circle" onclick="chooseimagevar('${xv1}','${xv2}')"><img id="variant_images_${xv1}_${xv2}" src="<?=base_url('f/'.sys('addfoto'))?>" /></div>
								                			<input type="hidden" id="variant_foto_${xv1}_${xv2}" name="variant[${xv1}][${xv2}][foto]" value="" />
								                		</td>
								                		<td>${xv1}</td>
								                		<td>${xv2}</td>
								                		<td><input type="text" class="form-control form-control-solid" placeholder="SKU" name="variant[${xv1}][${xv2}][sku]" value="${sku}" /></td>
								                		<td><input type="text" class="form-control form-control-solid sstok" placeholder="Stok" name="variant[${xv1}][${xv2}][stok]" /></td>
								                		<td><input type="text" class="form-control form-control-solid smin" placeholder="Stok Minimal" name="variant[${xv1}][${xv2}][min]" /></td>
								                		<td><input type="text" class="form-control form-control-solid sbase" placeholder="Harga Dasar" name="variant[${xv1}][${xv2}][base_price]" /></td>
								                		<td>
														  <div class="composition-wrapper" data-v1="${xv1}" data-v2="${xv2}">
														    <table class="table table-sm mb-2">
														      <tbody id="composition_${xv1}_${xv2}"></tbody>
														    </table>

														    <button type="button"
														      class="btn btn-light-primary btn-sm"
														      onclick="addComposition('${xv1}','${xv2}')">
														      + Komposisi
														    </button>
														  </div>
														</td>

								                		<td><input type="text"  class="form-control form-control-solid tagify-option" placeholder="Tambah opsi" name="variant[${xv1}][${xv2}][option]" /></td>
								                		
								                	</tr> 
								                `;
								            });
								        });
								    } else if(v1.length > 0){
								        v1.forEach(vv1 => {
								                var xv1 = vv1.trim().replace(/[^a-z0-9]+/g, "_").replace(/^_|_$/g, "").replace(/^[0-9]/, "_$&");
								                var sku = stringnormalize(pro+'_'+xv1);
								                var xv2 = '-';
								                html += `
								                	<tr style="border-bottom:1px dashed grey;">
								                		<td>
								                			<div class="symbol symbol-35px symbol-circle" onclick="chooseimagevar('${xv1}','_')"><img id="variant_images_${xv1}__" src="<?=base_url('f/'.sys('addfoto'))?>" /></div>
								                			<input type="hidden" id="variant_foto_${xv1}__" name="variant[${xv1}][-][foto]" value="" />
								                		</td>
								                		<td>${xv1}</td>
								                		<td></td>
								                		<td><input type="text" class="form-control form-control-solid" placeholder="SKU" name="variant[${xv1}][-][sku]" value="${sku}" /></td>
								                		<td><input type="text" class="form-control form-control-solid sstok" placeholder="Stok" name="variant[${xv1}][-][stok]" /></td>
								                		<td><input type="text" class="form-control form-control-solid smin" placeholder="Stok Minimal" name="variant[${xv1}][-][min]" /></td>
								                		<td><input type="text" class="form-control form-control-solid sbase" placeholder="Harga Dasar" name="variant[${xv1}][-][base_price]" /></td>
								                		<td>
														  <div class="composition-wrapper" data-v1="${xv1}" data-v2="${xv2}">
														    <table class="table table-sm mb-2">
														      <tbody id="composition_${xv1}_${xv2}"></tbody>
														    </table>

														    <button type="button"
														      class="btn btn-light-primary btn-sm"
														      onclick="addComposition('${xv1}','${xv2}')">
														      + Komposisi
														    </button>
														  </div>
														</td>

								                		<td><input type="text" class="form-control form-control-solid tagify-option" placeholder="Tambah opsi" name="variant[${xv1}][${xv2}][option]"/></td>
								                		
								                	</tr> 
								                `;
								        });
								    }

								    document.getElementById('variantResult').innerHTML = html;
								    // init tagify untuk input baru
									initTagifyOption(document.getElementById('variantResult'));
								}
 
								let tagifyInstances = [];

								function initTagifyOption(context = document) {
								    context.querySelectorAll('.tagify-option').forEach(input => {
								        if (input.tagify) return; // cegah double init

								        const tagify = new Tagify(input, {
								            delimiters: ",",
								            dropdown: {
								                enabled: 0
								            }
								        });

								        tagifyInstances.push(tagify);
								    });
								} 


								function addComposition(v1, v2) {
								    const tbodyId = `composition_${v1}_${v2}`;
								    const tbody   = document.getElementById(tbodyId);
								    const index   = tbody.children.length;

								    const rowId = `row_${v1}_${v2}_${index}`;
								    const selectId = `component_${v1}_${v2}_${index}`;

								    tbody.insertAdjacentHTML('beforeend', `
								        <tr id="${rowId}">
								            <td style="width:60%">
								                <select
								                    id="${selectId}"
								                    class="form-select form-select-sm component-select"
								                    name="variant[${v1}][${v2}][composition][${index}][component_id]"
								                    required></select>
								            </td>

								            <td style="width:25%">
								                <input type="number"
								                    min="1" 
								    				value="1"
								                    class="form-control form-control-sm"
								                    placeholder="Qty"
								                    name="variant[${v1}][${v2}][composition][${index}][qty]"
								                    required />
								            </td>

								            <td style="width:15%" class="text-center">
								                <button type="button"
								                    class="btn btn-light-danger btn-sm"
								                    onclick="document.getElementById('${rowId}').remove()">
								                    ✕
								                </button>
								            </td>
								        </tr>
								    `);

								    initComponentSelect(`#${selectId}`);
								}

								function initComponentSelect(selector) {
								    $(selector).select2({
								        placeholder: 'Pilih bahan',
								        allowClear: true,
								        width: '100%',
								        ajax: {
								            url: "<?= base_url('daftar_bahan/')?>"+<?=isset($idtokonya)?$idtokonya:'document.getElementById("thetoko").value'?>,
								            dataType: 'json',
								            delay: 250,
								            data: function (params) {
								                return {
								                    q: params.term
								                };
								            },
								            processResults: function (data) {
								                return {
								                    results: data.map(item => ({
								                        id: item.id,
								                        text: item.name + ' (' + item.unit + ')'
								                    }))
								                };
								            }
								        }
								    });
								}

								tagify1.on('change', generateVariants);
								tagify2.on('change', generateVariants);

								function capitalize(str){
								    return str.charAt(0).toUpperCase() + str.slice(1);
								}
								function chooseimagevar(var1,var2){
									var theid = $("#variant_foto_"+var1+"_"+var2).val();
									$(".fotoproduklistvar").removeClass('fotoproduklistvar_select');
									$("#fotoproduklistvar"+theid).addClass('fotoproduklistvar_select')
									$("#variant_choose_1").val(var1);
									$("#variant_choose_2").val(var2);
									$("#variant_choose_mod").modal('show');
									//$("#variant_choose").html('<span class="spinner-border spinner-border-sm align-middle ms-2"></span> Sedang Mengambil Gambar ...')
								}
								function selectimagevar(id){
                                	$(".fotoproduklistvar").removeClass('fotoproduklistvar_select');
                                	$("#fotoproduklistvar"+id).addClass('fotoproduklistvar_select'); 
                                	var url = $("#fotoproduklistvar"+id).attr('src');

									var v1 = $("#variant_choose_1").val();
									var v2 = $("#variant_choose_2").val();
									$("#variant_foto_"+v1+"_"+v2).val(id);
									$("#variant_images_"+v1+"_"+v2).attr('src',url);
									$("#variant_choose_mod").modal('hide');
								}
							</script>
						</div>
						<div class="col-12">
							<style>
								.iconblue {
									color: royalblue;
									font-weight: bold;
									cursor: pointer;
								}
							</style>
							<table>
								<thead><tr><th></th><th id="title_var1">Varian 1</th><th id="title_var2">Varian 2</th><th>SKU</th>
									<th>Stok <i class="fa fa-edit iconblue" onclick="samestok()"></i></th>
									<th>Minimum <i class="fa fa-edit iconblue" onclick="samemin()"></i></th>
									<th>Harga Dasar <i class="fa fa-edit iconblue" onclick="samebase()"></i></th>
									<th>Komposisi <i class="fa fa-edit iconblue" onclick="samecomposition()"></i></th>
									<th>Keterangan Opsional <i class="fa fa-edit iconblue" onclick="sameoption()"></i></th>
								</thead>
								<tbody id="variantResult"></tbody>
							</table>
							<script>
								function samestok(){
									Swal.fire({
									    title: 'Tetapkan Semua Stok',
									    input: 'text',
									    inputPlaceholder: 'Tulis jumlah Stok di sini...',
									    showCancelButton: true,
                                        reverseButtons: true,
									    confirmButtonText: 'Terapkan',
									    cancelButtonText: 'Batal'
									}).then((result) => {
									    if (result.isConfirmed) {
									        console.log(result.value)
									        $(".sstok").val(result.value)
									    }
									})

								}
								function samemin(){
									Swal.fire({
									    title: 'Tetapkan Semua Stok Minimum',
									    input: 'text',
									    inputPlaceholder: 'Tulis jumlah Stok Minimum di sini...',
									    showCancelButton: true,
                                        reverseButtons: true,
									    confirmButtonText: 'Terapkan',
									    cancelButtonText: 'Batal'
									}).then((result) => {
									    if (result.isConfirmed) {
									        console.log(result.value)
									        $(".smin").val(result.value)
									    }
									})

								}
								function samebase(){
									Swal.fire({
									    title: 'Tetapkan Semua Harga Dasar',
									    input: 'text',
									    inputPlaceholder: 'Tulis Harga Dasar di sini...',
									    showCancelButton: true,
                                        reverseButtons: true,
									    confirmButtonText: 'Terapkan',
									    cancelButtonText: 'Batal'
									}).then((result) => {
									    if (result.isConfirmed) {
									        console.log(result.value)
									        $(".sbase").val(result.value)
									    }
									})

								} 
								function samecomposition(){
									Swal.fire({
									    title: 'Tetapkan Semua Komposisi',
									    input: 'text',
									    inputPlaceholder: 'Tulis Komposisi di sini...',
									    showCancelButton: true,
                                        reverseButtons: true,
									    confirmButtonText: 'Terapkan',
									    cancelButtonText: 'Batal'
									}).then((result) => {
									    if (result.isConfirmed) {
									        console.log(result.value)
									        $(".sbase").val(result.value)
									    }
									})

								} 
								function sameoption(){
									Swal.fire({
									    title: 'Tetapkan Semua Keterangan Optional',
									    input: 'text',
									    inputPlaceholder: 'Tulis Keterangan Optional di sini...',
									    showCancelButton: true,
                                        reverseButtons: true,
									    confirmButtonText: 'Terapkan',
									    cancelButtonText: 'Batal'
									}).then((result) => {
									    if (result.isConfirmed) {
									        console.log(result.value)
									        $(".sbase").val(result.value)
									    }
									})

								} 

							</script>
						</div>
						
						<div class="fv-row mb-8">
							<div class="d-flex flex-stack">
								<div class="me-5">
									<label class="fs-6 fw-bold">Publis</label>
									<div class="fs-7 fw-bold text-muted">Jika Tidak publish, maka produk tidak akan muncul di halaman transaksi</div>
								</div>
								<label class="form-check form-switch form-check-custom form-check-solid">
									<input class="form-check-input" type="checkbox" value="1" name="publish" checked="checked" />
									<span class="form-check-label fw-bold text-muted">Tampil</span>
								</label>
							</div>
						</div>
						<!--end::Notice-->
					</div>
				</div>
 				
 				<div class="row">
 					<div class="col-lg-12">
 						<div class="mb-3">
							<label class="form-label">Satuan Terkecil (Base Unit)</label>
							<input type="text" name="base_unit" class="form-control" placeholder="contoh: batang" required value="pcs">
						</div>
						<table class="table table-bordered" id="unitTable">
						  <thead class="table-light">
						    <tr>
						      <th>Nama Satuan</th>
						      <th>Konversi ke Satuan Dibawahnya</th>
						      <th>Aksi</th>
						    </tr>
						  </thead>
						  <tbody>
						    <!-- default 1 baris -->
						    <tr>
						      <td>
						        <input type="text" class="form-control unit-name" placeholder="contoh: renceng" name="units[0][name]">
						      </td>
						      <td>
						        <input type="number" class="form-control unit-value" placeholder="contoh: 12" name="units[0][value]">
						      </td>
						      <td>
						        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
						      </td>
						    </tr>
						  </tbody>
						</table>

						<button type="button" class="btn btn-outline-primary" id="addUnit">
						  + Tambah Satuan
						</button>
						<script>
							let unitIndex = 0;
							document.getElementById('addUnit').addEventListener('click', function () {
								unitIndex++;
							  	const row = `
							    	<tr>
							      		<td><input type="text" class="form-control unit-name" required name="units[${unitIndex}][name]"></td>
							      		<td><input type="number" class="form-control unit-value" min="1" required name="units[${unitIndex}][value]"></td>
							      		<td><button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button></td>
							    	</tr>`;
							  	document.querySelector('#unitTable tbody').insertAdjacentHTML('beforeend', row);
							});

							document.addEventListener('click', function (e) {
							  if (e.target.classList.contains('remove-row')) {
							    e.target.closest('tr').remove();
							  }
							});
						</script>

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
						        url: "<?=site_url('produk/simpan')?>",  // atau route CI4 kamu
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

