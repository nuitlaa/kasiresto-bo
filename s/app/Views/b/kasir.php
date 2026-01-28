<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kasir — Metronic-like POS</title>

  <!-- Bootstrap 5 (core) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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
  </style>
</head>
<body>

<div class="container py-4">
  <div class="d-flex align-items-center mb-4">
    <h2 class="me-auto">💳 Kasir — POS</h2>
    <div>
      <button class="btn btn-outline-secondary me-2" id="btnOpenCart"><i class="bi bi-cart3"></i> Keranjang <span id="cartBadge" class="badge bg-danger ms-2">0</span></button>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCustomer"><i class="bi bi-people"></i> Pilih Pelanggan</button>
    </div>
  </div>

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
                  <th style="width:100px">Kode</th>
                  <th>Nama Produk</th>
                  <th>Variasi</th>
                  <th class="text-center">Stok</th>
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
              <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalCustomer">Ubah</button>
            </div>
          </div>

          <hr>

          <h6 class="mb-3">Ringkasan</h6>
          <div id="miniCart" style="max-height:40vh; overflow:auto;">
            <!-- cart items -->
            <div class="text-muted">Belum ada item di keranjang</div>
          </div>

          <div class="border-top pt-3 mt-3">
            <div class="d-flex justify-content-between mb-2">
              <div class="small text-muted">Subtotal</div>
              <div id="subtotalDisplay" class="price">Rp 0</div>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <div class="small text-muted">Diskon</div>
              <div id="discountDisplay" class="price">Rp 0</div>
            </div>
            <div class="d-flex justify-content-between fs-4 fw-bold">
              <div>Total</div>
              <div id="totalDisplay" class="price">Rp 0</div>
            </div>
          </div>

          <button id="btnCheckout" class="btn btn-success btn-pos w-100 mt-4">Checkout (F8)</button>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Offcanvas Cart (Metronic drawer-like) -->
<div class="offcanvas offcanvas-end offcanvas-cart" tabindex="-1" id="offcanvasCart">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Keranjang</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body px-3">
    <div id="cartCanvasItems">--</div>
    <div class="mt-3">
      <button class="btn btn-danger w-100 mb-2" id="btnClearCart">Bersihkan Keranjang</button>
      <button class="btn btn-primary w-100" id="btnProceed">Lanjut ke Pembayaran</button>
    </div>
  </div>
</div>

<!-- Modal Pilih Pelanggan (Metronic modal style) -->
<div class="modal fade" id="modalCustomer" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Pelanggan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="custSearch" class="form-control" placeholder="Cari nama / nomor HP...">
        </div>
        <div class="table-responsive" style="max-height:50vh; overflow:auto;">
          <table class="table table-hover">
            <thead class="small text-muted"><tr><th>Nama</th><th>HP</th><th>Alamat</th><th></th></tr></thead>
            <tbody id="customerList"></tbody>
          </table>
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
  if(!products || products.length===0){ tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Produk tidak ditemukan</td></tr>'; return; }
  tbody.innerHTML = products.map(p => {
    
      // --- BUAT DROPDOWN DI DALAM MAP ---
      let dropdown = "";
    
      if (p.variasi && p.variasi.length > 0) {
        dropdown = `
          <select class="form-select form-select-sm variant-select" 
                  data-product="${p.id}" onchange="changeVariant(this)">
            ${p.variasi.map(v => `
              <option value="${v.id}"
                      data-harga="${v.harga}"
                      data-stok="${v.stok}"
                      data-v1="${v.v1}"
                      data-v2="${v.v2 || ''}">
                ${v.v1}${v.v2 ? ' - ' + v.v2 : ''}
              </option>
            `).join("")}
          </select>
        `;
      }
    
      // --- RETURN ROW HTML ---
      return `
        <tr class="product-row">
          <td>${p.kode || p.id}</td>
    
          <td>
            <div class="fw-semibold">${p.nama}</div>
            <div class="small text-muted">${p.kategori || ''}</div>
          </td>
    
          <td style="width:180px;">
            ${dropdown}
          </td>
    
          <td class="text-center" id="stock-${p.id}">
            ${p.stok}
          </td>
    
          <td class="text-end price" id="price-${p.id}">
            ${toIDR(p.harga)}
          </td>
    
          <td class="text-end">
            <button class="btn btn-sm btn-primary" onclick="addCartAjax('${p.id}')">
              <i class="bi bi-plus-lg"></i> Tambah
            </button>
          </td>
        </tr>
      `;
    
    }).join('');

}

