<?php $db = db_connect();
  $userid   = usertoken($_SESSION['usertoken']);
  $companyid  = companyid($userid);
    //session()->set('redirect_store', current_url());
    session()->set('redirect_worker', current_url());
    if($_SESSION['userty']=='owner'){
      $tokos = $db->table('account_store')->where(['company'=>$companyid])->get()->getResultArray();
      $user = $db->table('account a')->join('account_company b','b.owner=a.id', 'left')->select('a.name, a.foto, a.type,b.owner_name company_owner, b.name company_name, b.foto company_foto, b.address company_address,b.phone company_phone,b.email company_email,b.id company_id')->where('a.id', $userid)->get()->getRowArray();
    } else {
      $asp = $db->table('account_store_privilage')->where(['account'=>$userid])->get()->getRowArray();
      $tokos = $db->table('account_store')->where(['id'=>$asp['store']])->get()->getResultArray();
    }
    $tabs = array('today'=>'Harian','month'=>'Bulanan','year'=>'Tahunan');
    include('mod/hmenu_laporan.php'); 
?>
<div class="row g-xxl-9" id="thecontent">
  <div class="col-xxl-12">
    <div class="card card-xxl-stretch mb-5 mb-xl-10">
      <div class="card-header card-header-stretch">
        <div class="card-title">
          <h3 class="m-0 text-gray-900">Laporan Pembelanjaan</h3>
        </div>
        <div class="card-toolbar">
          <ul class="nav nav-tabs nav-line-tabs nav-stretch border-transparent fs-5 fw-bolder" id="kt_security_summary_tabs">
            <?php foreach($tabs as $k=>$v){ echo '
              <li class="nav-item">
                <a class="nav-link text-active-primary '.($k=='today'?'active':'').'" data-kt-countup-tabs="true" data-bs-toggle="tab" onclick="filtertype('."'".$k."'".')"  style="cursor:pointer;">'.$v.'</a>
              </li>';
            } ?> 
          </ul>
        </div>
      </div>
      <div class="card-body pt-7 pb-0 px-0">
        <div class="tab-content">
          
            <div class="tab-pane fade active show" id="tab_laporan_<?=$k?>" role="tabpanel">
              
              <div class="pt-2">
                <div class="d-flex align-items-center pb-6 px-9">
                  <!--begin::Title-->
                  <h3 class="m-0 text-gray-900 flex-grow-1">Pembelanjaan <label id="thetitle">Harian</label></h3>
                  <!--end::Title-->
                  <!--begin::Nav pills-->
                  <ul class="nav nav-pills nav-line-pills border rounded p-1">
                    <li class="nav-item me-2">
                      <a class="nav-link btn btn-active-light btn-active-color-gray-700 btn-color-gray-400 py-2 px-5 fs-6 fw-bold active" data-bs-toggle="tab" onclick="filtertoko(0)" style="cursor:pointer;">semua</a>
                    </li>
                    <?php foreach ($tokos as $key => $v) { echo '
                      <li class="nav-item me-2">
                        <a class="nav-link btn btn-active-light btn-active-color-gray-700 btn-color-gray-400 py-2 px-5 fs-6 fw-bold" data-bs-toggle="tab" id="lap_graph_tab_'.$v['id'].'" onclick="filtertoko('.$v['id'].')" style="cursor:pointer;">'.$v['name'].'</a>
                      </li>';
                    } ?> 
                  </ul>
                  <!--end::Nav pills-->
                </div>
              </div>
              <input type="hidden" id="filtertype" value="today" name="">
              <input type="hidden" id="filtertoko" value="semua" name="">
              <script>
                let lapGraphChart = null;
                function filtertoko(toko){
                  document.getElementById('filtertoko').value = toko;
                  filtering();
                }
                function filtertype(what){ 
                  var bohlam = '';
                  switch(what){
                    case 'today': bohlam = "Harian"; break;
                    case 'month': bohlam = "Bulanan"; break;
                    case 'year': bohlam = "Tahunan"; break;
                  }
                  $("#thetitle").html(bohlam); 
                  document.getElementById('filtertype').value = what;
                  filtering();
                }
                function filtering(){
                  toko = document.getElementById('filtertoko').value;
                  what = document.getElementById('filtertype').value;
                  $.post('<?=site_url('laporan/pembelanjaan')?>',{toko:toko,type:what}).done(function(data){
                    // omzet
                    updateCountUp('#count_totalbelanja', data.summary.totalbelanja);

                    // lainnya
                    updateCountUp('#count_transaksi', data.summary.transaksi);
                    updateCountUp('#count_item', data.summary.item); 
                    renderLapGraph(
                        data.chart.categories, 
                        data.chart.revenue
                    );
                  })

                }
                function renderLapGraph(categories, revenue) {

                    const el = document.querySelector('#lap_graph');
                    if (!el) return;

                    // BUAT BARU JIKA BELUM ADA
                    if (!lapGraphChart) {
                        lapGraphChart = new ApexCharts(el, {
                            chart: {
                                type: 'bar',
                                height: el.offsetHeight,
                                toolbar: { show: false }
                            },
                            series: [
                                { name: 'Total', data: revenue }
                            ],
                            xaxis: {
                                categories: categories
                            },
                            dataLabels: { enabled: false },
                            plotOptions: {
                                bar: {
                                    columnWidth: '35%',
                                    borderRadius: 6
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: val => 'Rp ' + val.toLocaleString()
                                }
                            },
                            colors: ['#009ef7']
                        });

                        lapGraphChart.render();
                        return;
                    }

                    // UPDATE JIKA SUDAH ADA
                    lapGraphChart.updateOptions({
                        xaxis: { categories: categories }
                    });

                    lapGraphChart.updateSeries([
                        { name: 'Total', data: revenue }
                    ]);
                }



                function updateCountUp(selector, value) {
                    let el = document.querySelector(selector);
                    if (!el) return;

                    value = parseInt(value) || 0;
                    el.innerHTML = '0';

                    // JIKA KTCountUp ADA (Metronic full)
                    if (typeof KTCountUp !== 'undefined') {
                        if (el._ktCountUp) {
                            el._ktCountUp.reset();
                        }

                        el._ktCountUp = new KTCountUp(el, value, {
                            duration: 1.5,
                            separator: ','
                        });
                        el._ktCountUp.start();
                    }
                    // FALLBACK (tanpa Metronic)
                    else {
                        animatePureJS(el, value);
                    }
                }
                function animatePureJS(el, value) {
                      let start = 0;
                      let duration = 800;
                      let startTime = null;

                      function animate(time) {
                          if (!startTime) startTime = time;
                          let progress = Math.min((time - startTime) / duration, 1);
                          el.innerHTML = Math.floor(progress * value).toLocaleString();

                          if (progress < 1) {
                              requestAnimationFrame(animate);
                          }
                      }

                      requestAnimationFrame(animate);
                }


                $(document).ready(function(){
                  filtering();
                  loadPenjualan();
                })
              </script>
              <div class="row p-0 mb-5 px-9">
                <div class="col">
                  <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                    <span class="fs-4 fw-bold text-success d-block">💰 Total Pembelanjaan</span>
                    <span class="fs-2hx fw-bolder text-gray-900" id="count_totalbelanja" data-kt-countup-prefix="Rp " data-kt-countup-separator=",">0</span>
                  </div>
                </div>
                <div class="col">
                  <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                    <span class="fs-4 fw-bold text-primary d-block">🧾 Jumlah Transaksi</span>
                    <span class="fs-2hx fw-bolder text-gray-900" id="count_transaksi" >0</span>
                  </div>
                </div>
                <div class="col">
                  <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                    <span class="fs-4 fw-bold text-primary d-block">📦 Jumlah Item Belanja</span>
                    <span class="fs-2hx fw-bolder text-gray-900" id="count_item" >0</span>
                  </div>
                </div> 
              </div>
              <!--end::Row-->
              <!--begin::Container-->
              <div class="pt-2">
                <!--begin::Tabs-->
                <!--end::Tabs-->
                <!--begin::Tab content-->
                <div class="tab-content px-3">
                  <div class="tab-pane fade active show" id="lap_graph_tabcontent" role="tabpanel">
                    <div id="lap_graph" style="height: 300px"></div>
                  </div>
                </div>
                <!--end::Tab content-->
              </div>
              <!--end::Container-->
            </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card card-flush mb-5 mb-xl-10">
   
  <div class="card-header align-items-center py-5 gap-2 gap-md-5">
      <div class="card-title">
        <h3 class="fw-bold">📋 Data Pembelanjaan</h3>
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

      <!-- Toko (Owner only) -->
      <?php if ($_SESSION['userty'] == 'owner'): ?>
      <div class="col-md-2">
        <label class="form-label">🏪 Toko</label>
        <select id="filterToko_" class="form-select form-select-sm">
          <option value="">Semua Toko</option>
          <?php foreach($db->table('account_store')->where(['company'=>$companyid])->get()->getResultArray() as $k=>$v){
            echo '<option value="'.$v['id'].'">'.$v['name'].'</option>';
          } ?>
          <!-- loop toko -->
        </select>
      </div>
      <?php endif ?>

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

            $.post("<?=site_url('laporan/getPembelian')?>", {
                page: currentPage,
                date: $('#filterDate').val(),
                toko: $('#filterToko').val(),
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

        $('#filterStatus, #filterToko').on('change', applyFilter);



        function showDetail(id) {
            $('#modalDetail').modal('show');
            $('#modalDetailBody').html('Loading...');

            $.get("<?=site_url('laporan/detailpembelian')?>/"+id, res => {
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
        <h5 class="modal-title">Detail Transaksi Pembelian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="modalDetailBody">
        <div class="text-center text-muted py-10">Loading...</div>


      </div>
    </div>
  </div>
</div>
