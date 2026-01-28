<?php $db = db_connect();
	$userid 	= usertoken($_SESSION['usertoken']);
    $user       = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email')->where('a.id', $userid)->get()->getRowArray();
    include('mod/hmenu_owner.php'); 
    
?> 
<div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
	<div class="card-header cursor-pointer">
		<div class="card-title m-0">
			<h3 class="fw-bolder m-0">Data Perusahaan</h3>
		</div>
		<a href="<?=site_url('user/perusahaan')?>" class="btn btn-primary align-self-center">Ubah Data</a>
	</div>
	<div class="card-body p-9">
		<div class="row mb-7">
			<label class="col-lg-4 fw-bold text-muted">Nama Owner</label>
			<div class="col-lg-8">
				<span class="fw-bolder fs-6 text-gray-800"><?=$user['company_owner']?></span>
			</div>
		</div>
		<div class="row mb-7">
			<label class="col-lg-4 fw-bold text-muted">Perusahaan</label>
			<div class="col-lg-8 fv-row">
				<span class="fw-bold text-gray-800 fs-6"><?=$user['company_name']?></span>
			</div>
		</div>
		<div class="row mb-7">
			<label class="col-lg-4 fw-bold text-muted">Telepon
			<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Telepon Harus Aktif"></i></label>
			<div class="col-lg-8 d-flex align-items-center">
				<span class="fw-bolder fs-6 text-gray-800 me-2"><?=$user['company_phone']?></span>
			</div>
		</div>
		<div class="row mb-7">
			<label class="col-lg-4 fw-bold text-muted">Alamat</label>
			<div class="col-lg-8">
				<a href="#" class="fw-bold fs-6 text-gray-800 text-hover-primary"><?=$user['company_address']?></a>
			</div>
		</div>
	</div>
