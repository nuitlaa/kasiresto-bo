<?php $db = db_connect();
  $userid   = usertoken($_SESSION['usertoken']);
  $companyid  = companyid($userid);
    //session()->set('redirect_store', current_url());
    session()->set('redirect_worker', current_url());
    if($_SESSION['userty']=='owner'){
      $tokos = $db->table('account_store')->where(['company'=>$companyid])->get()->getResultArray();
      $user = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    } else {
      $asp = $db->table('account_store_privilage')->where(['accoung'=>$_SESSION['userid']])->get()->getRowArray();
      $tokos = $db->table('account_store')->where(['id'=>$asp['store']])->get()->getResultArray();
    }
    $tabs = array('today'=>'Harian','month'=>'Bulanan','year'=>'Tahunan');
    include('mod/hmenu_laporan.php'); 
?>
<div class="row g-xxl-9" id="thecontent">
  <div class="col-xxl-12">
    <div class="card card-xxl-stretch mb-5 mb-xl-10">
      <div class="card-header mt-6">
        <div class="card-title flex-column">
          <h3 class="fw-bolder mb-1">Laporan Toko</h3>
          <div class="fs-6 d-flex text-gray-400 fs-6 fw-bold d-none">
            <div class="d-flex align-items-center me-6">
            <span class="menu-bullet d-flex align-items-center me-2">
              <span class="bullet bg-success"></span>
            </span>Complete</div>
            <div class="d-flex align-items-center">
            <span class="menu-bullet d-flex align-items-center me-2">
              <span class="bullet bg-primary"></span>
            </span>Incomplete</div>
          </div>
        </div>
        <div class="card-toolbar">
          <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bolder" id="kt_security_summary_tabs">
            <?php foreach($tabs as $k=>$v){ echo '
              <li class="nav-item">
                <a class="nav-link text-active-primary '.($k=='today'?'active':'').'" data-kt-countup-tabs="true" data-bs-toggle="tab" onclick="loadChart('."'".$k."'".')"  style="cursor:pointer;">'.$v.'</a>
              </li>';
            } ?> 
          </ul>
        </div>
      </div>
      <div class="card-body pt-10 pb-0 px-5">
        <div id="kt_project_overview_graph" class="card-rounded-bottom" style="height: 300px"></div>
          <script>
            "use strict";

            let lapChart = null;

            function renderOverviewChart(categories, series) {

                const el = document.getElementById("kt_project_overview_graph");
                if (!el || typeof ApexCharts === "undefined") return;

                const height = el.offsetHeight || 300;

                if (!lapChart) {
                    lapChart = new ApexCharts(el, {
                        chart: {
                            type: "area",
                            height: height,
                            toolbar: { show: false },
                            animations: {
                                enabled: true,
                                easing: "easeinout",
                                speed: 600
                            }
                        },
                        series: series,
                        dataLabels: { enabled: false },
                        stroke: {
                            curve: "smooth",
                            width: 3
                        },
                        xaxis: {
                            categories: categories,
                            labels: {
                                style: { fontSize: "12px" }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: val => 'Rp ' + val.toLocaleString()
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: val => 'Rp ' + val.toLocaleString()
                            }
                        },
                        grid: {
                            strokeDashArray: 4
                        }
                    });

                    lapChart.render();
                    return;
                }

                // update smooth
                lapChart.updateOptions({
                    xaxis: { categories }
                });

                lapChart.updateSeries(series);
            }
            function loadChart(type) {
                $.get("<?=site_url('laporan/toko/chart')?>", { type: type }, function(res) {
                    renderOverviewChart(res.categories, res.series);
                });
            }

            // default
            loadChart('today');
          </script>
      </div>
    </div> 
  </div> 
