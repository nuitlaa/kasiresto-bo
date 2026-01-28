  <style>
    /* Light Metronic-like theme touches */
    body { background:#f5f7fb; font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
    .card-kt { border:0; border-radius:.75rem; box-shadow: 0 6px 18px rgba(35,47,62,0.06); }
    .table-row-dashed tbody tr { border-bottom: 1px dashed #e9eef5; }
    .product-row:hover { background: #fff; transform: translateY(-1px); }
    .badge-kategori { background:#eef2ff; color:#4338ca; font-weight:600; }
    .offcanvas-cart { width: 420px; }
    .price { font-weight:700; }
    .btn-pos { border-radius: .6rem; }
    .decinc { padding:5px !important; padding-left:10px !important;padding-right:10px !important; }

    .myfield {
      padding: 5px !important;
      font-size: 11px;
    }
  </style>

<div class="container py-4"> 

  <div class="row g-4">
    <!-- LEFT: Produk + search -->
    <div class="col-lg-8">
      <div class="card card-kt">
        <div class="card-body">

          <!-- KTSearch-like: instant keystroke search -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input id="ktSearch" class="form-control" placeholder="Ketik nama/kode, tekan Enter untuk cari cepat..." autocomplete="off">
            <button class="btn btn-outline-secondary" id="btnScan"><i class="bi bi-upc-scan"></i> Scan</button>
          </div>

          <!-- Pretend KTDatatable: responsive table area -->
          <div class="table-responsive" style="max-height:62vh; overflow:auto;">
            <table class="table align-middle table-row-dashed" id="ktDatatable">
              <thead class="text-muted small">
                <tr> 
                  <th>Nama Produk</th>
                  <th>Variasi</th>
                  <th class="text-center">Stok</th>
                  <th class="text-center">Qty</th>
                  <th class="text-center">Satuan</th>
                  <th class="text-end">Harga</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody id="productTable">
                <!-- rows injected by JS / AJAX -->
              </tbody>
            </table>

          </div>

        </div>
      </div>
    </div>

    <!-- RIGHT: Ringkasan / Quick Checkout -->
    <div class="col-lg-4">
      <div class="card card-kt">
        <div class="card-body">

          <div class="mb-3">
            <label class="form-label small text-muted">Pelanggan</label>
            <div class="d-flex align-items-center">
              <div id="selectedCustomer" class="flex-grow-1">Umum</div>
              <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalCustomer" onclick="pelanggan_batal()">Ubah</button>
              <input type="hidden" id="customer" name="customer" value="0">
              <input type="hidden" id="customerType" name="customerType" >
              <input type="hidden" id="defaultpembeli" name="">
            </div>
          </div>

          <hr>

          <h6 class="mb-3">Ringkasan <small style="color:dodgerblue;position: absolute;right: 30px;cursor: pointer;font-size: 11px;" onclick="clearCart()">Hapus Keranjang</small></h6>

          <div id="miniCart" style="max-height:40vh; overflow:auto;">
            <!-- cart items -->
            <div class="text-muted">Belum ada item di keranjang</div>
          </div>

          <div class="border-top pt-3 mt-3">
            <div class="d-flex justify-content-between mb-2">
              <div class="small text-muted">Subtotal</div>
              <div id="subtotalDisplay" class="price">Rp 0</div>
            </div>
            <div class="d-flex justify-content-between mb-2" style="border-top:1px dashed silver;padding-top: 5px;">
              <div class="small text-muted">Diskon <small onclick="tambahdiskon()" style="cursor: pointer;color:white;padding:2px 5px 2px 5px;background: royalblue;border-radius: 5px;" >+ Tambah</small></div>
              <script>
                function tambahdiskon(){
                  $("#modalDiscount").modal('show');
                  $(".discountvalue").val('');
                }
                async function fetchDiscount(q=''){
                  try{
                    const res = await fetch('/kasir/data_diskon');
                    const json = await res.json();
                    renderDiscount(json.data);

                    // total
                    document.getElementById("reviewTotal").innerText = "Rp " + toIDR(json.total||0);
                    document.getElementById('totalnya').value = (json.total||0);

                    document.getElementById('subtotalDisplay').innerText = toIDR(json.subtotal); 
                    document.getElementById('totalDisplay').innerText = toIDR(json.total||0);
                    document.getElementById('cashInput').value = toIDR(json.total||0);
                  }catch(e){
                    console.error("AJAX Discount ERROR:", e);   
                  }
                }

                function renderDiscount(discount){
                  const t = document.getElementById('discountlist');

                  if(!discount || discount.length === 0){
                    t.innerHTML = `
                        <div class="d-flex justify-content-between mb-2">
                          <div class="small text-muted"><small>Tidak ada diskon</small></div>
                        </div> `;
                  } 
                  else {
                    t.innerHTML = discount.map(c => `
                        <div class="d-flex justify-content-between mb-2">
                          <div class="small text-muted">${c.title} </div>
                          <div class="discountDisplay price">Rp ${toIDR(c.nominal)}</div>
                        </div> 
                    `).join('');

                    // diskon
                    document.getElementById("reviewDiscount").innerHTML = discount.map(c => `
                        <div class="d-flex justify-content-between mb-2">
                          <div class="small text-muted">${c.title} </div>
                          <div class="discountDisplay price">Rp ${toIDR(c.nominal)}</div>
                        </div> 
                    `).join('');
                  } 
                }
              </script>
            </div>
            <div id="discountlist"></div>

            <div class="d-flex justify-content-between fs-4 fw-bold">
              <div>Total</div>
              <div id="totalDisplay" class="price">Rp 0</div>
            </div>
          </div>

          <button id="btnCheckout" class="btn btn-success btn-pos w-100 mt-4" onclick="Checkoutdo()">Checkout (F8)</button>
          <script>
            function Checkoutdo(){
               /* ===== CEK CART ===== */
              if (!Array.isArray(carting) || carting.length === 0) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Keranjang kosong',
                  text: 'Silakan tambahkan produk terlebih dahulu'
                });
                return; // ⛔ STOP
              }

              /* ===== CEK CUSTOMER ===== */
              const customerId = document.getElementById('customer').value.trim();

              if (customerId !== '') {
                $(".hutangs").removeClass('d-none');
              } else {
                $(".hutangs").addClass('d-none');
              }

              document.getElementById('cashInput').value = document.getElementById('totalDisplay').innerText;
              $("#checkoutFinalModal").modal('show');
            }

          </script>

        </div>
      </div>
    </div>
  </div>
</div>


<!-- Modal Pilih Pelanggan (Metronic modal style) -->
<div class="modal fade" id="modalCustomer" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pilih_pelanggan_title">Pilih Pembeli</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="pelanggan_baru" method="post">
          <input type="hidden" id="id" name="id">
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 100px;">Nama</span>
            <input id="custName" name="save[name]" class="form-control addpelanggan" placeholder="Nama Pelanggan">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 100px;">HP</span>
            <input id="custHp" name="save[hp]" class="form-control addpelanggan" placeholder="Nomor Telepon">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 100px;">Alamat</span>
            <input id="custAddress" name="save[address]" class="form-control addpelanggan" placeholder="Alamat">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 100px;">Kategori</span>
            <select class="form-control" name="save[type]" id="kategori_pelanggan"></select>
          </div>
          <div style="width:100%;text-align: right;">
            <div class="btn btn-secondary" onclick="pelanggan_batal()">Batal</div>
            <div class="btn btn-primary" id="pelanggan_baru_btn" onclick="pelanggan_baru()">Tambah Pelanggan</div>
            <script> 
              function pelanggan_batal(){
                $("#pelanggan_baru").hide();
                $("#pelanggan_list").fadeIn();
                $(".addpelanggan").val('');
                $("#pilih_pelanggan_title").html('Pilih Pelanggan');
                $("#pelanggan_baru_btn").html('Tambah Pelanggan').removeClass('btn-warning').addClass('btn-primary');
              }
              function pelanggan_baru(){
                $("#pelanggan_baru_btn").html('menyimpan ...').removeClass('btn-primary').addClass('btn-warning'); 
                let form = document.getElementById("pelanggan_baru");
                let formData = new FormData(form); // <-- AMBIL SEMUA INPUT OTOMATIS
                $.ajax({
                    url: "/kasir/pelanggan_baru",
                    method: "POST",
                    data: formData,
                    processData: false,  // wajib
                    contentType: false,  // wajib
                    success: function(res){
                        console.log(res); 
                        if (res.status==true) {
                          $("#pelanggan_baru_btn").html('Tambah Pelanggan').removeClass('btn-warning').addClass('btn-primary');
                          fetchCustomers();
                        }
                    },
                    error: function(err){
                        console.log("Error:", err);
                    }
                }); 
              }
              function pelanggan_baru_form(){
                $("#pilih_pelanggan_title").html('Tambah Pelanggan');
                $("#pelanggan_list").hide();
                $("#pelanggan_baru").fadeIn();
                $("#pelanggan_baru_btn").html('Tambah Pelanggan').removeClass('btn-warning').addClass('btn-primary');
              }
            </script>
          </div>
        </form>
        <div id="pelanggan_list">
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input id="custSearch" class="form-control" placeholder="Cari nama / nomor HP...">
            <span class="btn btn-primary" onclick="pelanggan_baru_form()">Pembeli Baru</span>
          </div>
          <div class="table-responsive" style="max-height:50vh; overflow:auto;">
            <table class="table table-hover">
              <thead class="small text-muted"><tr><th>Nama</th><th>Jenis Pembeli</th><th>HP</th><th>Alamat</th><th></th></tr></thead>
              <tbody id="customerList"></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button class="btn btn-danger" onclick="kosongkanpembeli()">Kosongkan Pembeli</button>
        <script>
          function kosongkanpembeli(){
            $("#modalCustomer").modal('hide');
            $("#customer").val('');
            $("#customerType").val($("#defaultpembeli").val());
            $.post('<?=site_url('kasir/clearcust')?>').done(function(){location.reload();});
          }
        </script>
      </div>
    </div>
  </div>
</div>

<!-- Modal Pilih Pelanggan (Metronic modal style) -->
<div class="modal fade" id="modalDiscount" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pilih_pelanggan_title">Tambah Diskon</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="discount_baru" method="post">
          <input type="hidden" id="id" name="id">
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 150px;">Keterangan</span>
            <input id="discTitle" name="save[title]" class="form-control discountvalue" placeholder="Keterangan Diskon">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 150px;">Nominal</span>
            <input id="discNominal" name="save[nominal]" class="form-control discountvalue" placeholder="Nominal">
          </div> 
        </form> 
      </div>
      <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button class="btn btn-primary" id="diskon_baru_btn" onclick="discountsave()">Simpan</button>
          <script>  
            function discountsave(){
              $("#diskon_baru_btn").html('menyimpan ...').removeClass('btn-primary').addClass('btn-warning'); 
              let form = document.getElementById("discount_baru");
              let formData = new FormData(form); // <-- AMBIL SEMUA INPUT OTOMATIS
              $.ajax({
                  url: "/kasir/diskon_baru",
                  method: "POST",
                  data: formData,
                  processData: false,  // wajib
                  contentType: false,  // wajib
                  success: function(res){
                    $("#modalDiscount").modal('hide');
                      console.log(res); 
                      if (res.status==true) {
                        $("#diskon_baru_btn").html('Simpan').removeClass('btn-warning').addClass('btn-primary');
                        fetchDiscount();
                      }
                  },
                  error: function(err){
                      console.log("Error:", err);
                  }
              }); 
            } 
          </script>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let customerReqId = 0;


  function renderProducts(products) {
    let html = "";
    const tbody = document.getElementById('productTable');
    if(!products || products.length===0){ tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Produk tidak ditemukan</td></tr>'; return; }
    tbody.innerHTML = products.map(p => {
        // --- BUAT DROPDOWN DI DALAM MAP ---
        let dropdown = "";
        if (p.variasi && p.variasi.length > 0) {
          dropdown = `
            <select class="myfield form-select form-select-sm variant-select" 
                    data-product="${p.id}" id="variant-${p.id}" onchange="changeVariant(this)">
              ${p.variasi.map(v => `
                <option value="${v.id}"
                        data-harga="${v.harga}"
                        data-stok="${v.stok}"
                        data-sku="${v.sku}"
                        data-v1="${v.v1}"
                        data-v2="${v.v2 || ''}">
                  ${normalizehuman(v.v1)}${v.v2 ? ' - ' + normalizehuman(v.v2) : ''}
                </option>
              `).join("")}
            </select>
          `;
        }

        if (p.units && p.units.length > 0) {
          dropdownunit = `
            <select class="myfield form-select form-select-sm unit-select" id="unit-${p.id}" data-product="${p.id}" style="min-width:75px;padding-right:27px !important;" onchange="changeUnit(this)" >
              ${p.units.map(v => `<option value="${v.id}" id="unitnya${p.id}" data-id="${v.id}" data-multiplier="${v.multiplier}" >${v.name}</option>`).join("")} 
            </select>
          `;
        }
        const price = getPrice(p.id,p.idvarpro,p.idunit);
        // --- RETURN ROW HTML ---
        return `
          <tr class="product-row">
            
      
            <td>
              <div class="fw-semibold" style="font-size:11px;">${p.nama}</div>
              <label id="sku-${p.id}" style="font-size:9px;">${p.kode || p.id}</label>
              <div class="small text-muted" style="font-size:10px;display:none;">${p.kategori || ''}</div>
            </td>
      
            <td style="width:180px;">
              ${dropdown}
            </td>
      
            <td class="text-center" id="stok-${p.id}"  style="font-size:11px;">
              ${p.stok}
            </td>
            <td class="text-center">
              <input type="hidden" id="stock-${p.id}" value="${p.stok}" />
              <input type="number" max="${p.stok}" class="myfield form-control limit-number" style="width:75px;text-align:right;" id="qty-${p.id}" value="1" onchange="qtys(${p.id})" data-stok-base="${p.stok}" />
            </td>
      
            <td>${dropdownunit}</td>
            <td class="text-end price product-card"" id="price-${p.id}"  style="font-size:11px;" data-product="${p.id}" data-variant="${p.variasi[0].id}" data-unit="${p.units[0].id}" >
              <span class="product-price">${toIDR(price)}</span>
            </td>
      
            <td class="text-end">
              <button class="btn btn-sm btn-primary" onclick="addCartAjax('${p.id}')" style="white-space:nowrap;">
                <i class="bi bi-plus-lg"></i> Tambah
              </button>
            </td>
          </tr>
        `;
      }).join('');
  }

  // Format rupiah
  

  function qtys(id){
    let input = $("#qty-" + id); 
    let max = parseInt(input.attr('max')) || 999999;
    let val = parseInt(input.val()) || 0;
    if (val <= 0) {
        input.val(1);
    } else if (val > max) {
        input.val(max);
    }
  }

  // Event jika varian dipilih
  function changeVariant(el) {
    const productId = el.dataset.product;
    const option = el.selectedOptions[0];

    // ambil harga & stok varian
    const harga = option.dataset.harga;
    const stok = option.dataset.stok;
    const sku = option.dataset.sku;
    const variantId = el.value;
    const unitId = $("#unit-"+productId).val();

    const price = getPrice(productId, variantId, unitId);

    $("#qty-"+productId).attr('max',stok);
    //$("#price-"+productId).attr('data-variant',el.value);
    const card = document.getElementById("price-" + productId);
    card.dataset.variant = el.value;


    // update tampilan harga & stok
    if (price) document.getElementById("price-" + productId).innerHTML = '<span class="product-price">'+toIDR(price)+'</span>';
    if (stok) document.getElementById("stok-" + productId).innerHTML = stok;
    if (stok) document.getElementById("stock-" + productId).value = stok;
    if (sku) document.getElementById("sku-" + productId).innerHTML = sku;
  }
  function changeUnit(el){
    const productId = el.dataset.product;
    const option = el.selectedOptions[0];

    const multiplier = parseInt(option.dataset.multiplier) || 1;

    const qtyInput = document.getElementById(`qty-${productId}`);

    //$("#price-"+productId).attr('data-unit',el.value);
    const card = document.getElementById("price-" + productId);
    card.dataset.unit = el.value;

    // stok dasar (pcs)
    const stokBase = parseInt(qtyInput.dataset.stokBase) || 0;

    // hitung stok sesuai unit
    const stokUnit = Math.floor(stokBase / multiplier);

    // set max qty
    qtyInput.max = stokUnit;

    // reset qty kalau lebih besar dari max
    if (parseInt(qtyInput.value) > stokUnit) {
      qtyInput.value = stokUnit > 0 ? stokUnit : 1;
    }

    if (stokUnit) document.getElementById("stok-" + productId).innerHTML = stokUnit;
    if (stokUnit) document.getElementById("stock-" + productId).value = stokUnit;
    // OPTIONAL: tampilkan info stok

    const unitId = $("#unit-"+productId).val();
    const variantId = $("#variant-"+productId).val();
    const price = getPrice(productId, variantId, unitId); 
    if (price) document.getElementById("price-" + productId).innerHTML = '<span class="product-price">'+toIDR(price)+'</span>';
    console.log({
      stokBase,
      multiplier,
      stokUnit
    });
  }

  // -------------------------------
  // Simple POS logic + AJAX hooks
  // -------------------------------
  let cart = { items: [], total: 0, subtotal: 0, discount: 0 };
  let selectedCustomer = { id: null, name: 'Umum' };

  // Utility: format number to IDR
  function toIDR_(n){ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n); }
   
  let priceMap = {};
  let currentAccountType = null;

  // Fetch products: try to use server endpoint, fallback to local sample
  async function fetchProducts(q=''){
    try{
      const res = await fetch('/kasir/produk?q='+encodeURIComponent(q));
      if(!res.ok) throw new Error('no');
      const json = await res.json();
      /* ===== SIMPAN PRICE MAP ===== */
      if (json.price_map) {
        priceMap = json.price_map;
        currentAccountType = json.account_type;
      } else {
        priceMap = {};
      }


      renderProducts(json.data);

      document.getElementById('defaultpembeli').value = json.defaultpembeli;
      document.getElementById('customerType').value = json.account_type;
      document.getElementById('customer').value = json.aid;
      document.getElementById('selectedCustomer').innerHTML = json.aname;

      const select = document.getElementById('kategori_pelanggan');
      select.innerHTML = '';
      json.atype.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.id;
        opt.textContent = v.name;

        if (String(v.id) === String(json.account_type)) {
          opt.selected = true;
        }

        select.appendChild(opt);
      });

      if (json && json.cart && Array.isArray(json.cart.items)) {
          carting = json.cart.items;   // ini array isi cart
          cartInfo = json.cart;        // simpan subtotal, total
          updateCartUI();
      } else {
          console.error("Format cart tidak sesuai:", json);
      }
      if (json && json.discount && Array.isArray(json.discount)) {
          discount = json.discount;   // ini array isi cart 
          renderDiscount(discount);
      } else {
          console.error("Format cart tidak sesuai:", json);
      }
        

      return;
    }catch(e){ 
      console.error("AJAX Product ERROR:", e);   
    }
  }

  function getPrice(productId, variantId, unitId) {
    const variantPrices = priceMap?.[productId]?.[variantId];
    if (!variantPrices) return 0;

    return (
      variantPrices[unitId] ??
      variantPrices[Object.keys(variantPrices)[0]] ??
      0
    );
  }

  /*
  async function onCustomerChange(accountType) {
    const res = await fetch('/kasir/produk?account_type=' + accountType);
    const json = await res.json();

    priceMap = json.price_map;
    currentAccountType = accountType;

    refreshAllVisiblePrices();
  }
  */

 

  // Debounce helper for keystroke live search
  function debounce(fn, wait=250){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), wait); }; }

  // Live search binding
  const ktSearch = document.getElementById('ktSearch');
  ktSearch.addEventListener('input', debounce(e=>{ fetchProducts(e.target.value); }, 200));
  ktSearch.addEventListener('keypress', (e)=>{ if(e.key==='Enter'){ fetchProducts(ktSearch.value); } });

  // Add to cart via AJAX endpoint (server should return updated cart)
  async function addCartAjax(productId){
    try{ 
      var variantId = $("#variant-"+productId).val();
      var qty = $("#qty-"+productId).val();
      var unit = $("#unit-"+productId).val();
      var cust = $("#customer").val();

      const form = new FormData();
      form.append("variant", variantId);
      form.append("qty", qty); 
      form.append("customer", cust); 
      form.append("unit", unit); 
      const res = await fetch('/kasir/masukankeranjang/'+productId, {
          method: 'POST',
          body: form
      });
      
      const data = await res.json();
      if (data && data.cart && Array.isArray(data.cart.items)) {
          carting = data.cart.items;   // ini array isi cart
          cartInfo = data.cart;        // simpan subtotal, total
          updateCartUI();
      } else {
          console.error("Format cart tidak sesuai:", data);
      }

        
    }catch(e){
      // fallback: add locally
      console.error("AJAX ERROR:", e);  
      alert("AJAX ERROR: " + e); 
      //addCartLocal(productId);
    }
  }

  // Local cart add (fallback)
  function addCartLocal(id){
    // sample map
    const map = { 'BRG001':{id:'BRG001',nama:'Beras Premium',harga:60000}, 'BRG002':{id:'BRG002',nama:'Minyak Goreng 1L',harga:15000} };
    const p = map[id] || { id, nama:'Produk '+id, harga:0 };
    const found = cart.items.find(x=>x.id===p.id);
    if(found) found.qty++, found.subtotal = found.qty * p.harga; else cart.items.push({ id:p.id, nama:p.nama, qty:1, harga:p.harga, subtotal:p.harga });
    recalcCart(); updateCartUI();
  }

  function recalcCart(){
    // cart adalah array
    const subtotal = carting.reduce((s,i)=> s + (i.subtotal || (i.qty * i.price)), 0);

    cartSummary = {
        subtotal: subtotal,
        discount: 0,
        total: subtotal
    };
  }

  function updateCartUI() {
      if (!Array.isArray(carting)) {
          console.error("Cart bukan array:", carting);
          return;
      } 
      let html = ""; 
      const mini = document.getElementById('miniCart');
      if(!carting || carting.length === 0) mini.innerHTML = '<div class="text-muted">Belum ada item di keranjang</div>'; else {
        mini.innerHTML = carting.map(i=>`
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <div>
              <div class="fw-semibold">${i.product_name} - ${i.variant_name}</div>
              <div class="small text-muted">${i.qty} ${i.unit_name} x ${toIDR(i.price)}</div>
            </div>
            <div class="text-end">
              <div class="fw-bold">${toIDR(i.subtotal|| i.qty*i.price)}</div>
              <div class="mt-2">
                <button class="btn btn-sm btn-danger me-1 decinc" onclick="decrement('${i.id}')">-</button>
                <button class="btn btn-sm btn-primary decinc" onclick="increment('${i.id}')">+</button>
              </div>
            </div>
          </div>
        `).join('');

         // produk
        document.getElementById("reviewProducts").innerHTML =
        carting.map(i=>`
          <div class="d-flex justify-content-between border-bottom py-1">
            <div>${i.product_name} (${i.variant_name}) x ${i.qty}</div>
            <div>Rp ${toIDR(i.subtotal|| i.qty*i.price)}</div>
          </div>
        `).join('');
      }

      //recalcCart();
      document.getElementById('subtotalDisplay').innerText = toIDR(cartInfo.subtotal);
      document.getElementById('totalDisplay').innerText = toIDR(cartInfo.total||0);
      document.getElementById('cashInput').value = toIDR(cartInfo.total||0);


      // total
      document.getElementById("reviewTotal").innerText = "Rp " + toIDR(cartInfo.total);
      document.getElementById('totalnya').value = cartInfo.total;


      // reset input
      document.getElementById("cashInput").value = 0;
      document.getElementById("kembalian").innerText = "Rp 0";
  }

  async function increment(id){
    try{ 
      const res = await fetch('/kasir/inc/'+id, { method: 'POST'})
      const data = await res.json();
      if (data && data.cart && Array.isArray(data.cart.items)) {
          carting = data.cart.items;   // ini array isi cart
          cartInfo = data.cart;        // simpan subtotal, total
          updateCartUI();
      } else {
          console.error("Format cart tidak sesuai:", data);
      }
    }catch(e){
      console.error("AJAX Decrement ERROR:", e);  
    }
  }
  async function decrement(id){
    try{ 
      const res = await fetch('/kasir/dec/'+id, { method: 'POST'})
      const data = await res.json();
      if (data && data.cart && Array.isArray(data.cart.items)) {
          carting = data.cart.items;   // ini array isi cart
          cartInfo = data.cart;        // simpan subtotal, total
          updateCartUI();
      } else {
          console.error("Format cart tidak sesuai:", data);
      }
    }catch(e){
      console.error("AJAX Decrement ERROR:", e);  
    }
  }

  // remove / clear
  function clearCart(){ 
    Swal.fire({
        title: "Kosongkan Keranjang",
        html: `Anda yakin akan mengosongkan Keranjang ?`,
        icon: "warning",
        showCancelButton: true,
        reverseButtons: true, // 👈 INI KUNCINYA
        confirmButtonText: "Kosongkan",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'kasir/clear',
            type: "POST", 
            dataType: "json",   // <--- penting
            success: function(res){
                console.log(res);

                location.reload();
            },
            error: function(err){
                console.log("ERR:", err);
            }
          });
        }
    });
  }

  // customer modal: fetch list
  async function fetchCustomers(q=''){
    try{
      const res = await fetch('/kasir/daftar_pelanggan?q='+encodeURIComponent(q));
      const json = await res.json();
      renderCustomers(json.data);
    }catch(e){
      console.error("AJAX Customer ERROR:", e);   
    }
  }
  function renderCustomers(customers){
    const t = document.getElementById('customerList');

    if(!customers || customers.length === 0){
      t.innerHTML = `
        <tr>
          <td colspan="5" class="text-center text-muted">Pelanggan tidak ditemukan</td>
        </tr>`;
    } 
    else {
      t.innerHTML = customers.map(c => `
        <tr>
          <td>${c.name}</td>
          <td>${c.nametype}</td>
          <td>${c.hp || '-'}</td>
          <td>${c.address || '-'}</td>
          <td class='text-end'> <button class='btn btn-sm btn-primary' onclick='selectCustomer(${JSON.stringify(c).replace(/'/g, "\\'")})' data-bs-dismiss="modal">Pilih</button> </td>
        </tr>
      `).join('');
    }
    pelanggan_batal();

  }

  function selectCustomer(c){
    selectedCustomer = c;
    document.getElementById('selectedCustomer').innerText = c.name;
    document.getElementById('customer').value = c.id;
    document.getElementById('customerType').value = c.type;
    
    // pelanggan
    document.getElementById("reviewCustomer").innerHTML = `
      <div><strong>${c.name}</strong></div>
      <div>${c.nametype}</div>
      <div>${c.hp}</div>
      <div>${c.address}</div>
    `;
    $(".hutangs").removeClass('d-none');
    onCustomerChange(c.id);
  } 

  // bindings
  document.getElementById('custSearch').addEventListener('input', debounce(e=>fetchCustomers(e.target.value),200));

  async function onCustomerChange(pembeli) {
    setTimeout(() => changeCustomerReal(pembeli), 0);
  }
  async function changeCustomerReal(pembeli) {
    console.log('CHANGE TO:', pembeli);

     const reqId = ++customerReqId;

    try {
      const res = await fetch('/kasir/produk/' + pembeli, {
        cache: 'no-store'
      });
      if (!res.ok) throw new Error('price fetch failed');

      const json = await res.json();

      if (reqId !== customerReqId) return;

       /* ===== SET STATE DULU ===== */
        currentAccountType = json.accountType;
        priceMap = json.price_map || {};

      /* ===== REBUILD CART (CLONE) ===== */
      if (json.cart && Array.isArray(json.cart.items)) {
        carting  = json.cart.items.map(i => ({ ...i }));
        cartInfo = { ...json.cart };
      } else {
        carting = [];
        cartInfo = { subtotal: 0, total: 0 };
      }
      renderAll();
    } catch (e) {
      console.error('Customer change error:', e);
    }
  }

  function refreshAllVisiblePrices(){
    document.querySelectorAll('.product-card').forEach(card => {
      const productId = card.dataset.product;
      const variantSelect = document.getElementById('variant-' + productId);
      const unitSelect    = document.getElementById('unit-' + productId);

      if (!variantSelect || !unitSelect) return;

      const variantId = variantSelect.value;
      const unitId    = unitSelect.value;


      //const productId = card.dataset.product;
      //const variantId = card.dataset.variant;
      //const unitId    = card.dataset.unit;

      const price = getPrice(productId, variantId, unitId);

      const priceEl = card.querySelector('.product-price');
      if (priceEl) {
        priceEl.textContent = formatRupiah(price);
      }
    });
 
  }







  // keyboard shortcuts (F8 checkout)
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'F8') document.getElementById('btnCheckout').click();
  });


  // initial load
  fetchProducts();
  fetchCustomers();
  function refreshSummary() {
      updateCartUI(); // dipanggil di sini
  }
  function renderAll() {
    refreshAllVisiblePrices();
    updateCartUI();
  }


