<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	$companyid 	= companyid($userid);
    if (!$companyid) {
        $priv = $db->table('account_store_privilage')->select('company')->where('account', $userid)->get()->getRowArray();
        $companyid = isset($priv['company']) ? $priv['company'] : mycompanyid();
    }
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    //session()->set('redirect_store', current_url());
    session()->set('redirect_worker', current_url());
    $status = \Config\Services::request()->getGet('status');
    $search = \Config\Services::request()->getGet('q');
?>

<div class="card mb-5 mb-xxl-10" id="thecontent">
	<!--begin::Card header-->
	<div class="card-header">
		<!--begin::Heading-->
		<div class="card-title">
			<h3>Daftar Petugas</h3>
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
                <input type="text" id="searchInput" class="form-control form-control-solid w-250px ps-14" placeholder="Cari Petugas..." value="<?= htmlspecialchars($search ?? '') ?>" />
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
			<a href="<?=site_url('petugas/tambah')?>" class="btn btn-sm btn-primary my-1">Tambah Petugas</a>
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
						<th class="min-w-250px text-start">Nama Petugas</th>
						<th class="min-w-150px text-start">Toko</th>
						<th class="min-w-150px text-start">Jabatan</th>
						<th class="min-w-150px text-start">Telepon</th>
						<th class="min-w-150px text-start">Email</th>
						<th class="min-w-150px text-start">Alamat</th>
						<th class="min-w-100px text-start">Status</th>
						<th class="min-w-150px text-start">Aksi</th>
					</tr>
				</thead>
				<!--end::Thead-->
				<!--begin::Tbody-->
				<tbody class="fw-6 fw-bold text-gray-600">
					<?php 
					$pager = \Config\Services::pager();
					$page = (int) (\Config\Services::request()->getGet('page') ?? 1);
					$perPage = 10;

					$builder = $db->table('account a')
                        ->join('account_store_privilage b','b.account=a.id','left')
                        ->join('account_store c', 'c.id = b.store', 'left') // Join store table
                        ->where(['b.company'=>$companyid]);
                        
                    if ($status && $status != 'all') {
                        $builder->where('a.status', $status);
                    }
                    
                    if ($search) {
                        $builder->groupStart()
                            ->like('a.name', $search)
                            ->orLike('c.name', $search)
                            ->orLike('b.privilage', $search)
                            ->orLike('a.phone', $search)
                            ->orLike('a.email', $search)
                            ->orLike('a.address', $search)
                        ->groupEnd();
                    }

                    $builder->orderBy('a.created','DESC');
					
					$total = $builder->countAllResults(false);
                    $data = $builder->select('a.*,b.privilage,b.store, c.name as store_name')->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
					
					foreach($data as $k=>$v){ ?>
						<tr>
							<td>
								<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
									<a href="<?=site_url('petugas/'.$v['id'])?>">
										<div class="symbol-label">
											<img src="<?=$v['foto']!=''?base_url('f/'.str_replace('.', '_thumb.', $v['foto'])):base_url('f/'.sys('nofoto'))?>" alt="Emma Smith" class="w-100" />
										</div>
									</a>
								</div>
								<div class="d-flex flex-column">
									<a href="<?=site_url('petugas/'.$v['id'])?>" class="text-gray-800 text-hover-primary mb-1"><?=$v['name']?></a>
								</div>
							</td>
							<td class="text-start"><a href="<?=site_url('toko/'.$v['store'])?>" class="text text-<?=isset($v['store'])?'primary':'danger'?>"><?=isset($v['store_name'])?$v['store_name']:'Belum ada'?></a></td>
							<td><?=privilage($v['privilage'])?></td>
							<td><?=$v['phone']?></td>
							<td><?=$v['email']?></td>
							<td><?=$v['address']?></td>
							<td class="text-<?=$v['status']=='active'?'success':'danger'?>"><?=$v['status']?></td>
							<td class="pe-9 text-start">
								<a href="<?=site_url('petugas/ubah/'.$v['id'])?>" class="badge badge-primary"><i class="fa fa-edit text-white"> </i></a>
								<a href="javascript:;" onclick="hapus('<?=$v['id']?>', '<?=htmlspecialchars($v['name'], ENT_QUOTES)?>')" class="badge badge-danger"><i class="fa fa-trash text-white"> </i></a>
							</td>
						</tr>
					<?php } ?> 
				</tbody>
				<!--end::Tbody-->
			</table>
			<!--end::Table-->
		</div>
		<!--end::Table wrapper-->
		<div class="d-flex justify-content-end py-4 px-9">
			<?= $pager->makeLinks($page, $perPage, $total, 'metronic_full') ?>
		</div>
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
        var url = "<?=site_url('petugas/list')?>?status=" + status;
        if (search) {
            url += "&q=" + encodeURIComponent(search);
        }
        window.location.href = url;
    }

    function hapus(id, name) {
        Swal.fire({
            title: 'Hapus Petugas?',
            text: "Apakah anda yakin ingin menghapus petugas " + name + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("<?=site_url('petugas/hapus')?>", {id: id}, function(res) {
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