</div>
<div class="card card-flush mb-5 mb-xl-10">
   
  <div class="card-header align-items-center py-5 gap-2 gap-md-5">
      <div class="card-title">
        <h3 class="fw-bold">📋 Data Penjualan</h3>
      </div>
      <div class="card-toolbar"> 
      </div>
  </div>
  <div class="card-body">
    <div class="row g-3 align-items-end">

      <!-- Range Tanggal -->
      <div class="col-md-3">
        <label class="form-label">📅 Range Tanggal</label>
        <input type="text" id="filterDate" class="form-control form-control-sm"
               placeholder="Pilih tanggal">
      </div>


      <!-- Status -->
      <div class="col-md-2">
        <label class="form-label">💳 Status</label>
        <select id="filterStatus" class="form-select form-select-sm">
          <option value="">Semua</option>
          <option value="lunas">Lunas</option>
          <option value="belum">Belum</option>
        </select>
      </div>

      <!-- Search -->
      <div class="col-md-3">
        <label class="form-label">🔎 Invoice</label>
        <input type="text" id="filterSearch" class="form-control form-control-sm"
               placeholder="Cari invoice...">
      </div>

      <!-- Button -->
      <div class="col-md-2">
        <button class="btn btn-sm btn-primary w-100" onclick="loadPenjualan(true)">
          Filter
        </button>
      </div>
      <script>
        let isResetting = false;
        let currentPage = 1;
        let isLoading   = false;
        let hasMore     = true;
        let searchTimer = null;
        const debounce = (fn, delay = 500) => {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), delay);
            };
        };

        $('#filterSearch').on('input', debounce(() => {
            console.log('SCRIPT INIT debounce');

            loadPenjualan(true);
        }, 800));
        /*
        $('#filterSearch').on('keyup', function () {
            console.log('SCRIPT INIT keyup');

            clearTimeout(searchTimer);

            searchTimer = setTimeout(() => { 
                loadPenjualan(true);
            }, 2000); // 2 detik
        });
        $('#filterSearch').on('keydown', function(e){
            console.log('SCRIPT INIT keydown');

            if (e.key === 'Enter') {
                clearTimeout(searchTimer); 
                loadPenjualan(true);
            }
        });
        */

        $('#filterDate').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Pilih',
                cancelLabel: 'Batal'
            }
        });

        $('#filterDate').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(
                picker.startDate.format('YYYY-MM-DD') +
                ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            ); 
            loadPenjualan(true); // auto reload
        });

        $('#filterDate').on('cancel.daterangepicker', function () {
            $(this).val(''); 
            loadPenjualan(true);
        });


        function loadPenjualan(reset = false) {
            //console.log('LOAD', reset, currentPage);

            if (isLoading) return;
            if (!hasMore && !reset) return;

            if (reset) {
                isResetting = true;
                currentPage = 1;
                hasMore = true;
                $('#penjualanBody').html('');
            }

            isLoading = true;

            $('#penjualanBody').append(`
                <tr id="loaderRow">
                    <td colspan="7" class="text-center text-muted py-5">
                        Loading...
                    </td>
                </tr>
            `);

            $.post("<?=site_url('laporan/getPenjualan')?>", {
                page: currentPage,
                date: $('#filterDate').val(),
                status: $('#filterStatus').val(),
                search: $('#filterSearch').val()
            }).done(res => {

                $('#loaderRow').remove();

                if (!res.data || res.data.length === 0) {
                    hasMore = false;

                    if (currentPage === 1) {
                        $('#penjualanBody').html(`
                            <tr>
                                <td colspan="7" class="text-center text-muted py-10">
                                    Data tidak ditemukan
                                </td>
                            </tr>
                        `);
                    }
                    return;
                }

                let html = '';
                res.data.forEach(row => {
                    html += `
                    <tr>
                      <td class="fw-bold">${row.invoice}</td>
                      <td>${row.date}</td>
                      <td>${row.namacustomer}</td>
                      <td class="text-center">
                        <span class="badge badge-light-primary">${row.item}</span>
                      </td>
                      <td class="text-end fw-bold text-success">
                        Rp ${Number(row.total).toLocaleString()}
                      </td>
                      <td class="text-center">
                        <span class="badge badge-light-${row.lunas=='1'?'success':'warning'}">
                          ${row.lunas=='1'?'Lunas':'Belum'}
                        </span>
                      </td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-light-primary"
                            onclick="showDetail(${row.id})">
                          Detail
                        </button>
                      </td>
                    </tr>`;
                });

                $('#penjualanBody').append(html);
                currentPage++;

            }).always(() => {
                isLoading = false;

                // 🔓 buka infinite scroll SETELAH reset selesai
                if (isResetting) {
                    setTimeout(() => {
                        isResetting = false;
                    }, 300);
                }
            });
        }


        $(window).on('scroll', function () {

            if (isResetting) return;

            if (
                $(window).scrollTop() + $(window).height()
                >= $(document).height() - 200
            ) {
                loadPenjualan();
            }
        });


        function applyFilter() {
            loadPenjualan(true);
        }

        $('#filterStatus').on('change', applyFilter);



        function showDetail(id) {
            $('#modalDetail').modal('show');
            $('#modalDetailBody').html('Loading...');

            $.get("<?=site_url('laporan/detail')?>/"+id, res => {
                $('#modalDetailBody').html(res.html);
            });
        }
      </script>

    </div>
    <!--begin::Table wrapper-->
    <div class="table-responsive">
      <!--begin::Table-->
      <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
        <thead>
          <tr class="fw-bold text-muted">
            <th>Invoice</th>
            <th>Tanggal</th>
            <th>Customer</th>
            <th class="text-center">Item</th>
            <th class="text-end">Total</th>
            <th class="text-center">Status</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody id="penjualanBody"></tbody>
      </table>
    </div> 
  </div> 
</div> 
<div class="modal fade" id="modalDetail" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detail Transaksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="modalDetailBody">
        <div class="text-center text-muted py-10">Loading...</div>


      </div>
    </div>
  </div>
</div>
