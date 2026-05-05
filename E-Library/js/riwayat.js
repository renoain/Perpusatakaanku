document.addEventListener("DOMContentLoaded", () => {
  const riwayatList = document.getElementById("riwayat-list");

  // Ambil data riwayat dari localStorage
  function getRiwayat() {
    return JSON.parse(localStorage.getItem("riwayat")) || [];
  }

  // Simpan data ke localStorage
  function saveRiwayat(data) {
    localStorage.setItem("riwayat", JSON.stringify(data));
    localStorage.setItem("lastUpdate", Date.now());
  }

  // Render daftar riwayat peminjaman
  function renderRiwayat() {
    const riwayat = getRiwayat();
    riwayatList.innerHTML = "";

    if (riwayat.length === 0) {
      riwayatList.innerHTML = `
        <div class="text-center text-muted py-5">
          <i class="bi bi-clock-history display-1 d-block mb-3 text-secondary"></i>
          <h5>Belum ada riwayat peminjaman</h5>
          <p>Pinjam buku dari koleksi dan lihat di sini nanti 📘</p>
        </div>
      `;
      return;
    }

    // urutkan terbaru di atas
    riwayat.slice().reverse().forEach((book, index) => {
      const date = new Date(book.tanggalPinjam);
      const now = new Date();
      const daysDiff = Math.floor((now - date) / (1000 * 60 * 60 * 24));

      // Hitung denda otomatis
      let status = book.status || "Dipinjam";
      let denda = 0;

      if (status === "Dipinjam" && daysDiff > 7) {
        denda = (daysDiff - 7) * 1000;
        status = `Terlambat (${daysDiff - 7} hari)`;
      } else if (status === "Dikembalikan") {
        denda = book.denda || 0;
      }

      const card = document.createElement("div");
      card.className = "col-lg-4 col-md-6 col-sm-10";
      card.innerHTML = `
        <div class="card h-100 shadow-sm border-0 hover-shadow">
          <img src="${book.img || book.image || 'img/default-book.jpg'}" 
               class="card-img-top" alt="${book.title || book.judul}" 
               onerror="this.src='img/default-book.jpg'"
               style="height: 300px; object-fit: cover;">
          <div class="card-body text-center">
            <h5 class="fw-bold">${book.title || book.judul}</h5>
            <p class="text-muted"><i class="bi bi-person"></i> ${book.author || book.penulis}</p>
            <p class="mt-3 small text-muted">
              <i class="bi bi-calendar-event"></i> Dipinjam: ${date.toLocaleDateString("id-ID")}
            </p>
            <p class="fw-semibold">
              Status: <span class="${status.includes('Terlambat') ? 'text-danger' : 'text-success'}">${status}</span>
            </p>
            ${denda > 0 ? `<p class="text-danger">Denda: Rp${denda.toLocaleString()}</p>` : ""}
          </div>
          <div class="card-footer bg-white border-0 text-center pb-3">
            ${
              book.status !== "Dikembalikan"
                ? `<button class="btn btn-sm btn-success me-2" onclick="kembalikanBuku(${index})">
                     <i class="bi bi-arrow-return-left"></i> Kembalikan
                   </button>`
                : ""
            }
            <button class="btn btn-sm btn-danger" onclick="hapusRiwayat(${index})">
              <i class="bi bi-trash"></i> Hapus
            </button>
          </div>
        </div>
      `;
      riwayatList.appendChild(card);
    });
  }

  // Fungsi kembalikan buku
  window.kembalikanBuku = function (index) {
    const riwayat = getRiwayat();
    const item = riwayat[index];
    if (!item) return;

    const pinjamDate = new Date(item.tanggalPinjam);
    const now = new Date();
    const diffDays = Math.floor((now - pinjamDate) / (1000 * 60 * 60 * 24));
    let denda = 0;

    if (diffDays > 7) {
      denda = (diffDays - 7) * 1000;
      alert(`📚 Buku dikembalikan terlambat ${diffDays - 7} hari.\n💰 Denda: Rp${denda.toLocaleString()}`);
    } else {
      alert("✅ Buku berhasil dikembalikan tepat waktu!");
    }

    item.status = "Dikembalikan";
    item.denda = denda;
    saveRiwayat(riwayat);
    renderRiwayat();
  };

  // Fungsi hapus buku dari riwayat
  window.hapusRiwayat = function (index) {
    const riwayat = getRiwayat();
    if (!confirm("Yakin ingin menghapus riwayat ini?")) return;
    riwayat.splice(index, 1);
    saveRiwayat(riwayat);
    renderRiwayat();
  };

  // Jalankan render awal
  renderRiwayat();

  // Auto-refresh setiap 3 detik (sinkronisasi dengan admin dashboard)
  setInterval(renderRiwayat, 3000);
});
