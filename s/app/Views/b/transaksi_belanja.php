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
                  <th class="text-center">Qty</th>
                  <th class="text-center">Satuan</th>
                  <th class="text-end">Harga per Satuan</th>
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
            <label class="form-label small text-muted">Supplier</label>
            <div class="d-flex align-items-center">
              <div id="selectedsupplier" class="flex-grow-1" style="color:darkblue;"></div>
              <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalsupplier" onclick="supplier_batal()">Ubah</button>
              <input type="hidden" id="supplier" name="supplier" value="0">
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
            <div id="discountlist"></div>

            <div class="d-flex justify-content-between fs-4 fw-bold">
              <div>Total</div>
              <div id="totalDisplay" class="price">Rp 0</div>
            </div>
          </div>

          <button id="btnCheckout" class="btn btn-success btn-pos w-100 mt-4" onclick="Checkoutdo()">Checkout (F8)</button>
          <script>
            function Checkoutdo(){
              var a = document.getElementById('supplier').value;
              if (a!='' && a!=0) {
                $("#checkoutFinalModal").modal('show');
              } else {
                alert('supplier harus di pilih terlebih dahulu');
              }
            }

          </script>

        </div>
      </div>
    </div>
  </div>
</div>


<!-- Modal Pilih Supplier (Metronic modal style) -->
<div class="modal fade" id="modalsupplier" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pilih_supplier_title">Pilih Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="supplier_baru" method="post">
          <input type="hidden" id="id" name="id">
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 100px;">Nama</span>
            <input id="custName" name="save[name]" class="form-control addsupplier" placeholder="Nama Supplier">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 100px;">HP</span>
            <input id="custHp" name="save[hp]" class="form-control addsupplier" placeholder="Nomor Telepon">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 100px;">Alamat</span>
            <input id="custAddress" name="save[address]" class="form-control addsupplier" placeholder="Alamat">
          </div>
          <div class="input-group mb-3" style="display: none;">
            <span class="input-group-text" style="min-width: 100px;">Kategori</span>
            <select class="form-control" name="save[type]" id="kategori_supplier"></select>
          </div>
          <div style="width:100%;text-align: right;">
            <div class="btn btn-secondary" onclick="supplier_batal()">Batal</div>
            <div class="btn btn-primary" id="supplier_baru_btn" onclick="supplier_baru()">Tambah Supplier</div>
            <script> 
              function supplier_batal(){
                $("#supplier_baru").hide();
                $("#supplier_list").fadeIn();
                $(".addsupplier").val('');
                $("#pilih_supplier_title").html('Pilih Supplier');
                $("#supplier_baru_btn").html('Tambah Supplier').removeClass('btn-warning').addClass('btn-primary');
              }
              function supplier_baru(){
                $("#supplier_baru_btn").html('menyimpan ...').removeClass('btn-primary').addClass('btn-warning'); 
                let form = document.getElementById("supplier_baru");
                let formData = new FormData(form); // <-- AMBIL SEMUA INPUT OTOMATIS
                $.ajax({
                    url: "/belanja/supplier_baru",
                    method: "POST",
                    data: formData,
                    processData: false,  // wajib
                    contentType: false,  // wajib
                    success: function(res){
                        console.log(res); 
                        if (res.status==true) {
                          $("#supplier_baru_btn").html('Tambah Supplier').removeClass('btn-warning').addClass('btn-primary');
                          fetchsupplier();
                        }
                    },
                    error: function(err){
                        console.log("Error:", err);
                    }
                }); 
              }
              function supplier_baru_form(){
                $("#pilih_supplier_title").html('Tambah Supplier');
                $("#supplier_list").hide();
                $("#supplier_baru").fadeIn();
                $("#supplier_baru_btn").html('Tambah Supplier').removeClass('btn-warning').addClass('btn-primary');
              }
            </script>
          </div>
        </form>
        <div id="supplier_list">
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input id="custSearch" class="form-control" placeholder="Cari nama / nomor HP...">
            <span class="btn btn-primary" onclick="supplier_baru_form()">Supplier Baru</span>
          </div>
          <div class="table-responsive" style="max-height:50vh; overflow:auto;">
            <table class="table table-hover">
              <thead class="small text-muted"><tr><th>Nama</th><th>HP</th><th>Alamat</th><th></th></tr></thead>
              <tbody id="supplierList"></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modalvariant" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pilih_variant_title">Tambah Variant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="variant_baru" method="post">
          <input type="hidden" class="addvariant" id="variantid" name="id">
          <input type="hidden" class="addvariant" id="variantproductid" name="product">
          <div class="input-group mb-3" id="group_var1">
            <span class="input-group-text" style="min-width: 120px;" id="title_var1">Varian 1</span>
            <input name="var1" class="form-control addvariant" placeholder="Varian 1" id="input_var1">
          </div>
          <div class="input-group mb-3" id="group_var2">
            <span class="input-group-text" style="min-width: 120px;" id="title_var2">Varian 2</span>
            <input name="var2" class="form-control addvariant" placeholder="Varian 2" id="input_var2">
          </div>
          <div class="input-group mb-3" style="display:none;">
            <span class="input-group-text" style="min-width: 120px;">Nama Variant</span>
            <input id="variantName" name="variant" class="form-control addvariant" placeholder="Nama Variant">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" style="min-width: 120px;">Stok Minimal</span>
            <input id="variantHp" name="min" class="form-control addvariant" placeholder="Stok Minimal Peringatan">
          </div>
          <div style="width:100%;text-align: right;">
            <div class="btn btn-secondary" onclick="variant_batal()">Batal</div>
            <div class="btn btn-primary" id="supplier_baru_btn" onclick="variant_baru()">Tambah Varian</div>
            <script> 
              function variant_batal(){
                $("#variant_baru").hide();
                $(".addvariant").val('');
                $("#variant_baru_btn").html('Tambah Variant').removeClass('btn-warning').addClass('btn-primary');
                $("#modalvariant").modal('hide');
              }
              function variant_baru(){
                $("#variant_baru_btn").html('menyimpan ...').removeClass('btn-primary').addClass('btn-warning'); 
                let form = document.getElementById("variant_baru");
                let formData = new FormData(form); // <-- AMBIL SEMUA INPUT OTOMATIS
                $.ajax({
                    url: "/belanja/variant_baru",
                    method: "POST",
                    data: formData,
                    processData: false,  // wajib
                    contentType: false,  // wajib
                    success: function(res){
                        console.log(res); 
                        if (res.status==true) {
                          $("#variant_baru_btn").html('Tambah Variant').removeClass('btn-warning').addClass('btn-primary');
                          $("#modalvariant").modal('hide');
                          
                            const productId = $("#variantproductid").val();
                          //$("#variant-"+productId).append('<option value="'+res.idnya+'">'+res.name+"</option>");


                            const select = $("#variant-" + productId);

                            const v = res.variant;

                            // bikin text option
                            const textOption = v.namavariant
                                ? v.namavariant
                                : [v.v1, v.v2].filter(Boolean).join(' - ');

                            // buat option baru
                            const option = `
                                <option value="${v.id}"
                                        data-harga=""
                                        data-stok=""
                                        data-sku="${v.sku || ''}"
                                        data-v1="${v.v1 || ''}"
                                        data-v2="${v.v2 || ''}"
                                        selected>
                                    ${textOption}
                                </option>
                            `;

                            // tambahkan SEBELUM option "Tambah Varian Baru"
                            select.find('option[value="tambah"]').before(option);

                            // set default ke varian baru
                            select.val(v.id).trigger('change');

                            // reset tombol & modal
                            $("#variant_baru_btn")
                                .html('Tambah Variant')
                                .removeClass('btn-warning')
                                .addClass('btn-primary');

                            $("#modalvariant").modal('hide');
                        }
                    },
                    error: function(err){
                        console.log("Error:", err);
                    }
                }); 
              } 
            </script>
          </div>
        </form> 
      </div> 
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function renderProducts(products) {
    let html = "";
    const tbody = document.getElementById('productTable');
    if(!products || products.length===0){ tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Produk tidak ditemukan</td></tr>'; return; }
    tbody.innerHTML = products.map(p => {
        // --- BUAT DROPDOWN DI DALAM MAP ---
        let dropdown = "";
        var var1 = p.var1;
        var var2 = p.var2;
        var harganya = 0;
        if (p.variasi && p.variasi.length > 0) {
          harganya = parseInt(p.variasi[0].harga) || 0;
          dropdown = `
            <select class="myfield form-select form-select-sm variant-select" style="padding-right:27px !important;"
                    data-product="${p.id}" id="variant-${p.id}" onchange="changeVariant(this)" 
                        data-var1="${var1}"
                        data-var2="${var2}" >
              ${p.variasi.map(v => `
                <option value="${v.id}"
                        data-harga="${v.harga}"
                        data-stok="${v.stok}"
                        data-sku="${v.sku}"
                        data-v1="${v.v1}"
                        data-v2="${v.v2 || ''}">
                  ${v.namavariant
                      ? v.namavariant
                      : [v.v1, v.v2].filter(Boolean).join(' - ')
                  }

                </option>
              `).join("")}
              <option value="tambah">Tambah Varian Baru</option>
            </select>
          `;
        }
        if (p.units && p.units.length > 0) {
          dropdownunit = `
            <select class="myfield form-select form-select-sm unit-select" id="unit-${p.id}" data-product="${p.id}" style="min-width:75px;padding-right:27px !important;" >
              ${p.units.map(v => `<option value="${v.id}" >${v.name}</option>`).join("")} 
            </select>
          `;
        }
      
        // --- RETURN ROW HTML ---
        return `
          <tr class="product-row"> 
            <td style="min-width:150px;">
              <div class="fw-semibold" style="font-size:11px;">${p.nama}</div>
              <div class="small text-muted" style="font-size:10px;">${p.kategori || ''}</div>
            </td>
      
            <td style="width:180px;">
              ${dropdown}
            </td>
            <td class="text-center">
              <input type="number" class="myfield form-control limit-number" style="min-width:50px;text-align:right;" id="qty-${p.id}" value="1" min="0" />
            </td>
      
            <td>${dropdownunit}</td>
            <td class="text-end price" id="price-${p.id}">
              <input type="text" class="myfield form-control limit-number" style="min-width:100px;;text-align:right;" id="harga-${p.id}" value="${toIDR(harganya)}" placeholder="Harga Beli" />
            </td>
      
            <td class="text-end">
              <button class="btn btn-sm btn-primary" onclick="addCartAjax('${p.id}')" style="white-space:nowrap;">
                <i class="bi bi-plus-lg"></i>
              </button>
            </td>
          </tr>
        `;
      }).join('');
  }

  // Format rupiah
  

  // Event jika varian dipilih
  function changeVariant(el) {
    const productId = el.dataset.product;
    const option = el.selectedOptions[0];
    const valuex = option.value;
    if (valuex=="tambah") { 
        $(".addvariant").val('');
        $("#variantproductid").val(productId);
        $("#modalvariant").modal('show');
      const var1 = el.dataset.var1;
      const var2 = el.dataset.var2;
      $("#group_var1").hide();
      $("#group_var2").hide();
      if (var1!='') {
        $("#group_var1").show();
        $("#input_var1").attr('placeholder',var1);
        $("#title_var1").html(var1);
      }
      if (var2!='') {
        $("#group_var2").show();
        $("#input_var2").attr('placeholder',var2);
        $("#title_var2").html(var2);
      }
    } else {
      // ambil harga & stok varian
      const harga = option.dataset.harga;
      const stok = option.dataset.stok;
      const sku = option.dataset.sku;

      //$("#qty-"+productId).attr('max',stok);
      // update tampilan harga & stok
      if (harga) document.getElementById("harga-" + productId).value = toIDR(harga);
      //if (harga) document.getElementById("price-" + productId).innerHTML = toIDR(harga);
      //if (stok) document.getElementById("stok-" + productId).innerHTML = stok;
      //if (stok) document.getElementById("stock-" + productId).value = stok;
      //if (sku) document.getElementById("sku-" + productId).innerHTML = sku;
    }
  }

  function tambahinvariant(){

  }

  // -------------------------------
  // Simple POS logic + AJAX hooks
  // -------------------------------
  let cart = { items: [], total: 0, subtotal: 0, discount: 0 };
  let selectedsupplier = { id: null, name: 'Umum' };

  // Utility: format number to IDR
  function toIDR_(n){ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n); }
   

  // Fetch products: try to use server endpoint, fallback to local sample
  async function fetchProducts(q=''){
    try{
      const res = await fetch('/belanja/produk?q='+encodeURIComponent(q));
      if(!res.ok) throw new Error('no');
      const json = await res.json();

      renderProducts(json.data);
   
      if (json && json.cart && Array.isArray(json.cart.items)) {
          carting = json.cart.items;   // ini array isi cart
          cartInfo = json.cart;        // simpan subtotal, total
          updateCartUI();
      } else {
          console.error("cart render Format cart tidak sesuai:", json);
      }
      if (json && json.discount && Array.isArray(json.discount)) {
          discount = json.discount;   // ini array isi cart 
          renderDiscount(discount);
      } else {
          console.error("discount Format cart tidak sesuai:", json);
      }
        

      return;
    }catch(e){ 
      console.error("AJAX Product ERROR:", e);   
    }
  } 

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
      var pri = $("#harga-"+productId).val();

      if (parseInt(pri)>0) {
        const form = new FormData();
        form.append("variant", variantId);
        form.append("unit", unit); 
        form.append("qty", qty); 
        form.append("price", pri); 
        const res = await fetch('/belanja/masukankeranjang/'+productId, {
            method: 'POST',
            body: form
        });
        
        const data = await res.json();
        if (data && data.cart && Array.isArray(data.cart.items)) {
            carting = data.cart.items;   // ini array isi cart
            cartInfo = data.cart;        // simpan subtotal, total
            updateCartUI();
        } else {
            console.error("Format Keranjang tidak sesuai:", data);
        }
      } else {
        alert('harga harus di isi terlebih dahulu');
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
          console.error("Cart bukan array:", cart);
          return;
      } 
      let html = "";
      const mini = document.getElementById('miniCart');
      if(!carting || carting.length === 0) mini.innerHTML = '<div class="text-muted">Belum ada item di keranjang</div>'; else {
        mini.innerHTML = carting.map(i=>`
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <div>
              <div class="fw-semibold">${i.product_name} - ${i.variant_name}</div>
              <div class="small text-muted">${i.qty} ${i.satuan} x ${toIDR(i.price)}</div>
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

      // total
      document.getElementById("reviewTotal").innerText = "Rp " + toIDR(cartInfo.total);
      document.getElementById('totalnya').value = cartInfo.total;


      // reset input
      document.getElementById("cashInput").value = 0;
      document.getElementById("kembalian").innerText = "Rp 0";
  }

  async function increment(id){
    try{ 
      const res = await fetch('/belanja/inc/'+id, { method: 'POST'})
      const data = await res.json();
      if (data && data.cart && Array.isArray(data.cart.items)) {
          carting = data.cart.items;   // ini array isi cart
          cartInfo = data.cart;        // simpan subtotal, total
          updateCartUI();
      } else {
          console.error("increment Format cart tidak sesuai:", data);
      }
    }catch(e){
      console.error("AJAX Decrement ERROR:", e);  
    }
  }
  async function decrement(id){
    try{ 
      const res = await fetch('/belanja/dec/'+id, { method: 'POST'})
      const data = await res.json();
      if (data && data.cart && Array.isArray(data.cart.items)) {
          carting = data.cart.items;   // ini array isi cart
          cartInfo = data.cart;        // simpan subtotal, total
          updateCartUI();
      } else {
          console.error("decrement Format cart tidak sesuai:", data);
      }
    }catch(e){
      console.error("AJAX Decrement ERROR:", e);  
    }
  }

  // remove / clear
  function clearCart(){ 
    $.ajax({
      url: '/belanja/clear',
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

  // supplier modal: fetch list
  async function fetchsupplier(q=''){
    try{
      const res = await fetch('/belanja/daftar_supplier?q='+encodeURIComponent(q));
      const json = await res.json();
      rendersupplier(json.data);
      const select = document.getElementById("kategori_supplier");
      json.type.forEach(item => {
          const option = document.createElement("option");
          option.value = item.id;
          option.textContent = item.name;   // what you want to display
          select.appendChild(option);
      });
    }catch(e){
      console.error("AJAX supplier ERROR:", e);   
    }
  }
  function rendersupplier(supplier){
    const t = document.getElementById('supplierList');

    if(!supplier || supplier.length === 0){
      t.innerHTML = `
        <tr>
          <td colspan="4" class="text-center text-muted">supplier tidak ditemukan</td>
        </tr>`;
    } 
    else {
      t.innerHTML = supplier.map(c => `
        <tr>
          <td>${c.name}</td>
          <td>${c.hp || '-'}</td>
          <td>${c.address || '-'}</td>
          <td class='text-end'> <button class='btn btn-sm btn-primary' onclick='selectsupplier(${JSON.stringify(c).replace(/'/g, "\\'")})' data-bs-dismiss="modal">Pilih</button> </td>
        </tr>
      `).join('');
    }
    supplier_batal();

  }

  function selectsupplier(c){
    selectedsupplier = c;
    document.getElementById('selectedsupplier').innerText = c.name;
    document.getElementById('supplier').value = c.id;

    // supplier
    document.getElementById("reviewsupplier").innerHTML = `
      <div><strong>${selectedsupplier.name}</strong></div>
      <div>${selectedsupplier.hp}</div>
      <div>${selectedsupplier.address}</div>
    `;
    $(".hutangs").removeClass('d-none');
  } 

  // bindings
  document.getElementById('custSearch').addEventListener('input', debounce(e=>fetchsupplier(e.target.value),200));





  // keyboard shortcuts (F8 checkout)
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'F8') document.getElementById('btnCheckout').click();
  });


  // initial load
  fetchProducts();
  fetchsupplier();
  function refreshSummary() {
      updateCartUI(); // dipanggil di sini
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

        <!-- 1. DATA supplier -->
        <h6 class="fw-bold">supplier</h6>
        <div id="reviewsupplier" class="mb-3">
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
        <h6 class="fw-bold" style="display:none;">Diskon</h6>
        <div id="reviewDiscount" class="mb-3 text-danger fw-bold" style="display:none;">
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
                url: '/belanja/checkout',
                type: "POST",
                data: {
                    supplier: document.getElementById('supplier').value,
                    cashInput: document.getElementById('cashInput').value,
                    paymethod: document.querySelector('input[name="payMethod"]:checked').value,
                },
                dataType: "json",   // <--- penting
                success: function(res){
                    console.log(res);

                    if (res.status === true) {
                        $("#finishCheckoutBtn").html('Tambah supplier').removeClass('btn-warning').addClass('btn-primary');
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
                                window.open('/belanja/print/' + res.invoice, '_blank');
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