</script>

 <!-- MODAL FINAL CHECKOUT -->
<div class="modal fade" id="checkoutFinalModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title">Review & Konfirmasi Checkout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <!-- 1. DATA PELANGGAN -->
        <h6 class="fw-bold">Pelanggan</h6>
        <div id="reviewCustomer" class="mb-3">
          <!-- Diisi via JS -->
        </div>

        <hr>

        <!-- 2. PRODUK -->
        <h6 class="fw-bold">Rincian Produk</h6>
        <div id="reviewProducts" class="mb-3">
          <!-- Diisi via JS -->
        </div>

        <hr>

        <!-- 3. DISKON -->
        <h6 class="fw-bold">Diskon</h6>
        <div id="reviewDiscount" class="mb-3 text-danger fw-bold">
          <!-- Diisi via JS -->
        </div>

        <hr>

        <!-- 4. TOTAL -->
        <h4 class="fw-bold">Total: <span id="reviewTotal" style="position: absolute;right: 25px;">Rp 0</span></h4>
        <input type="hidden" id="totalnya" name="">
        <hr>

        <!-- 5. METODE PEMBAYARAN -->
        <h6 class="fw-bold">Metode Pembayaran</h6>

        <div class="btn-group w-100 mb-3" role="group">
          <input type="radio" class="btn-check" name="payMethod" id="payCash" value="cash" checked>
          <label class="btn btn-outline-primary" for="payCash">Cash</label>

          <input type="radio" class="btn-check" name="payMethod" id="payTransfer" value="transfer">
          <label class="btn btn-outline-primary" for="payTransfer">Transfer</label>

          <input type="radio" class="btn-check hutangs d-none" name="payMethod" id="payDebt" value="hutang">
          <label class="btn btn-outline-primary hutangs d-none" for="payDebt">Hutang</label>
        </div>

        <!-- 6. INPUT NOMINAL CASH -->
        <div id="cashSection">
          <label class="fw-bold">Nominal Uang Cash</label>
          <input type="text" class="form-control mb-2" id="cashInput" value="0" min="0" style="text-align: right;" onchange="uangnya()">

          <!-- TOMBOL UANG -->
          <div class="d-flex gap-2 mb-2">
            <button class="btn btn-outline-success uangBtn" data-val="100000">Rp 100.000</button>
            <button class="btn btn-outline-success uangBtn" data-val="50000">Rp 50.000</button>
            <button class="btn btn-outline-success uangBtn" data-val="20000">Rp 20.000</button>
          </div>

          <!-- KEMBALIAN -->
          <div class="alert alert-info">
            Kembalian: <span id="kembalian">Rp 0</span>
          </div>
        </div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary" id="finishCheckoutBtn" onclick="Checkout()">Selesaikan Checkout</button>
      </div>
      <script>
            function Checkout(){
              $("#finishCheckoutBtn").html('Sedang Menyimpan...').removeClass('btn-primary').addClass('btn-warning');
              $.ajax({
                url: 'kasir/checkout',
                type: "POST",
                data: {
                    customer: document.getElementById('customer').value,
                    cashInput: document.getElementById('cashInput').value,
                    paymethod: document.querySelector('input[name="payMethod"]:checked').value,
                },
                dataType: "json",   // <--- penting
                success: function(res){
                    console.log(res);

                    if (res.status === true) {
                        $("#finishCheckoutBtn").html('Tambah Pelanggan').removeClass('btn-warning').addClass('btn-primary');
                        Swal.fire({
                            title: "Transaksi Berhasil!",
                            html: `
                                <div style="font-size:20px; margin-top:10px;">
                                    Invoice: <b>${res.invoice}</b><br>
                                    Total: <b>${toIDR(res.total)}</b>
                                </div>
                            `,
                            icon: "success",
                            showCancelButton: true,
                            confirmButtonText: "Cetak Struk",
                            cancelButtonText: "Tutup"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open('kasir/print/' + res.invoice, '_blank');
                            }
                            location.reload(); // reset halaman kasir
                        });
                    }
                },
                error: function(err){
                    console.log("ERR:", err);
                }
              });

            }


            function toIDR(n) {
                n = Number(n);
                if (isNaN(n)) return "0";
                return n.toLocaleString("id-ID", { maximumFractionDigits: 0 });
            }

            // --- CASH SECTION ---
            $('.uangBtn').click(function(){
                let add = parseInt($(this).attr('data-val'));
                var str = document.getElementById("cashInput").value;
                let current = parseInt(str.replace(/[^0-9]/g, '')) || 0;
                document.getElementById("cashInput").value = toIDR(current + add);
                uangnya();
            });

            function uangnya(){
              let checkoutTotal = parseInt($("#totalnya").val().replace(/[^0-9]/g,'')) || 0;
              let bayar = parseInt($("#cashInput").val().replace(/[^0-9]/g,'')) || 0;

              let kembali = bayar - checkoutTotal;
              if(kembali < 0) kembali = 0;

              $("#kembalian").text("Rp " + toIDR(kembali));
            }
            // Hitung kembalian 

           

            // sembunyikan cash section jika bukan cash
            document.querySelectorAll("input[name='payMethod']").forEach(r=>{
              r.addEventListener("change", function(){
                if(this.value === "cash"){
                  document.getElementById("cashSection").style.display = "block";
                } else {
                  document.getElementById("cashSection").style.display = "none";
                }
              });
            });


      </script>
    </div>
  </div>
</div>