</div>
<div class="row gy-5 g-xl-10">
	<div class="col-xl-6">
		<div class="card card-xl-stretch mb-xl-10">
			<div class="card-header border-0 pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bolder fs-3 mb-1">Kegiatan Terbaru</span>
					<span class="text-muted fw-bold fs-7">Lebih dari 0 Transaksi</span>
				</h3>
				<div class="card-toolbar">
					<button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
						<span class="svg-icon svg-icon-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="5" y="5" width="5" height="5" rx="1" fill="#000000" />
									<rect x="14" y="5" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
									<rect x="5" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
									<rect x="14" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
								</g>
							</svg>
						</span>
					</button>
					<div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_61a085c7edf6c">
						<div class="px-7 py-5">
							<div class="fs-5 text-dark fw-bolder">Filter</div>
						</div>
						<div class="separator border-gray-200"></div>
						<div class="px-7 py-5">
							<div class="mb-10">
								<label class="form-label fw-bold">Tahun :</label>
								<div>
									<select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Pilih Tahun" data-dropdown-parent="#kt_menu_61a085c7edf6c" data-allow-clear="true">
										<option></option>
										<option value="2024">2024</option>
										<option value="2025">2025</option>
									</select>
								</div>
							</div> 
							<div class="mb-10">
								<label class="form-label fw-bold">Bulan :</label>
								<div>
									<select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Pilih Bulan" data-dropdown-parent="#kt_menu_61a085c7edf6c" data-allow-clear="true">
										<option value="semua">Semua</option>
										<?php for ($i=1; $i <= 12 ; $i++) { 
											switch ($i) { 
												case 1: $bulan = "Januari"; break; 
												case 2: $bulan = "Februari"; break; 
												case 3: $bulan = "Maret"; break; 
												case 4: $bulan = "April"; break; 
												case 5: $bulan = "Mei"; break; 
												case 6: $bulan = "Juni"; break; 
												case 7: $bulan = "Juli"; break; 
												case 8: $bulan = "Agustus"; break; 
												case 9: $bulan = "September"; break; 
												case 10: $bulan = "Oktober"; break; 
												case 11: $bulan = "November"; break; 
												case 12: $bulan = "Desember"; break; 
											}
											$bln = $i<10?'0'.$i:$i;
											$sel = isset($_GET['bulan'])&&$_GET['bulan']==$bln?'selected':'';
											echo '<option value="'.$bln.'" '.$sel.'>'.$bulan.'</option>';
										} ?>
									</select>
								</div>
							</div> 
							<div class="d-flex justify-content-end">
								<button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
								<button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Terapkan</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div id="kt_charts_widget_1_chart" style="height: 350px"></div>
				<script>
            		a = document.getElementById("kt_charts_widget_1_chart");
            		o = parseInt(KTUtil.css(a, "height"));
            		s = KTUtil.getCssVariableValue("--bs-gray-500");
            		r = KTUtil.getCssVariableValue("--bs-gray-200");
            		i = KTUtil.getCssVariableValue("--bs-primary");
            		l = KTUtil.getCssVariableValue("--bs-gray-300");
					new ApexCharts(a, {
                    series: [
                        { name: "Net Profit", data: [44, 55, 57, 56, 61, 58] },
                        { name: "Revenue", data: [76, 85, 101, 98, 87, 105] },
                    ],
                    chart: { fontFamily: "inherit", type: "bar", height: o, toolbar: { show: !1 } },
                    plotOptions: { bar: { horizontal: !1, columnWidth: ["30%"], borderRadius: 4 } },
                    legend: { show: !1 },
                    dataLabels: { enabled: !1 },
                    stroke: { show: !0, width: 2, colors: ["transparent"] },
                    xaxis: {
                        categories: ["Feb", "Mar", "Apr", "May", "Jun", "Jul"],
                        axisBorder: { show: !1 },
                        axisTicks: { show: !1 },
                        labels: { style: { colors: s, fontSize: "12px" } },
                    },
                    yaxis: { labels: { style: { colors: s, fontSize: "12px" } } },
                    fill: { opacity: 1 },
                    states: {
                        normal: { filter: { type: "none", value: 0 } },
                        hover: { filter: { type: "none", value: 0 } },
                        active: { allowMultipleDataPointsSelection: !1, filter: { type: "none", value: 0 } },
                    },
                    tooltip: {
                        style: { fontSize: "12px" },
                        y: {
                            formatter: function (e) {
                                return "$" + e + " thousands";
                            },
                        },
                    },
                    colors: [i, l],
                    grid: { borderColor: r, strokeDashArray: 4, yaxis: { lines: { show: !0 } } },
                }).render()
				</script>
			</div>
		</div>
	</div>
	<div class="col-xl-6">
		<div class="card card-xl-stretch mb-5 mb-xl-10">
			<div class="card-header border-0 pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bolder fs-3 mb-1">Produk yang hampir habis</span>
					<span class="text-muted fw-bold fs-7">Terdapat 10 produk</span>
				</h3>
				<div class="card-toolbar">
					<button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
						<span class="svg-icon svg-icon-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="5" y="5" width="5" height="5" rx="1" fill="#000000" />
									<rect x="14" y="5" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
									<rect x="5" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
									<rect x="14" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
								</g>
							</svg>
						</span>
					</button>
					<div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_61a085c7ee5b6">
						<div class="px-7 py-5">
							<div class="fs-5 text-dark fw-bolder">Filter Options</div>
						</div>
						<div class="separator border-gray-200"></div>
						<div class="px-7 py-5">
							<div class="mb-10">
								<label class="form-label fw-bold">Status:</label>
								<div>
									<select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-dropdown-parent="#kt_menu_61a085c7ee5b6" data-allow-clear="true">
										<option></option>
										<option value="1">Approved</option>
										<option value="2">Pending</option>
										<option value="2">In Process</option>
										<option value="2">Rejected</option>
									</select>
								</div>
							</div>
							<div class="mb-10">
								<label class="form-label fw-bold">Member Type:</label>
								<div class="d-flex">
									<label class="form-check form-check-sm form-check-custom form-check-solid me-5">
										<input class="form-check-input" type="checkbox" value="1" />
										<span class="form-check-label">Author</span>
									</label>
									<label class="form-check form-check-sm form-check-custom form-check-solid">
										<input class="form-check-input" type="checkbox" value="2" checked="checked" />
										<span class="form-check-label">Customer</span>
									</label>
								</div>
							</div>
							<div class="mb-10">
								<label class="form-label fw-bold">Notifications:</label>
								<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
									<input class="form-check-input" type="checkbox" value="" name="notifications" checked="checked" />
									<label class="form-check-label">Enabled</label>
								</div>
							</div>
							<div class="d-flex justify-content-end">
								<button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
								<button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Apply</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body py-3">
				<div class="table-responsive">
					<table class="table align-middle gs-0 gy-5">
						<thead>
							<tr>
								<th class="p-0 w-50px"></th>
								<th class="p-0 min-w-200px"></th>
								<th class="p-0 min-w-100px"></th>
								<th class="p-0 min-w-40px"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>
									<div class="symbol symbol-50px me-2">
										<span class="symbol-label">
											<img src="<?=base_url('t/')?>/media/svg/brand-logos/plurk.svg" class="h-50 align-self-center" alt="" />
										</span>
									</div>
								</th>
								<td>
									<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Top Authors</a>
									<span class="text-muted fw-bold d-block fs-7">Successful Fellas</span>
								</td>
								<td>
									<div class="d-flex flex-column w-100 me-2">
										<div class="d-flex flex-stack mb-2">
											<span class="text-muted me-2 fs-7 fw-bold">70%</span>
										</div>
										<div class="progress h-6px w-100">
											<div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
								</td>
								<td class="text-end">
									<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
										<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
										<span class="svg-icon svg-icon-2">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
												<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</a>
								</td>
							</tr>
							<tr>
								<th>
									<div class="symbol symbol-50px me-2">
										<span class="symbol-label">
											<img src="<?=base_url('t/')?>/media/svg/brand-logos/telegram.svg" class="h-50 align-self-center" alt="" />
										</span>
									</div>
								</th>
								<td>
									<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Popular Authors</a>
									<span class="text-muted fw-bold d-block fs-7">Most Successful</span>
								</td>
								<td>
									<div class="d-flex flex-column w-100 me-2">
										<div class="d-flex flex-stack mb-2">
											<span class="text-muted me-2 fs-7 fw-bold">50%</span>
										</div>
										<div class="progress h-6px w-100">
											<div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
								</td>
								<td class="text-end">
									<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
										<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
										<span class="svg-icon svg-icon-2">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
												<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</a>
								</td>
							</tr>
							<tr>
								<th>
									<div class="symbol symbol-50px me-2">
										<span class="symbol-label">
											<img src="<?=base_url('t/')?>/media/svg/brand-logos/vimeo.svg" class="h-50 align-self-center" alt="" />
										</span>
									</div>
								</th>
								<td>
									<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">New Users</a>
									<span class="text-muted fw-bold d-block fs-7">Awesome Users</span>
								</td>
								<td>
									<div class="d-flex flex-column w-100 me-2">
										<div class="d-flex flex-stack mb-2">
											<span class="text-muted me-2 fs-7 fw-bold">80%</span>
										</div>
										<div class="progress h-6px w-100">
											<div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
								</td>
								<td class="text-end">
									<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
										<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
										<span class="svg-icon svg-icon-2">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
												<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</a>
								</td>
							</tr>
							<tr>
								<th>
									<div class="symbol symbol-50px me-2">
										<span class="symbol-label">
											<img src="<?=base_url('t/')?>/media/svg/brand-logos/bebo.svg" class="h-50 align-self-center" alt="" />
										</span>
									</div>
								</th>
								<td>
									<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Active Customers</a>
									<span class="text-muted fw-bold d-block fs-7">Best Customers</span>
								</td>
								<td>
									<div class="d-flex flex-column w-100 me-2">
										<div class="d-flex flex-stack mb-2">
											<span class="text-muted me-2 fs-7 fw-bold">90%</span>
										</div>
										<div class="progress h-6px w-100">
											<div class="progress-bar bg-primary" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
								</td>
								<td class="text-end">
									<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
										<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
										<span class="svg-icon svg-icon-2">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
												<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</a>
								</td>
							</tr>
							<tr>
								<th>
									<div class="symbol symbol-50px me-2">
										<span class="symbol-label">
											<img src="<?=base_url('t/')?>/media/svg/brand-logos/kickstarter.svg" class="h-50 align-self-center" alt="" />
										</span>
									</div>
								</th>
								<td>
									<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Bestseller Theme</a>
									<span class="text-muted fw-bold d-block fs-7">Amazing Templates</span>
								</td>
								<td>
									<div class="d-flex flex-column w-100 me-2">
										<div class="d-flex flex-stack mb-2">
											<span class="text-muted me-2 fs-7 fw-bold">70%</span>
										</div>
										<div class="progress h-6px w-100">
											<div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
									</div>
								</td>
								<td class="text-end">
									<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
										<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
										<span class="svg-icon svg-icon-2">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
												<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</a>
								</td>
							</tr>
						</tbody>
						<!--end::Table body-->
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row gy-5 gx-xl-10">
	<div class="col-xl-6">
		<div class="card card-xl-stretch mb-xl-10">
			<div class="card-header align-items-center border-0 mt-4">
				<h3 class="card-title align-items-start flex-column">
					<span class="fw-bolder mb-2 text-dark">Penjualan hari ini</span>
					<span class="text-muted fw-bold fs-7">890,344 Transaksi</span>
				</h3>
				<div class="card-toolbar">
					<button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
						<span class="svg-icon svg-icon-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="5" y="5" width="5" height="5" rx="1" fill="#000000" />
									<rect x="14" y="5" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
									<rect x="5" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
									<rect x="14" y="14" width="5" height="5" rx="1" fill="#000000" opacity="0.3" />
								</g>
							</svg>
						</span>
					</button>
					<!--begin::Menu 1-->
					<div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_61a085c7ef1b3">
						<!--begin::Header-->
						<div class="px-7 py-5">
							<div class="fs-5 text-dark fw-bolder">Filter Options</div>
						</div>
						<!--end::Header-->
						<!--begin::Menu separator-->
						<div class="separator border-gray-200"></div>
						<!--end::Menu separator-->
						<!--begin::Form-->
						<div class="px-7 py-5">
							<!--begin::Input group-->
							<div class="mb-10">
								<!--begin::Label-->
								<label class="form-label fw-bold">Status:</label>
								<!--end::Label-->
								<!--begin::Input-->
								<div>
									<select class="form-select form-select-solid" data-kt-select2="true" data-placeholder="Select option" data-dropdown-parent="#kt_menu_61a085c7ef1b3" data-allow-clear="true">
										<option></option>
										<option value="1">Approved</option>
										<option value="2">Pending</option>
										<option value="2">In Process</option>
										<option value="2">Rejected</option>
									</select>
								</div>
								<!--end::Input-->
							</div>
							<!--end::Input group-->
							<!--begin::Input group-->
							<div class="mb-10">
								<!--begin::Label-->
								<label class="form-label fw-bold">Member Type:</label>
								<!--end::Label-->
								<!--begin::Options-->
								<div class="d-flex">
									<!--begin::Options-->
									<label class="form-check form-check-sm form-check-custom form-check-solid me-5">
										<input class="form-check-input" type="checkbox" value="1" />
										<span class="form-check-label">Author</span>
									</label>
									<!--end::Options-->
									<!--begin::Options-->
									<label class="form-check form-check-sm form-check-custom form-check-solid">
										<input class="form-check-input" type="checkbox" value="2" checked="checked" />
										<span class="form-check-label">Customer</span>
									</label>
									<!--end::Options-->
								</div>
								<!--end::Options-->
							</div>
							<!--end::Input group-->
							<!--begin::Input group-->
							<div class="mb-10">
								<!--begin::Label-->
								<label class="form-label fw-bold">Notifications:</label>
								<!--end::Label-->
								<!--begin::Switch-->
								<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
									<input class="form-check-input" type="checkbox" value="" name="notifications" checked="checked" />
									<label class="form-check-label">Enabled</label>
								</div>
								<!--end::Switch-->
							</div>
							<!--end::Input group-->
							<!--begin::Actions-->
							<div class="d-flex justify-content-end">
								<button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Reset</button>
								<button type="submit" class="btn btn-sm btn-primary" data-kt-menu-dismiss="true">Apply</button>
							</div>
							<!--end::Actions-->
						</div>
						<!--end::Form-->
					</div>
					<!--end::Menu 1-->
					<!--end::Menu-->
				</div>
			</div>
			<div class="card-body pt-5">
				<div class="timeline-label">
					<div class="timeline-item">
						<div class="timeline-label fw-bolder text-gray-800 fs-6">08:42</div>
						<!--end::Label-->
						<!--begin::Badge-->
						<div class="timeline-badge">
							<i class="fa fa-genderless text-warning fs-1"></i>
						</div>
						<!--end::Badge-->
						<!--begin::Text-->
						<div class="fw-mormal timeline-content text-muted ps-3">Outlines keep you honest. And keep structure</div>
						<!--end::Text-->
					</div>
					<!--end::Item-->
					<!--begin::Item-->
					<div class="timeline-item">
						<!--begin::Label-->
						<div class="timeline-label fw-bolder text-gray-800 fs-6">10:00</div>
						<!--end::Label-->
						<!--begin::Badge-->
						<div class="timeline-badge">
							<i class="fa fa-genderless text-success fs-1"></i>
						</div>
						<!--end::Badge-->
						<!--begin::Content-->
						<div class="timeline-content d-flex">
							<span class="fw-bolder text-gray-800 ps-3">AEOL meeting</span>
						</div>
						<!--end::Content-->
					</div>
					<!--end::Item-->
					<!--begin::Item-->
					<div class="timeline-item">
						<!--begin::Label-->
						<div class="timeline-label fw-bolder text-gray-800 fs-6">14:37</div>
						<!--end::Label-->
						<!--begin::Badge-->
						<div class="timeline-badge">
							<i class="fa fa-genderless text-danger fs-1"></i>
						</div>
						<!--end::Badge-->
						<!--begin::Desc-->
						<div class="timeline-content fw-bolder text-gray-800 ps-3">Make deposit
						<a href="#" class="text-primary">USD 700</a>. to ESL</div>
						<!--end::Desc-->
					</div>
					<!--end::Item-->
					<!--begin::Item-->
					<div class="timeline-item">
						<!--begin::Label-->
						<div class="timeline-label fw-bolder text-gray-800 fs-6">16:50</div>
						<!--end::Label-->
						<!--begin::Badge-->
						<div class="timeline-badge">
							<i class="fa fa-genderless text-primary fs-1"></i>
						</div>
						<!--end::Badge-->
						<!--begin::Text-->
						<div class="timeline-content fw-mormal text-muted ps-3">Indulging in poorly driving and keep structure keep great</div>
						<!--end::Text-->
					</div>
					<!--end::Item-->
					<!--begin::Item-->
					<div class="timeline-item">
						<!--begin::Label-->
						<div class="timeline-label fw-bolder text-gray-800 fs-6">21:03</div>
						<!--end::Label-->
						<!--begin::Badge-->
						<div class="timeline-badge">
							<i class="fa fa-genderless text-danger fs-1"></i>
						</div>
						<!--end::Badge-->
						<!--begin::Desc-->
						<div class="timeline-content fw-bold text-gray-800 ps-3">New order placed
						<a href="#" class="text-primary">#XF-2356</a>.</div>
						<!--end::Desc-->
					</div>
					<!--end::Item-->
					<!--begin::Item-->
					<div class="timeline-item">
						<!--begin::Label-->
						<div class="timeline-label fw-bolder text-gray-800 fs-6">16:50</div>
						<!--end::Label-->
						<!--begin::Badge-->
						<div class="timeline-badge">
							<i class="fa fa-genderless text-primary fs-1"></i>
						</div>
						<!--end::Badge-->
						<!--begin::Text-->
						<div class="timeline-content fw-mormal text-muted ps-3">Indulging in poorly driving and keep structure keep great</div>
						<!--end::Text-->
					</div>
					<!--end::Item-->
					<!--begin::Item-->
					<div class="timeline-item">
						<!--begin::Label-->
						<div class="timeline-label fw-bolder text-gray-800 fs-6">21:03</div>
						<!--end::Label-->
						<!--begin::Badge-->
						<div class="timeline-badge">
							<i class="fa fa-genderless text-danger fs-1"></i>
						</div>
						<!--end::Badge-->
						<!--begin::Desc-->
						<div class="timeline-content fw-bold text-gray-800 ps-3">New order placed
						<a href="#" class="text-primary">#XF-2356</a>.</div>
						<!--end::Desc-->
					</div>
					<!--end::Item-->
					<!--begin::Item-->
					<div class="timeline-item">
						<!--begin::Label-->
						<div class="timeline-label fw-bolder text-gray-800 fs-6">10:30</div>
						<!--end::Label-->
						<!--begin::Badge-->
						<div class="timeline-badge">
							<i class="fa fa-genderless text-success fs-1"></i>
						</div>
						<!--end::Badge-->
						<!--begin::Text-->
						<div class="timeline-content fw-mormal text-muted ps-3">Finance KPI Mobile app launch preparion meeting</div>
						<!--end::Text-->
					</div>
					<!--end::Item-->
				</div>
				<!--end::Timeline-->
			</div>
		</div>
	</div>
	<div class="col-xl-6">
		<div class="card card-xl-stretch mb-5 mb-xl-10">
			<div class="card-header border-0 pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label fw-bolder fs-3 mb-1">Produk Terlaris</span>
					<span class="text-muted mt-1 fw-bold fs-7">10 Produk teratas</span>
				</h3>
				<div class="card-toolbar">
					<ul class="nav">
						<li class="nav-item">
							<a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-dark active fw-bolder px-4 me-1" data-bs-toggle="tab" href="#kt_table_widget_5_tab_1">Month</a>
						</li>
						<li class="nav-item">
							<a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-dark fw-bolder px-4 me-1" data-bs-toggle="tab" href="#kt_table_widget_5_tab_2">Week</a>
						</li>
						<li class="nav-item">
							<a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-dark fw-bolder px-4" data-bs-toggle="tab" href="#kt_table_widget_5_tab_3">Day</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="card-body py-3">
				<div class="tab-content">
					<!--begin::Tap pane-->
					<div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
						<!--begin::Table container-->
						<div class="table-responsive">
							<!--begin::Table-->
							<table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
								<!--begin::Table head-->
								<thead>
									<tr class="border-0">
										<th class="p-0 w-50px"></th>
										<th class="p-0 min-w-150px"></th>
										<th class="p-0 min-w-140px"></th>
										<th class="p-0 min-w-110px"></th>
										<th class="p-0 min-w-50px"></th>
									</tr>
								</thead>
								<!--end::Table head-->
								<!--begin::Table body-->
								<tbody>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/plurk.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Brad Simmons</a>
											<span class="text-muted fw-bold d-block">Movie Creator</span>
										</td>
										<td class="text-end text-muted fw-bold">React, HTML</td>
										<td class="text-end">
											<span class="badge badge-light-success">Approved</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/telegram.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Popular Authors</a>
											<span class="text-muted fw-bold d-block">Most Successful</span>
										</td>
										<td class="text-end text-muted fw-bold">Python, MySQL</td>
										<td class="text-end">
											<span class="badge badge-light-warning">In Progress</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/vimeo.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">New Users</a>
											<span class="text-muted fw-bold d-block">Awesome Users</span>
										</td>
										<td class="text-end text-muted fw-bold">Laravel,Metronic</td>
										<td class="text-end">
											<span class="badge badge-light-primary">Success</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/bebo.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Active Customers</a>
											<span class="text-muted fw-bold d-block">Movie Creator</span>
										</td>
										<td class="text-end text-muted fw-bold">AngularJS, C#</td>
										<td class="text-end">
											<span class="badge badge-light-danger">Rejected</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/kickstarter.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Bestseller Theme</a>
											<span class="text-muted fw-bold d-block">Best Customers</span>
										</td>
										<td class="text-end text-muted fw-bold">ReactJS, Ruby</td>
										<td class="text-end">
											<span class="badge badge-light-warning">In Progress</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
								</tbody>
								<!--end::Table body-->
							</table>
						</div>
						<!--end::Table-->
					</div>
					<!--end::Tap pane-->
					<!--begin::Tap pane-->
					<div class="tab-pane fade" id="kt_table_widget_5_tab_2">
						<!--begin::Table container-->
						<div class="table-responsive">
							<!--begin::Table-->
							<table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
								<!--begin::Table head-->
								<thead>
									<tr class="border-0">
										<th class="p-0 w-50px"></th>
										<th class="p-0 min-w-150px"></th>
										<th class="p-0 min-w-140px"></th>
										<th class="p-0 min-w-110px"></th>
										<th class="p-0 min-w-50px"></th>
									</tr>
								</thead>
								<!--end::Table head-->
								<!--begin::Table body-->
								<tbody>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/plurk.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Brad Simmons</a>
											<span class="text-muted fw-bold d-block">Movie Creator</span>
										</td>
										<td class="text-end text-muted fw-bold">React, HTML</td>
										<td class="text-end">
											<span class="badge badge-light-success">Approved</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/telegram.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Popular Authors</a>
											<span class="text-muted fw-bold d-block">Most Successful</span>
										</td>
										<td class="text-end text-muted fw-bold">Python, MySQL</td>
										<td class="text-end">
											<span class="badge badge-light-warning">In Progress</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/bebo.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Active Customers</a>
											<span class="text-muted fw-bold d-block">Movie Creator</span>
										</td>
										<td class="text-end text-muted fw-bold">AngularJS, C#</td>
										<td class="text-end">
											<span class="badge badge-light-danger">Rejected</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
								</tbody>
								<!--end::Table body-->
							</table>
						</div>
						<!--end::Table-->
					</div>
					<!--end::Tap pane-->
					<!--begin::Tap pane-->
					<div class="tab-pane fade" id="kt_table_widget_5_tab_3">
						<!--begin::Table container-->
						<div class="table-responsive">
							<!--begin::Table-->
							<table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
								<!--begin::Table head-->
								<thead>
									<tr class="border-0">
										<th class="p-0 w-50px"></th>
										<th class="p-0 min-w-150px"></th>
										<th class="p-0 min-w-140px"></th>
										<th class="p-0 min-w-110px"></th>
										<th class="p-0 min-w-50px"></th>
									</tr>
								</thead>
								<!--end::Table head-->
								<!--begin::Table body-->
								<tbody>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/kickstarter.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Bestseller Theme</a>
											<span class="text-muted fw-bold d-block">Best Customers</span>
										</td>
										<td class="text-end text-muted fw-bold">ReactJS, Ruby</td>
										<td class="text-end">
											<span class="badge badge-light-warning">In Progress</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/bebo.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Active Customers</a>
											<span class="text-muted fw-bold d-block">Movie Creator</span>
										</td>
										<td class="text-end text-muted fw-bold">AngularJS, C#</td>
										<td class="text-end">
											<span class="badge badge-light-danger">Rejected</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/vimeo.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">New Users</a>
											<span class="text-muted fw-bold d-block">Awesome Users</span>
										</td>
										<td class="text-end text-muted fw-bold">Laravel,Metronic</td>
										<td class="text-end">
											<span class="badge badge-light-primary">Success</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<div class="symbol symbol-45px me-2">
												<span class="symbol-label">
													<img src="<?=base_url('t/')?>/media/svg/brand-logos/telegram.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td>
											<a href="#" class="text-dark fw-bolder text-hover-primary mb-1 fs-6">Popular Authors</a>
											<span class="text-muted fw-bold d-block">Most Successful</span>
										</td>
										<td class="text-end text-muted fw-bold">Python, MySQL</td>
										<td class="text-end">
											<span class="badge badge-light-warning">In Progress</span>
										</td>
										<td class="text-end">
											<a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
												<span class="svg-icon svg-icon-2">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
														<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</td>
									</tr>
								</tbody>
								<!--end::Table body-->
							</table>
						</div>
						<!--end::Table-->
					</div>
					<!--end::Tap pane-->
				</div>
			</div>
		</div>
	</div>
</div>