// Format rupiah
const toIDR = n => new Intl.NumberFormat('id-ID').format(n);

// Event jika varian dipilih
function changeVariant(el) {
  const productId = el.dataset.product;
  const option = el.selectedOptions[0];

  // ambil harga & stok varian
  const harga = option.dataset.harga;
  const stok = option.dataset.stok;

  // update tampilan harga & stok
  if (harga) document.getElementById("price-" + productId).innerHTML = toIDR(harga);
  if (stok) document.getElementById("stock-" + productId).innerHTML = stok;
}

// Render awal
//renderProducts();
</script>
<script>
// -------------------------------
// Simple POS logic + AJAX hooks
// -------------------------------
let cart = { items: [], total: 0, subtotal: 0, discount: 0 };
let selectedCustomer = { id: null, name: 'Umum' };

// Utility: format number to IDR
function toIDR_(n){ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n); }

// Render product rows (used after AJAX search)
function renderProducts__(products){
  const tbody = document.getElementById('productList');
  if(!products || products.length===0){ tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Produk tidak ditemukan</td></tr>'; return; }
  tbody.innerHTML = products.map(p=>`
    <tr class="product-row">
        <td>${p.kode || p.id}</td>
    
        <td>
            <div class="fw-semibold">${p.nama}</div>
            <div class="small text-muted">
                ${p.kategori || ''}
    
                ${p.var1 ? `<span class='badge bg-secondary ms-2'>${p.var1}</span>` : ''}
                ${p.var2 ? `<span class='badge bg-dark ms-1'>${p.var2}</span>` : ''}
            </div>
        </td>
    
        <td class="text-end price">${toIDR(p.harga)}</td>
        <td class="text-center">${p.stok ?? '-'}</td>
    
        <td class="text-end">
            <button class="btn btn-sm btn-primary" onclick="addCartAjax('${p.id}')">
                <i class="bi bi-plus-lg"></i> Tambah
            </button>
        </td>
    </tr>

  `).join('');
}

// Fetch products: try to use server endpoint, fallback to local sample
async function fetchProducts(q=''){
  try{
    const res = await fetch('/kasir/produk?q='+encodeURIComponent(q));
    if(!res.ok) throw new Error('no');
    const json = await res.json();
    renderProducts(json);
    return;
  }catch(e){
    // fallback sample
    const sample = [
      {id:'BRG001',kode:'BRG001',nama:'Beras Premium',harga:60000,stok:12,kategori:'Bahan Pokok'},
      {id:'BRG002',kode:'BRG002',nama:'Minyak Goreng 1L',harga:15000,stok:30,kategori:'Minyak'},
      {id:'BRG003',kode:'BRG003',nama:'Gula Pasir 1Kg',harga:14000,stok:20,kategori:'Gula'}
    ];
    const filtered = sample.filter(s=>!q || (s.nama.toLowerCase().includes(q.toLowerCase())|| s.kode.toLowerCase().includes(q.toLowerCase())));
    renderProducts(filtered);
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
    
    const res = await fetch('/kasir/masukankeranjang/'+productId, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
          variant: variantId,
          qty: qty
        })
    });
    
    const data = await res.json();
    if(data) { cart = data; updateCartUI(); }
      
  }catch(e){
    // fallback: add locally
    addCartLocal(productId);
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
  cart.subtotal = cart.items.reduce((s,i)=>s + (i.subtotal|| (i.qty*i.harga)), 0);
  cart.discount = cart.discount || 0;
  cart.total = cart.subtotal - cart.discount;
}

