<div class="mb-5">
  <div class="d-flex flex-column">
    <span class="text-gray-600">Invoice</span>
    <span class="fs-4 fw-bold text-gray-800"><?= esc($order->invoice) ?></span>
  </div>
</div>

<div class="row mb-5">
  <div class="col-md-4">
    <div class="fw-bold text-gray-600">Tanggal</div>
    <div><?= date('d M Y H:i', strtotime($order->date)) ?></div>
  </div>
  <div class="col-md-4">
    <div class="fw-bold text-gray-600">Customer</div>
    <div><?= esc($order->namacustomer ?: 'Umum') ?></div>
  </div>
  <div class="col-md-4">
    <div class="fw-bold text-gray-600">Status</div>
    <span class="badge badge-light-<?= $order->lunas ? 'success' : 'warning' ?>">
      <?= $order->lunas ? 'Lunas' : 'Belum Lunas' ?>
    </span>
  </div>
</div>

<hr>

<h6 class="fw-bold mb-3">📦 Item Transaksi</h6>

<div class="table-responsive mb-5">
  <table class="table table-row-dashed align-middle">
    <thead>
      <tr class="fw-bold text-muted">
        <th>Produk</th>
        <th>Varian</th>
        <th class="text-center">Qty</th> 
        <th class="text-end">Harga</th>
        <th class="text-end">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $it): ?>
      <tr>
        <td class="fw-bold"><?= esc($it->product_name) ?></td>
        <td><?= esc($it->variant_name ?: '-') ?></td>
        <td class="text-center"><?= $it->qty.' '.$it->namaunit ?></td>
        <td class="text-end">Rp <?= number_format($it->nominal) ?></td>
        <td class="text-end fw-bold">Rp <?= number_format($it->subtotal) ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<hr>

<div class="row">
  <div class="col-md-6">
    <div class="fw-bold text-gray-600">Metode Pembayaran</div>
    <div><?= esc($order->payment_method ?: '-') ?></div>
  </div>
  <div class="col-md-6 text-end">
    <div class="fw-bold text-gray-600">Total</div>
    <div class="fs-4 fw-bold text-success">
      Rp <?= number_format($order->nominal) ?>
    </div>
  </div>
</div>