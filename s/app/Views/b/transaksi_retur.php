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
                  <th class="text-center">Stok</th> 
                  <th class="text-center">Jumlah Retur</th>
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
        var stoknya = 0;
        if (p.variasi && p.variasi.length > 0) {
        	stoknya = parseInt(p.variasi[0].stok) || 0;
          dropdown = `
            <select class="form-select form-select-sm variant-select" 
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
            </select>
          `;
        }
      
        // --- RETURN ROW HTML ---
        return `
          <tr class="product-row"> 
            <td>
              <div class="fw-semibold">${p.nama}</div>
              <div class="small text-muted">${p.kategori || ''}</div>
            </td>
      
            <td style="min-width:180px;">
              ${dropdown}
            </td>
            <td id="stok-${p.id}" data-value="${stoknya}">${stoknya}</td> 
            <td class="text-center">
              <input type="number" class="form-control limit-number" style="width:100%;text-align:right;" id="qty-${p.id}" value="1" min="0" max="${stoknya}" onchange="inputqty(${p.id})" />
            </td>
       
            <td class="text-end">
              <button class="btn btn-sm btn-danger" onclick="addreturn('${p.id}')" style="white-space:nowrap;">
                <i class="bi bi-trash"></i> Retur
              </button>
            </td>
          </tr>
        `;
      }).join('');
  }

  // Format rupiah
  function inputqty(prid){
  	var v = $("#qty-"+prid).val();
  	var m = $("#qty-"+prid).attr('max');
  	if (parseInt(v) > parseInt(m)) {
  		$("#qty-"+prid).val(m);
  	}
  }
  function addreturn(productId){
	Swal.fire({
	  title: 'Retur?',
	  text: 'Apakah produk ini akan di retur ?',
	  icon: 'question',
	  showCancelButton: true,
	  confirmButtonText: 'Ya',
	  cancelButtonText: 'Nanti saja',
	  reverseButtons: true
	}).then((result) => {
	  if (result.isConfirmed) {
	    // aksi jika YA
	    var varid = $("#variant-"+productId).val();
	    var qty = $("#qty-"+productId).val();
	    $.post('/retur',{variant:varid,qty:qty}).done(function(ret){
      		if(!ret.status) throw new Error('no');
      		location.reload();
	    })
	  }
	});

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
      
      //if (harga) document.getElementById("price-" + productId).innerHTML = toIDR(harga);
      if (stok) document.getElementById("stok-" + productId).innerHTML = stok;
      $("#qty-"+productId).attr('max',stok);
      //if (stok) document.getElementById("stock-" + productId).value = stok;
      //if (sku) document.getElementById("sku-" + productId).innerHTML = sku;
    	inputqty(productId);
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
 

  // Local cart add (fallback)
  function addCartLocal(id){
    // sample map
    const map = { 'BRG001':{id:'BRG001',nama:'Beras Premium',harga:60000}, 'BRG002':{id:'BRG002',nama:'Minyak Goreng 1L',harga:15000} };
    const p = map[id] || { id, nama:'Produk '+id, harga:0 };
    const found = cart.items.find(x=>x.id===p.id);
    if(found) found.qty++, found.subtotal = found.qty * p.harga; else cart.items.push({ id:p.id, nama:p.nama, qty:1, harga:p.harga, subtotal:p.harga });
    recalcCart();
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
   




  // keyboard shortcuts (F8 checkout)
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'F8') document.getElementById('btnCheckout').click();
  });


  // initial load
  fetchProducts(); 


</script>
 