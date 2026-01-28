							</div>
						</div>
					</div>
					<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
						<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
							<div class="text-dark order-2 order-md-1">
								<span class="text-muted fw-bold me-1"><?=date('Y')?>©</span>
								<a href="<?=sys('app-author-link')?>" target="_blank" class="text-gray-800 text-hover-primary"><?=sys('app-author')?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
				</svg>
			</span>
		</div>
	</body>
</html>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const target = document.getElementById("thecontent");
    const offset = 80;

    if (target) {
        const y = target.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top: y, behavior: "smooth" });
    }

    document.querySelectorAll('.uang').forEach(function(el){
        let nilai = el.innerText.replace(/[^0-9]/g, ''); // ambil angka saja
        if (nilai !== '') {
            el.innerText = formatRupiah(nilai);
        }
    });

    document.querySelectorAll('.coping').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();

            const text = this.dataset.copy || this.innerText;

            navigator.clipboard.writeText(text).then(() => {
                console.log('Copied:', text);
            }).catch(err => {
                console.error('Gagal copy', err);
            });
        });
    });


});
 	function copyText(e, el) {
	    e.preventDefault();
	    navigator.clipboard.writeText(el.innerText);
	}

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

function uang(input) {
  let value = input.value.replace(/[^0-9]/g, '');
  if (value === '') { input.value = ''; return; }
  let formatted = new Intl.NumberFormat('id-ID').format(value);
  input.value = formatted;
}

function deuang(inputId) {
  return inputId.replace(/[^0-9]/g, '');
}

function stringnormalize(str) {
  return str
    .trim()                       // hapus spasi awal & akhir
    .replace(/[^a-zA-Z0-9]+/g, '_') // spasi & simbol → _
    .replace(/_+/g, '_')           // __ atau lebih → _
    .replace(/^_+|_+$/g, '');      // hapus _ di awal & akhir
}

function normalizehuman(str) {
  if (!str) return '';
  return str
    .replace(/^_+/, '')   // hapus _ di awal
    .replace(/_/g, ' ');  // _ di tengah → spasi
}


function removing(table,id,field=false){
    if (field==false) {
        var namafield = table;
    } else {
        var namafield = field;
    }
    Swal.fire({
        html: `Anda yakin akan menghapus data dari tabel "${namafield}" ini ?`,
        icon: "warning",
        buttonsStyling: false,
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonText: "Hapus",
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
	    if (result.isConfirmed) {
			$.post('/a/remove/'+table+'/'+id).done(function(data){
				var y = JSON.parse(data);
				if (y.status==true) {
					$("#row"+id).fadeOut();
					Swal.fire({
				        html: `data telah dihapus ?`,
				        icon: "info"
				    })
				}
			})
	    }
	});

}

function golink(url){
	window.location.href = url;
}
</script>
