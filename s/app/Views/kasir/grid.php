<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Kasir POS Modern</title>
<style>
  body { font-family: Inter, Arial, sans-serif; margin: 0; background:#f2f2f7; }
  .header { display:flex; align-items:center; gap:15px; padding:12px 20px; background:#ff4d4d; color:#fff; }
  .search-box input { width:300px; padding:10px; border-radius:10px; border:none; }
  .container { display:flex; height: calc(100vh - 64px); }
  .left { flex:2; padding:20px; overflow-y:auto; }
  .right { flex:1; background:#fff; padding:20px; border-left:1px solid #ddd; display:flex; flex-direction:column; }

  /* Tabs */
  .tabs { display:flex; gap:20px; margin-bottom:20px; font-size:15px; font-weight:500; color:#777; }
  .tabs div { padding-bottom:8px; cursor:pointer; }
  .tabs .active { border-bottom:3px solid #ff4d4d; color:#ff4d4d; }

  /* Products */
  .products { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:15px; }
  .product-card { background:#fff; border-radius:12px; padding:10px; cursor:pointer; box-shadow:0 2px 6px rgba(0,0,0,0.08); transition:.15s; }
  .product-card:hover { transform:scale(1.03); }
  .product-card img { width:100%; height:120px; object-fit:cover; border-radius:8px; }
  .product-name { margin-top:8px; font-size:14px; font-weight:600; }

  /* Right Panel */
  .cart-title { font-weight:600; margin-bottom:10px; display:flex; justify-content:space-between; font-size:16px; }
  .cart-list { flex:1; overflow-y:auto; border-top:1px solid #eee; border-bottom:1px solid #eee; padding:15px 0; }
  .cart-item { display:flex; justify-content:space-between; margin-bottom:12px; font-size:14px; }
  .total-box { margin-top:15px; font-size:20px; font-weight:700; text-align:right; }
  .checkout { margin-top:15px; display:flex; gap:12px; }
  .btn { padding:12px; border:none; border-radius:10px; cursor:pointer; font-size:15px; font-weight:600; }
  .btn-save { background:#ffd54f; }
  .btn-pay { background:#ff4d4d; color:#fff; flex:1; }

  /* Modal */
  .modal-bg { position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); display:none; align-items:center; justify-content:center; }
  .modal-box { background:#fff; width:350px; padding:20px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,.2); }
  .modal-title { font-size:18px; font-weight:600; margin-bottom:12px; }
  .form-group { margin-bottom:12px; }
  select { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; }
  .modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:15px; }
  .btn-cancel { padding:10px 14px; background:#ccc; border-radius:8px; }
  .btn-add { padding:10px 14px; background:#ff4d4d; color:#fff; border-radius:8px; }
</style>
</head>
<body>

<div class="header">
  <div><strong>Kasir Modern</strong></div>
  <div class="search-box"><input type="text" placeholder="Cari Produk" /></div>
</div>

<div class="container">
  <div class="left">

    <div class="tabs">
      <div class="active">Semua</div>
      <div>Makanan</div>
      <div>Minuman</div>
      <div>Sayuran</div>
      <div>Hotplate</div>
      <div>Nasi</div>
    </div>

    <div class="products" id="productList"></div>

  </div>

  <div class="right">
    <div class="cart-title">
      <span>Daftar Pesanan</span>
      <span style="cursor:pointer;color:red">Hapus</span>
    </div>

    <div class="cart-list" id="cartList"></div>

    <div class="total-box" id="totalBox">Total: Rp 0</div>

    <div class="checkout">
      <button class="btn btn-save">Simpan</button>
      <button class="btn btn-pay">Bayar</button>
    </div>
  </div>
</div>

<!-- Modal Varian -->
<div class="modal-bg" id="variantModal">
  <div class="modal-box">
    <div class="modal-title">Pilih Varian</div>

    <div class="form-group">
      <label>Ukuran</label>
      <select id="varUkuran"></select>
    </div>

    <div class="form-group">
      <label>Warna</label>
      <select id="varWarna"></select>
    </div>

    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-add" onclick="confirmVariant()">Tambah</button>
    </div>
  </div>
</div>

<script>
const products = [
  {
    id: "P001",
    nama: "Ikan Bakar Saus Madu",
    price: 70000,
    img: "https://awsimages.detik.net.id/community/media/visual/2022/04/20/resep-gurame-bakar-bumbu-kecap-cabe_43.jpeg?w=650",
    variasi: [
      { ukuran:"S", warna:"Kuning" },
      { ukuran:"M", warna:"Merah" },
      { ukuran:"L", warna:"Hitam" }
    ]
  },
  {
    id: "P002",
    nama: "Ayam Penyet",
    price: 35000,
    img: "https://upload.wikimedia.org/wikipedia/commons/5/57/Ayam_penyet.JPG",
    variasi: [
      { ukuran:"S", warna:"Hijau" },
      { ukuran:"M", warna:"Putih" }
    ]
  }
];

let selectedProduct = null;
let cart = [];

const productList = document.getElementById("productList");
const cartList = document.getElementById("cartList");
const totalBox = document.getElementById("totalBox");

// Render Produk
function renderProducts() {
  productList.innerHTML = products.map(p => `
    <div class="product-card" onclick="openVariant('${p.id}')">
      <img src="${p.img}" />
      <div class="product-name">${p.nama}</div>
    </div>
  `).join("");
}

// Buka modal varian
function openVariant(id) {
  selectedProduct = products.find(x => x.id === id);

  const ukuranSet = [...new Set(selectedProduct.variasi.map(v => v.ukuran))];
  const warnaSet = [...new Set(selectedProduct.variasi.map(v => v.warna))];

  document.getElementById("varUkuran").innerHTML = ukuranSet.map(u=>`<option>${u}</option>`).join("");
  document.getElementById("varWarna").innerHTML = warnaSet.map(w=>`<option>${w}</option>`).join("");

  document.getElementById("variantModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("variantModal").style.display = "none";
}

function confirmVariant() {
  const ukuran = document.getElementById("varUkuran").value;
  const warna = document.getElementById("varWarna").value;

  const variantText = `${ukuran} | ${warna}`;

  const exists = cart.find(c => c.id===selectedProduct.id && c.variant===variantText);

  if (exists) exists.qty++;
  else cart.push({ id:selectedProduct.id, nama:selectedProduct.nama, price:selectedProduct.price, variant:variantText, qty:1 });

  closeModal();
  renderCart();
}

// Render Cart
function renderCart() {
  cartList.innerHTML = cart.map(c => `
    <div class="cart-item">
      <div>${c.nama}<br><small>${c.variant}</small> x${c.qty}</div>
      <div>Rp ${(c.price * c.qty).toLocaleString()}</div>
    </div>
  `).join("");

  const total = cart.reduce((a,b)=>a + (b.price * b.qty), 0);
  totalBox.innerHTML = `Total: Rp ${total.toLocaleString()}`;
}

renderProducts();
</script>

</body>
</html>