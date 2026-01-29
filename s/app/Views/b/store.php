<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    session()->set('redirect_store', current_url());
    $status = \Config\Services::request()->getGet('status');
    $search = \Config\Services::request()->getGet('q');
?>

<div class="card mb-5 mb-xxl-10" id="thecontent">
	<!--begin::Card header-->
	<div class="card-header">
		<!--begin::Heading-->
		<div class="card-title">
			<h3>Daftar Toko</h3>
		</div>
		<!--end::Heading-->
		<!--begin::Toolbar-->
		<div class="card-toolbar">
            <div class="d-flex align-items-center position-relative my-1 me-4">
                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                    </svg>
                </span>
                <input type="text" id="searchInput" class="form-control form-control-solid w-250px ps-14" placeholder="Cari Toko..." value="<?= htmlspecialchars($search ?? '') ?>" />
            </div>

			<div class="my-1 me-4">
				<!--begin::Select-->
				<select id="statusFilter" class="form-select form-select-sm form-select-solid w-125px" data-control="select2" data-placeholder="Select Hours" data-hide-search="true">
					<option value="all" <?=($status=='all')?'selected':''?>>Semua</option>
					<option value="active" <?=($status=='active')?'selected':''?>>Aktif</option>
					<option value="passive" <?=($status=='passive')?'selected':''?>>Tidak Aktif</option>
				</select>
				<!--end::Select-->
			</div>
			<a href="<?=site_url('toko/tambah')?>" class="btn btn-sm btn-primary my-1">Tambah Toko</a>
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
						<th class="min-w-250px">Toko</th>
						<th class="min-w-150px">Kepala Toko</th>
						<th class="min-w-150px">Jumlah karyawan</th>
						<th class="min-w-150px">Jenis Layanan</th>
						<th class="min-w-150px">Telepon</th>
						<th class="min-w-150px">Alamat</th>
						<th class="min-w-100px">Status</th>
						<th class="min-w-150px">Aksi</th>
					</tr>
				</thead>
				<!--end::Thead-->
				<!--begin::Tbody-->
				<tbody class="fw-6 fw-bold text-gray-600">
					<?php 
					$builder = $db->table('account_store')->where(['owner'=>$userid]);

                    if ($status && $status != 'all') {
                        $builder->where('status', $status);
                    }

                    if ($search) {
                        $builder->groupStart()
                            ->like('name', $search)
                            ->orLike('address', $search)
                        ->groupEnd();
                    }

                    $store = $builder->get()->getResultArray();
					
					foreach($store as $k=>$v){ $chief = $db->table('account_store_privilage a')->join('account b','b.id=a.account','left')->where(['a.store'=>$v['id'],'a.privilage'=>'chief'])->select('b.id,b.name')->get()->getRowArray(); ?>
						<tr>
							<td class="d-flex align-items-center">
								<!--begin:: Avatar -->
								<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
									<a href="<?=site_url('toko/'.$v['id'])?>">
										<div class="symbol-label">
											<img src="<?=$v['foto']!=''?base_url('f/'.$v['foto']):base_url('f/'.sys('nofoto'))?>" alt="Emma Smith" class="w-100" />
										</div>
									</a>
								</div>
								<!--end::Avatar-->
								<!--begin::User details-->
								<div class="d-flex flex-column">
									<a href="<?=site_url('toko/'.$v['id'])?>" class="text-gray-800 text-hover-primary mb-1"><?=$v['name']?></a>
									<span><?=$v['description']?></span>
								</div>
								<!--begin::User details-->
							</td>
							<td><a href="#" class="badge badge-<?=isset($chief['id'])?'primary':'danger'?>"><?=isset($chief['name'])?$chief['name']:'Belum ada'?></a></td>
							<td><?=$db->table('account_store_privilage')->where(['store'=>$v['id'],'privilage'=>'employee'])->countAllResults()?></td>
							<td><?=$v['layanan']=='ambil'?'Pembeli Mengambil Sendiri':'Pesanan diantar Pelayan'?></td>
							<td><?=$v['phone']?></td>
							<td><?=$v['address']?></td>
							<td class="text-<?=$v['status']=='active'?'success':'danger'?>"><?=$v['status']?></td>
							<td class="pe-9">
								<a href="<?=site_url('toko/ubah/'.$v['id'])?>" class="badge badge-primary text-white"><i class="fa fa-edit text-white"></i></a>
								<a href="javascript:;" onclick="hapus('<?=$v['id']?>', '<?=htmlspecialchars($v['name'], ENT_QUOTES)?>')" class="badge badge-danger"><i class="fa fa-trash text-white"></i></a>
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

<script>
    var searchTimeout;

    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        var search = $('#searchInput').val();
        reloadPage(status, search);
    });

    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        var search = $(this).val();
        var status = $('#statusFilter').val();
        
        searchTimeout = setTimeout(function() {
            reloadPage(status, search);
        }, 800); // Debounce 800ms
    });

    function reloadPage(status, search) {
        var url = "<?=site_url('toko/list')?>?status=" + status;
        if (search) {
            url += "&q=" + encodeURIComponent(search);
        }
        window.location.href = url;
    }

    function hapus(id, name) {
        Swal.fire({
            title: 'Hapus Toko?',
            text: "Apakah anda yakin ingin menghapus toko " + name + "? Semua data karyawan terkait toko ini akan kehilangan akses toko.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("<?=site_url('toko/hapus')?>", {id: id}, function(res) {
                    var obj = JSON.parse(res);
                    if (obj.status) {
                        Swal.fire(
                            'Terhapus!',
                            obj.message,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            obj.message,
                            'error'
                        );
                    }
                });
            }
        })
    }
</script>