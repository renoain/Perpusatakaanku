// peminjaman.js
document.addEventListener("DOMContentLoaded", () => {
  // Buat modal form dinamis (supaya tidak perlu ditulis di HTML)
  const modalHTML = `
    <div class="modal fade" id="borrowModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title"><i class="bi bi-journal-check"></i> Form Peminjaman Buku</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form id="borrowForm">
              <div class="mb-3">
                <label class="form-label">Nama Peminjam</label>
                <input type="text" class="form-control" id="borrowerName" placeholder="Masukkan nama kamu" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" id="borrowerEmail" placeholder="Masukkan email kamu" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Judul Buku</label>
                <input type="text" class="form-control" id="bookTitle" readonly>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Pinjam</label>
                  <input type="date" class="form-control" id="borrowDate" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Kembali</label>
                  <input type="date" class="form-control" id="returnDate" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Catatan / Keterangan</label>
                <textarea class="form-control" id="borrowNote" rows="2" placeholder="(Opsional)"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">ID Peminjaman</label>
                <input type="text" class="form-control" id="borrowId" readonly>
              </div>
              <button type="submit" class="btn btn-success w-100">Simpan & Pinjam Buku</button>
            </form>
          </div>
        </div>
      </div>
    </div>`;
  
  document.body.insertAdjacentHTML("beforeend", modalHTML);
  const borrowModal = new bootstrap.Modal(document.getElementById("borrowModal"));

  // Fungsi untuk membuka form dengan data buku yang diklik
  window.openBorrowForm = function(book) {
    document.getElementById("bookTitle").value = book.title;
    document.getElementById("borrowDate").value = new Date().toISOString().split("T")[0];
    
    // Default tanggal kembali 7 hari kemudian
    const returnDate = new Date();
    returnDate.setDate(returnDate.getDate() + 7);
    document.getElementById("returnDate").value = returnDate.toISOString().split("T")[0];

    document.getElementById("borrowId").value = "ELIB-" + Date.now().toString().slice(-6);
    borrowModal.show();
  };

  // Simpan form ke localStorage
  document.getElementById("borrowForm").addEventListener("submit", (e) => {
    e.preventDefault();

    const data = {
      idPeminjaman: document.getElementById("borrowId").value,
      nama: document.getElementById("borrowerName").value.trim(),
      email: document.getElementById("borrowerEmail").value.trim(),
      judul: document.getElementById("bookTitle").value,
      tanggalPinjam: document.getElementById("borrowDate").value,
      tanggalKembali: document.getElementById("returnDate").value,
      catatan: document.getElementById("borrowNote").value.trim(),
      status: "Dipinjam",
      denda: 0
    };

    let riwayat = JSON.parse(localStorage.getItem("riwayat")) || [];
    riwayat.push(data);
    localStorage.setItem("riwayat", JSON.stringify(riwayat));

    alert(`✅ Buku "${data.judul}" berhasil dipinjam!\nID Peminjaman: ${data.idPeminjaman}`);
    borrowModal.hide();
  });
});