function updateCartUI(){
  // mini list
  const mini = document.getElementById('miniCart');
  if(!cart.items || cart.items.length===0) mini.innerHTML = '<div class="text-muted">Belum ada item di keranjang</div>'; else {
    mini.innerHTML = cart.items.map(i=>`
      <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
        <div>
          <div class="fw-semibold">${i.nama}</div>
          <div class="small text-muted">${i.qty} x ${toIDR(i.harga)}</div>
        </div>
        <div class="text-end">
          <div class="fw-bold">${toIDR(i.subtotal|| i.qty*i.harga)}</div>
          <div class="mt-2">
            <button class="btn btn-sm btn-outline-secondary me-1" onclick="decrement('${i.id}')">-</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="increment('${i.id}')">+</button>
          </div>
        </div>
      </div>
    `).join('');
  }

  // cart offcanvas body
  document.getElementById('cartCanvasItems').innerHTML = mini.innerHTML;

  // totals
  recalcCart();
  document.getElementById('subtotalDisplay').innerText = toIDR(cart.subtotal);
  document.getElementById('discountDisplay').innerText = toIDR(cart.discount||0);
  document.getElementById('totalDisplay').innerText = toIDR(cart.total||0);

  // badge
  document.getElementById('cartBadge').innerText = cart.items.length || 0;
}

function increment(id){
  // try server
  fetch('/cart/inc/'+id, { method:'POST'}).then(r=>r.json()).then(d=>{ cart=d; updateCartUI(); }).catch(()=>{ const it=cart.items.find(x=>x.id===id); if(it){ it.qty++; it.subtotal=it.qty*it.harga; updateCartUI(); } });
}
function decrement(id){
  fetch('/cart/dec/'+id, { method:'POST'}).then(r=>r.json()).then(d=>{ cart=d; updateCartUI(); }).catch(()=>{ const it=cart.items.find(x=>x.id===id); if(it){ it.qty--; if(it.qty<=0) cart.items = cart.items.filter(x=>x.id!==id); else it.subtotal=it.qty*it.harga; updateCartUI(); } });
}

// remove / clear
function clearCart(){ fetch('/cart/clear', { method:'POST' }).then(r=>r.json()).then(d=>{ cart=d; updateCartUI(); }).catch(()=>{ cart={items:[],total:0,subtotal:0,discount:0}; updateCartUI(); }); }

// customer modal: fetch list
async function fetchCustomers(q=''){
  try{
    const res = await fetch('/kasir/pelanggan/list?q='+encodeURIComponent(q));
    const json = await res.json();
    renderCustomers(json);
  }catch(e){
    // fallback sample
    const sample = [ {id:1,nama:'Andi',hp:'081234',alamat:'Bandung'},{id:2,nama:'Siti',hp:'089876',alamat:'Jakarta'} ];
    renderCustomers(sample.filter(s=>!q || s.nama.toLowerCase().includes(q.toLowerCase())));
  }
}
function renderCustomers(customers){
  const t = document.getElementById('customerList');
  if(!customers || customers.length===0) t.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Pelanggan tidak ditemukan</td></tr>';
  else t.innerHTML = customers.map(c=>`<tr><td>${c.nama}</td><td>${c.hp||'-'}</td><td>${c.alamat||'-'}</td><td class='text-end'><button class='btn btn-sm btn-primary' onclick="selectCustomer(${JSON.stringify(c).replace(/'/g,'\\\\')})" data-bs-dismiss='modal'>Pilih</button></td></tr>`).join('');
}
function selectCustomer(c){ selectedCustomer = c; document.getElementById('selectedCustomer').innerText = c.nama; }

// bindings
document.getElementById('custSearch').addEventListener('input', debounce(e=>fetchCustomers(e.target.value),200));

// offcanvas open
const offcanvasCart = new bootstrap.Offcanvas(document.getElementById('offcanvasCart'));
document.getElementById('btnOpenCart').addEventListener('click', ()=> offcanvasCart.toggle());

// clear cart button
document.getElementById('btnClearCart').addEventListener('click', ()=>{ if(confirm('Bersihkan keranjang?')) clearCart(); });

// proceed / checkout
document.getElementById('btnProceed').addEventListener('click', ()=>{ alert('Lanjut ke pembayaran — integrasikan dengan endpoint /checkout'); });

// keyboard shortcuts (F8 checkout)
document.addEventListener('keydown', (e)=>{
  if(e.key === 'F8') document.getElementById('btnCheckout').click();
});

// initial load
fetchProducts();
fetchCustomers();
updateCartUI();

</script>


</body>
</html>
