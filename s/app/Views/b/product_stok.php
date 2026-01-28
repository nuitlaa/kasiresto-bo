<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$companyid 	= companyid($userid);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    //session()->set('redirect_store', current_url());
    session()->set('redirect_worker', current_url());
?>
<style>
	.borderin {
		border-bottom: 1px dashed silver !important;
	}

	.inputan {
		padding: 5px;
		max-width: 120px;
		text-align: right;
	    border: 1px solid silver;
	    color: grey;
	}
</style>

<link href="<?= base_url('t/') ?>/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?= base_url('t/') ?>/plugins/custom/datatables/datatables.bundle.js"></script>
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


			<span class="fw-bold text-muted me-2" id="pagination-info">1 - 0 dari 0</span>
			<a href="#" id="btn-prev" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Previous message">
				<span class="svg-icon svg-icon-2 m-0">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M11.2657 11.4343L15.45 7.25C15.8642 6.83579 15.8642 6.16421 15.45 5.75C15.0358 5.33579 14.3642 5.33579 13.95 5.75L8.40712 11.2929C8.01659 11.6834 8.01659 12.3166 8.40712 12.7071L13.95 18.25C14.3642 18.6642 15.0358 18.6642 15.45 18.25C15.8642 17.8358 15.8642 17.1642 15.45 16.75L11.2657 12.5657C10.9533 12.2533 10.9533 11.7467 11.2657 11.4343Z" fill="black" />
					</svg>
				</span>
			</a>
			<a href="#" id="btn-next" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Next message">
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

        <div class="container-fluid">

            <h3 class="mb-5">📦 Stok Produk</h3>

            <!-- ALERT -->
            <?php if (count(array_filter($data, fn($r)=>$r['stock'] <= $r['minstock'])) > 0): ?>
                <div class="alert alert-danger">
                    ⚠️ Terdapat produk dengan stok di bawah minimum
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">

                    <table id="table-stock" class="table table-row-dashed">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Varian</th>
                                <th>SKU</th>
                                <th>Stok</th>
                                <th>Min</th>
                            </tr>
                        </thead>
                    </table>

                    <script>
                    $('#table-stock').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: '<?= base_url("produk/produkstok") ?>',
                        order: [[3, 'asc']],
                        columns: [
                            { data: 'product_name' },
                            { data: 'variant_name' },
                            { data: 'sku' },
                            { data: 'stock' },
                            { data: 'minstock' }
                        ]
                    });
                    </script>


                </div>
            </div>

        </div>



	</div> 
</div> 
<style>
	.erroring {
		background-color: rgba(176, 42, 55, 0.10);
		border-color: rgba(176, 42, 55, 0.35);
		color: #58151c;
	}
	.waiting {
		background-color: rgba(59, 130, 246, 0.10);
		border-color: rgba(59, 130, 246, 0.35);
		color: #1e3a8a;
	}
	.done {
		background-color: rgba(40, 167, 69, 0.12);
		border-color: rgba(40, 167, 69, 0.4);
		color: #155724;
	}
</style>
<script>
	document.addEventListener("DOMContentLoaded", function () {
	    const input = document.querySelector('[data-kt-customer-table-filter="search"]');
	    const rows  = document.querySelectorAll('[data-kt-customer-payment-method="row"]');

	    input.addEventListener("keyup", function () {
	        const keyword = this.value.toLowerCase().trim();

	        rows.forEach(row => {
	            const text = row.dataset.search || row.innerText.toLowerCase();
	            row.style.display = text.includes(keyword) ? "" : "none";
	        });
	    });
});

	function harganya(product,variant,unit,type){
		var harga = $("#harganya_"+product+"_"+variant+"_"+unit+"_"+type).val();
		$("#harganya_"+product+"_"+variant+"_"+unit+"_"+type).addClass('waiting');
		$.post('<?=site_url('produk/setharga')?>',{
			product : product,variant:variant,unit:unit,type:type,price:deuang(harga)
		}).done(function(data){
			$("#harganya_"+product+"_"+variant+"_"+unit+"_"+type).removeClass('waiting').addClass('done');
			setTimeout(function(){
				$("#harganya_"+product+"_"+variant+"_"+unit+"_"+type).removeClass('done');
			},3000)
		})
	}
</script>