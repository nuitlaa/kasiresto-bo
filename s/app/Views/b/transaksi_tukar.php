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
  </style>

<div class="container py-4"> 

  <div class="row g-4">
    <!-- LEFT: Produk + search -->
    <div class="col-lg-12">
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

  </div>
</div>
 
 
<!-- Modal Pilih Supplier (Metronic modal style) -->
<div class="modal fade" id="tukarmod" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pilih_supplier_title">Pilih Produk pengganti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"> 
        <div id="supplier_list">
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input id="custSearch" class="form-control" placeholder="Cari Produk">
          </div>
          <div class="table-responsive" style="max-height:50vh; overflow:auto;">
            <table class="table align-middle table-row-dashed" id="ktDatatable3">
              <thead class="text-muted small">
                <tr>
                  <th>Nama Produk</th>
                  <th>Variasi</th> 
                  <th class="text-center">Qty</th>
                  <th class="text-center">Satuan</th>
                  <th class="text-end">Harga per Satuan</th>
                  <th class="text-end">&nbsp;</th>
                </tr>
              </thead>
              <tbody id="productTablepilih">
                	<tr>
	                  <th id="pilih_produk">Nama Produk</th>
	                  <th id="pilih_variasi">Variasi</th> 
	                  <th id="pilih_qty" class="text-center">Qty</th>
	                  <th id="pilih_satuan" class="text-center">Satuan</th>
	                  <th id="pilih_harga" class="text-end">Harga per Satuan</th>
	                  <th class="text-end">&nbsp;</th>
	              </tr>
              </tbody>
            </table>
            <hr />
            Pilih Produk Pengganti
            <table class="table align-middle table-row-dashed" id="ktDatatable2">
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
              <tbody id="productTablepengganti">
                <!-- rows injected by JS / AJAX -->
              </tbody>
            </table>
            <input type="hidden" id="from_variant" name="">
            <input type="hidden" id="from_qty" name="">
            <input type="hidden" id="from_unit" name="">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
    const tbodi = document.getElementById('productTablepengganti');

    if(!products || products.length===0){ 
    	tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Produk tidak ditemukan</td></tr>'; return; 
    	tbodi.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Produk tidak ditemukan</td></tr>'; return; 
    }
    tbody.innerHTML = products.map(p => {
        // --- BUAT DROPDOWN DI DALAM MAP ---
        let dropdown = "";
        var var1 = p.var1;
        var var2 = p.var2;
        var harganya = 0;
        if (p.variasi && p.variasi.length > 0) {
          harganya = parseInt(p.variasi[0].harga) || 0;
          dropdown = `
            <select class="form-select form-select-sm variant-select" 
                    data-product="${p.id}" id="variant-${p.id}" onchange="changeVariant(this)" 
                        data-var1="${var1}"
                        data-var2="${var2}" >
              ${p.variasi.map(v => `
                <option value="${v.id}" id="varianya-${v.id}"
                        data-harga="${v.harga}"
                        data-stok="${v.stok}"
                        data-sku="${v.sku}"
                        data-v1="${v.v1}"
                        data-v2="${v.v2 || ''}">
                  ${v.namavariant
                      ? v.namavariant
                      : [v.v1, v.v2].filter(Boolean).join(' - ')
                  } - (stok : ${v.stok} )

                </option>
              `).join("")} 
            </select>
          `;
        }
        if (p.units && p.units.length > 0) {
          dropdownunit = `
            <select class="form-select form-select-sm unit-select" id="unit-${p.id}" data-product="${p.id}" >
              ${p.units.map(v => `<option value="${v.id}"  id="unitnya-${v.id}" >${v.name}</option>`).join("")} 
            </select>
          `;
        }
      
        // --- RETURN ROW HTML ---
        return `
          <tr class="product-row"> 
            <td>
              <div class="fw-semibold" id="row_produk${p.id}">${p.nama}</div>
              <div class="small text-muted">${p.kategori || ''}</div>
            </td>
      
            <td style="min-width:180px;">
              ${dropdown}
            </td>
            <td class="text-center">
              <input type="number" class="form-control limit-number" style="width:75px;text-align:right;" id="qty-${p.id}" value="1" min="0" />
            </td>
      
            <td>${dropdownunit}</td>
            <td class="text-end price" id="price-${p.id}">
              <input type="text" class="form-control limit-number" style="width:100$;text-align:right;" id="harga-${p.id}" value="${toIDR(harganya)}" placeholder="Harga Beli" />
            </td>
      
            <td class="text-end">
              <button class="btn btn-sm btn-primary" onclick="tukar('${p.id}')" style="white-space:nowrap;">
                <i class="bi bi-switch"></i> Tukar
              </button>
            </td>
          </tr>
        `;
      }).join('');


    tbodi.innerHTML = products.map(p => {
        // --- BUAT DROPDOWN DI DALAM MAP ---
        let dropdown = "";
        var var1 = p.var1;
        var var2 = p.var2;
        var harganya = 0;
        if (p.variasi && p.variasi.length > 0) {
          harganya = parseInt(p.variasi[0].harga) || 0;
          dropdown = `
            <select class="form-select form-select-sm variant-select" 
                    data-product="${p.id}" id="pengganti_variant-${p.id}" onchange="changeVariant(this)" 
                        data-var1="${var1}"
                        data-var2="${var2}" >
              ${p.variasi.map(v => `
                <option value="${v.id}" id="pengganti_varianya-${v.id}"
                        data-harga="${v.harga}"
                        data-stok="${v.stok}"
                        data-sku="${v.sku}"
                        data-v1="${v.v1}"
                        data-v2="${v.v2 || ''}">
                  ${v.namavariant
                      ? v.namavariant
                      : [v.v1, v.v2].filter(Boolean).join(' - ')
                  } - (stok : ${v.stok} )

                </option>
              `).join("")} 
            </select>
          `;
        }
        if (p.units && p.units.length > 0) {
          dropdownunit = `
            <select class="form-select form-select-sm unit-select" id="pengganti_unit-${p.id}" data-product="${p.id}" >
              ${p.units.map(v => `<option value="${v.id}"  id="pengganti_unitnya-${v.id}" >${v.name}</option>`).join("")} 
            </select>
          `;
        }
      
        // --- RETURN ROW HTML ---
        return `
          <tr class="product-row"> 
            <td>
              <div class="fw-semibold" id="pengganti_row_produk${p.id}">${p.nama}</div>
              <div class="small text-muted">${p.kategori || ''}</div>
            </td>
      
            <td style="min-width:180px;">
              ${dropdown}
            </td>
            <td class="text-center">
              <input type="number" class="form-control limit-number pengganti_qty" style="width:75px;text-align:right;" id="pengganti_qty-${p.id}" value="1" min="0" readonly />
            </td>
      
            <td>${dropdownunit}</td>
            <td class="text-end price" id="pengganti_price-${p.id}">
              <input type="text" class="form-control limit-number" style="width:100$;text-align:right;" id="pengganti_harga-${p.id}" value="${toIDR(harganya)}" placeholder="Harga Beli" />
            </td>
      
            <td class="text-end">
              <button class="btn btn-sm btn-primary" onclick="tukarin('${p.id}')" style="white-space:nowrap;">
                <i class="bi bi-switch"></i> Tukar
              </button>
            </td>
          </tr>
        `;
      }).join('');
  }

  function tukarin(p){
	Swal.fire({
	  title: 'Penukaran?',
	  text: 'Anda akan menukar produk ini ?',
	  icon: 'question',
	  showCancelButton: true,
	  confirmButtonText: 'Ya',
	  cancelButtonText: 'Nanti saja',
	  reverseButtons: true
	}).then((result) => {
	  if (result.isConfirmed) {
	    // aksi jika YA

	  	var varian1 = $("#from_variant").val();
	  	var qty1 = $("#from_qty").val();
	  	var varian2 = $("#pengganti_variant-"+p).val();
	  	var qty2 = $("#pengganti_qty-"+p).val();
	    
	    $.post('/tukar',{v1:varian1,v2:varian2,q1:qty1,q2:qty2}).done(function(ret){
      		if(!ret.status) throw new Error('no');
      		location.reload();
	    })
	  }
	});

  }

  // Format rupiah
  function tukar(p){
  	$("#tukarmod").modal('show');
  	$("#pilih_produk").html($("#row_produk"+p).html());
  	$("#pilih_variasi").html($("#varianya-"+$("#variant-"+p).val()).html() );
  	$("#pilih_qty").html($("#qty-"+p).val());
  	$("#pilih_satuan").html($("#unitnya-"+$("#unit-"+p).val()).html());
  	$("#pilih_harga").html($("#harga-"+p).val());

  	$("#from_variant").val($("#variant-"+p).val());
  	$("#from_qty").val($("#qty-"+p).val());
  	$("#from_unit").val($("#unit-"+p).val());
  	$(".pengganti_qty").val($("#qty-"+p).val());
  }

  // Event jika varian dipilih
  function changeVariant(el) {
    const productId = el.dataset.product;
    const option = el.selectedOptions[0];
    const valuex = option.value;
     
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
          
      } else {
          console.error("cart render Format cart tidak sesuai:", json);
      }
      if (json && json.discount && Array.isArray(json.discount)) {
          discount = json.discount;   // ini array isi cart 
          renderDiscount(discount);
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
 




  // keyboard shortcuts (F8 checkout)
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'F8') document.getElementById('btnCheckout').click();
  });


  // initial load
  fetchProducts(); 
  function refreshSummary() { 
  }


  
	function toIDR(n) {
	    n = Number(n);
	    if (isNaN(n)) return "0";
	    return n.toLocaleString("id-ID", { maximumFractionDigits: 0 });
	}
  	const custSearch = document.getElementById('custSearch');
	custSearch.addEventListener('input', debounce(e=>{ fetchProducts(e.target.value); }, 200));
  	custSearch.addEventListener('keypress', (e)=>{ if(e.key==='Enter'){ fetchProducts(custSearch.value); } });
</script>

 