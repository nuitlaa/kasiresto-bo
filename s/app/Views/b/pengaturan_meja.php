<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$companyid 	= companyid($userid);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_pengaturan.php'); 
    //session()->set('redirect_store', current_url());
    session()->set('redirect_worker', current_url());
    if ($_SESSION['userty']=='owner') {
    	$whr['a.company'] 	= $companyid;
    } else {
    	$ap = $db->table('account_store_privilage')->where(['account'=>$userid])->select('store')->get()->getRowArray();
    	$whr['a.store'] 		= $ap['store'];
    }
    $whr['a.status'] 		= 'active';
    $meja = $db->table('account_store_table a')->join('account_store b','b.id=a.store')->select('a.*,b.name namatoko')->where($whr)->get()->getResultArray();
?>


<div class="card pt-4 mb-6 mb-xl-9"> 
	<div class="card-header border-0">
		
		<div class="card-title">
			<h2 class="fw-bolder mb-0">Meja</h2>
		</div> 

		<div class="card-toolbar">
			<div class="d-flex align-items-center position-relative my-1" style="margin-right: 20px;">
				<span class="svg-icon svg-icon-1 position-absolute ms-6">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black"></rect>
						<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black"></path>
					</svg>
				</span>
				<input type="text" data-kt-customer-table-filter="search" class="form-control form-control-solid w-250px ps-15" placeholder="Cari">
			</div>
 
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

			<a href="<?=site_url('pengaturan/meja/tambah')?>" class="btn btn-sm btn-flex btn-light-primary">
				<span class="svg-icon svg-icon-3">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black" />
						<rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black" />
						<rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black" />
					</svg>
				</span>Meja Baru
			</a>
		</div>
	</div> 
	<div id="kt_customer_view_payment_method" class="card-body pt-0">
		<table class="table">
			<style>th { text-align: center;font-weight: bold; }</style>
			<tr><th>Meja</th><th>Toko</th><th>Deskripsi</th><th>Kode Unik</th><th>Aksi</th></tr>
			<?php 
			$star = icon('star');
			$edit = icon('pencil2');
			$more = icon('more'); 
			$trash = '<i class="fa fa-trash"></i>'; 
			foreach($meja as $K=>$v){  
				?>
					<tr id="row<?=$v['id']?>">
						<td>
							<div class="pe-5">
								<div class="d-flex align-items-center flex-wrap gap-1">
									<a href="#" class="fw-bolder text-dark text-hover-primary" id="meja<?=$v['id']?>"><?=$v['name']?></a>
									<span class="svg-icon svg-icon-7 svg-icon-success mx-3">
										<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<circle fill="#000000" cx="12" cy="12" r="8" />
										</svg>
									</span> 
								</div>
							</div>
						</td>
						<td style="text-align: center;">
							<span class="fw-bold text-muted text-end me-3" id="store<?=$v['id']?>"><?=$v['namatoko']?></span>
						</td>
						<td style="text-align: center;">
							<span class="fw-bold text-muted text-end me-3"><?=$v['description']?></span>
						</td>
						<td style="text-align: center;">
							<span class="fw-bold text-muted text-end me-3"><a href="javascript:;" onclick="codemeja(<?=$v['id']?>)" data-code="<?=$v['code']??''?>" id="code<?=$v['id']?>"><?=$v['code']??'{Generate Kode}'?></a></span>
						</td>
						<td style="text-align: center;"> 
							<a href="<?=site_url('pengaturan/meja/'.$v['id'])?>" class="btn btn-icon btn-active-light-primary w-30px h-30px me-3" ><?=$edit?></a>
							<a href="javascript:;" class="btn btn-icon btn-active-light-danger w-30px h-30px" onclick="removing('account_type',<?=$v['id']?>)"><?=$trash?></a>
						</td>
					</tr> 
			<?php } ?>
		</table>
	</div> 
</div> 
<script>
	function codemeja(id){
		var cd = $("#code"+id).attr('data-code');
		if (cd!='') { viewqr(id); } else {
			$.post('<?=site_url('pengaturan/meja/code/')?>'+id).done(function(data){
				var y = JSON.parse(data);
				if (y.status==true) {
					$("#code"+id).html(y.code).attr('data-code',y.code);
					viewqr(id);
				}
			});
		}
	}

	function viewqr(id){
		var meja = $("#meja"+id).html();
		var store = $("#store"+id).html();
		var code = $("#code"+id).attr('data-code');
		$("#qr-store").html(store);
		$("#qr-meja").html(meja);
		$("#qr-code").html('<?=site_url('')?>'+code);
		$("#qr-qr").attr('src','<?=site_url('qr/')?>'+code);
		$("#qrmod").modal('show');
		$("#namafile").val(store + ' - ' + meja);
	}
</script> 
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #qr-area, #qr-area * {
        visibility: visible;
    }
    #qr-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>

<div class="modal fade" tabindex="-1" id="qrmod">
    <div class="modal-dialog">
        <div class="modal-content"> 

            <div id="qr-area" class="modal-body" style="text-align: center;">
                <h1 id="qr-store" style="text-align: center;"></h1>
                <h1 id="qr-meja" style="text-align: center;"></h1>
                <img id="qr-qr" style="width:100%;" crossorigin="anonymous" />
                <h5 id="qr-code" style="text-align: center;"></h5>
                <input type="hidden" id="namafile" name="">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="simpangambar()">Simpan Gambar</button>
                <button type="button" class="btn btn-primary" onclick="printQR()">Print</button>
            </div>
            <script>
            	function simpangambar(){
            		const element = document.getElementById('qr-area'); 
				    html2canvas(element, {
				        scale: 2,          // biar tajam
				        useCORS: true      // WAJIB kalau gambar QR dari URL
				    }).then(canvas => {

				        // convert ke image
				        const imgData = canvas.toDataURL('image/png');

				        // auto download
				        const link = document.createElement('a');
				        link.href = imgData;
				        link.download = document.getElementById('namafile').value+'.png';
				        link.click();

				    });
					$("#qrmod").modal('hide');
            	}
            	function printQR() {
				    window.print();
					$("#qrmod").modal('hide');
				}
            </script>
        </div>
    </div>
</div>

