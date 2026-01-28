<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
	if ($_SESSION['userty']=='owner') {
		$companyid 	= companyid($userid);
	} else {
		$storep = $db->table('account_store_privilage')->where(['account'=>$userid])->select('company')->get()->getRowArray();
		$companyid 	= $storep['company'];
	}
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_pengaturan.php'); 
    //session()->set('redirect_store', current_url());
    session()->set('redirect_kategori', current_url());
?>


<div class="card pt-4 mb-6 mb-xl-9"> 
	<div class="card-header border-0">
		
		<div class="card-title">
			<h2 class="fw-bolder mb-0">Daftar Kategori</h2>
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

			<a href="<?=site_url('pengaturan/kategori/tambah')?>" class="btn btn-sm btn-flex btn-light-primary ">
				<span class="svg-icon svg-icon-3">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black" />
						<rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black" />
						<rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black" />
					</svg>
				</span>Kategori Baru
			</a>
		</div>
	</div> 
	<div id="kt_customer_view_payment_method" class="card-body pt-0">
		<div class="d-flex flex-column">
			<style>
				th {
					text-align: left;
					font-size: 16px;
					font-weight: bold !important;
					background: rgba(255, 255, 255, 0.3) !important;
					padding-left: 20px !important;
				}
				.trodd {
					background: #fafafa !important;
				}
				.treven {
					background: #f2f4f7 !important;
				}
				.trodd:hover {
				    background-color: #e9ecef !important;
				}
				.treven:hover {
				    background-color: #e9ecef !important;
				}

				li {
					margin-left: 50px;
				}
				.form-check-input { cursor:pointer; }
			</style>
			<table class="table">
				<?php 
				$star = icon('star');
				$edit = icon('pencil2');
				$more = icon('more');
				if ($_SESSION['userty']=='owner') {
					$stores = $db->table('account_store a')->where(['a.company'=>$companyid])->select('a.id,a.name namatoko')->get()->getResultArray();
				} else {
					$stores = $db->table('account_store a')->where(['a.account'=>$userid])->select('a.id,a.name namatoko')->get()->getResultArray();
				}
				echo '<tr><th>Kategori</th>';
				$jmltoko = 0;
				foreach($stores as $Kk=>$vv){
					echo '<th>'.$vv['namatoko'].'</th>';
					$jmltoko++;
				}
				echo '</tr>';
				$atype = $db->table('account_type')->where(['sales'=>1])->get()->getResultArray();
				$varr = array(1,2);
				$category = $db->table('product_category pc')
							   ->select("
							        pc.id,
							        pc.name,
							        pc.status,
							        pc.category,
							        GROUP_CONCAT(pcs.store ORDER BY pcs.store) as stores
							   ")
							   ->join(
							        'product_category_store pcs',
							        'pcs.category = pc.id AND pcs.company = '.$db->escape($companyid).' AND pcs.status = "ok"',
							        'left'
							   )
							   ->groupBy('pc.id')
							   ->get()
							   ->getResultArray();


				$odd = true;
				$parent = '';
				$parentid = '';
				foreach($category as $K=>$v){ $rand = rand(1,4); switch ($rand) { case 1: $bg = 'success'; break; case 2: $bg = 'info'; break; case 3: $bg = 'primary'; break; case 4: $bg = 'danger'; break; default:$bg="secondary"; break;}
					if (!empty($v['stores'])) {
				        $storex = '[' . str_replace(',', '][', $v['stores']) . ']';
				    } else {
				        $storex = '';
				    }
					if ($parent!=$v['category']) {
						$parentid = $v['id'];
						echo '<tr><th>'.$v['category'].'</th>';
						foreach($stores as $Kk=>$vv){
							echo '<th><input class="form-check-input" type="checkbox" value="" id="groupcheck_'.$parentid.'_'.$vv['id'].'" onclick="groupcheck('.$parentid.','.$vv['id'].')" /></th>';
						}
						echo '</tr>';
					}
					$parent = $v['category'];
					if ($odd==true) {
						$cl 	= 'trodd';
						$odd 	= false;
					} else {
						$cl 	= 'treven';
						$odd 	= true;
					}
					echo '<tr class="'.$cl.'" id="row'.$v['id'].'"><td>
					<li class="d-flex align-items-center py-2">
						<span class="bullet bullet-vertical  bg-'.$bg.' me-5"></span> '.$v['name'].'
						<div class="badge badge-light-'.($v['status']=='active'?'success':'danger').' ms-5">'.($v['status']=='active'?"Aktif":"Tidak Aktif").'</div>
					</li></td>';
					foreach($stores as $Kk=>$vv){ $ceked = '';
						if (str_contains($storex, '['.$vv['id'].']')) { $ceked = 'checked="checked"'; }

						echo '<td style="padding-left:20px !important;"><input class="form-check-input groupcheck_'.$parentid.'_'.$vv['id'].'"  id="onecheck_'.$v['id'].'_'.$vv['id'].'" type="checkbox" value="" onchange="does('.$v['id'].','.$vv['id'].')" data-cat="'.$v['id'].'" data-store="'.$vv['id'].'" '.$ceked.' /></td>';
					}
					echo '<td style="text-align: center;"> 
								<a href="'.site_url('pengaturan/kategori/'.$v['id']).'" class="btn btn-icon btn-primary w-30px h-30px me-3" ><i class="fa fa-edit" style="color:white;"></i></a>
								<a href="javascript:;" class="btn btn-icon btn-danger w-30px h-30px" onclick="removing('."'product_category'".','.$v['id'].')"><i class="fa fa-trash" style="color:white;"></i></a>
							</td>';
					echo '</tr>';

				}
				?> 
			</table>
			<script>
				function groupcheck(catid,storeid){
					const checkbox = document.getElementById('groupcheck_' + catid + '_' + storeid);
				    if (checkbox.checked) {
				        $('.groupcheck_' + catid + '_' + storeid).prop('checked', true);
				        $('.groupcheck_' + catid + '_' + storeid).each(function(){
				        	var cat = $(this).attr('data-cat');
				        	var str = $(this).attr('data-store');
				        	does(cat,str);
				        })
				    } else {
				        $('.groupcheck_' + catid + '_' + storeid).prop('checked', false);
				        $('.groupcheck_' + catid + '_' + storeid).each(function(){
				        	var cat = $(this).attr('data-cat');
				        	var str = $(this).attr('data-store');
				        	does(cat,str);
				        })
				    }
				}

				function does(catid,storeid){ 
					const checkbox = document.getElementById('onecheck_' + catid + '_' + storeid);
				    if (checkbox.checked) {
				        var ok = "ok";
				    } else {
				        var ok = "not";
				    }
				    setTimeout(function(){
				    	$.post('cat/'+catid+'/'+storeid+'/'+ok).done(function(data){
				    		var y = JSON.parse(data);
				    		if (y.status==true) {

				    		}
				    	})
				    },500);
				}
			</script>
		</div>
	</div> 
</div> 