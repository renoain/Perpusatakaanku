// books.js — Koleksi Buku + Form Peminjaman + Wishlist + Sinkronisasi Data
document.addEventListener("DOMContentLoaded", () => {

  // ======= DATA DEFAULT =======
  const defaultBooks = [
    {
      id: 1,
      title: "Laskar Pelangi",
      author: "Andrea Hirata",
      year: 2005,
      inventory: "INV-ELIB-001",
      image: "img/Book 1.jpg",
      description: "Kisah inspiratif anak-anak Belitung yang berjuang untuk bersekolah di tengah keterbatasan.",
      stok: 5
    },
    {
      id: 2,
      title: "Bumi Manusia",
      author: "Pramoedya Ananta Toer",
      year: 1980,
      inventory: "INV-ELIB-002",
      image: "img/Book 2.jpg",
      description: "Perjalanan Minke menantang sistem sosial kolonial demi kebebasan berpikir.",
      stok: 5
    },
    {
      id: 3,
      title: "Filosofi Teras",
      author: "Henry Manampiring",
      year: 2018,
      inventory: "INV-ELIB-003",
      image: "img/Book 3.jpg",
      description: "Pendekatan stoisisme untuk hidup lebih tenang dan fokus pada hal yang bisa dikendalikan.",
      stok: 4
    },
    {
      id: 4,
      title: "Dilan 1990",
      author: "Pidi Baiq",
      year: 2014,
      inventory: "INV-ELIB-004",
      image: "img/Book 4.jpg",
      description: "Kisah cinta Milea dan Dilan di masa SMA Bandung tahun 90-an yang penuh nostalgia.",
      stok: 2
    },
    {
      id: 5,
      title: "Atomic Habits",
      author: "James Clear",
      year: 2019,
      inventory: "INV-ELIB-005",
      image: "img/Book 5.jpg",
      description: "Cara membangun kebiasaan kecil yang menghasilkan perubahan besar.",
      stok: 5
    },
    {
      id: 6,
      title: "The Subtle Art of Not Giving a F*ck",
      author: "Mark Manson",
      year: 2016,
      inventory: "INV-ELIB-006",
      image: "img/Book 6.jpg",
      description: "Cara realistis menjalani hidup dengan menerima ketidaksempurnaan.",
      stok: 4
    },
  ];

  // ======= UTILITAS =======
  function getBooks() {
    const stored = JSON.parse(localStorage.getItem("books"));
    let books;

    if (!stored || !Array.isArray(stored) || stored.length === 0) {
      books = defaultBooks;
      localStorage.setItem("books", JSON.stringify(defaultBooks));
    } else {
      books = stored.map((b, i) => ({
        id: b.id || defaultBooks[i]?.id || Date.now() + i,
        title: b.title || defaultBooks[i]?.title || "Tanpa Judul",
        author: b.author || defaultBooks[i]?.author || "Tidak diketahui",
        image: b.image || defaultBooks[i]?.image || "img/default-book.jpg",
        description: b.description || defaultBooks[i]?.description || "",
        year: b.year || defaultBooks[i]?.year || 2000 + i,
        inventory: b.inventory || `INV-ELIB-${String(i + 1).padStart(3, "0")}`,
        stok: parseInt(b.stok) || defaultBooks[i]?.stok || 1,
      }));
      localStorage.setItem("books", JSON.stringify(books));
    }
    return books;
  }

  function saveBooks(data) {
    localStorage.setItem("books", JSON.stringify(data));
  }

  function getRiwayat() {
    return JSON.parse(localStorage.getItem("riwayat")) || [];
  }

  function saveRiwayat(data) {
    localStorage.setItem("riwayat", JSON.stringify(data));
  }

  function getCurrentUser() {
    return JSON.parse(localStorage.getItem("currentUser"));
  }

  // ======= RENDER BUKU =======
  const bookList = document.getElementById("book-list");
  const searchInput = document.getElementById("searchBook");

  function renderBooks(bookArray) {
    bookList.innerHTML = "";

    if (bookArray.length === 0) {
      bookList.innerHTML = `<div class="text-center text-muted py-5">Buku tidak ditemukan 😅</div>`;
      return;
    }

    bookArray.forEach((book) => {
      const col = document.createElement("div");
      col.className = "col-lg-4 col-md-6 col-sm-10";
      col.innerHTML = `
        <div class="card h-100 shadow-sm border-0 hover-shadow" style="transition: all .3s;">
          <img src="${book.image}" class="card-img-top" alt="${book.title}" 
               onerror="this.src='img/default-book.jpg'" style="height: 320px; object-fit: cover;">
          <div class="card-body text-center">
            <h5 class="card-title fw-bold">${book.title}</h5>
            <p class="text-muted mb-2"><i class="bi bi-person"></i> ${book.author}</p>
            <p class="small text-secondary">${book.description.slice(0, 120)}...</p>
            <p class="fw-bold mt-2">Stok: 
              <span class="${book.stok > 0 ? 'text-success' : 'text-danger'}">${book.stok}</span>
            </p>
          </div>
          <div class="card-footer bg-white border-0 text-center pb-3">
            <button class="btn btn-sm btn-outline-primary me-2" onclick="viewBook(${book.id})">
              <i class="bi bi-info-circle"></i> Detail
            </button>
            <button class="btn btn-sm btn-success me-2" onclick="bukaFormPinjam(${book.id})"
                    ${book.stok <= 0 ? "disabled" : ""}>
              ${book.stok <= 0 ? "Stok Habis" : '<i class="bi bi-journal-check"></i> Pinjam'}
            </button>
            <button class="btn btn-sm btn-danger" onclick="addToWishlist(${book.id})">
              <i class="bi bi-heart"></i> Wishlist
            </button>
          </div>
        </div>
      `;
      bookList.appendChild(col);
    });
  }

  renderBooks(getBooks());

  // ======= SEARCH =======
  searchInput.addEventListener("input", function () {
    const keyword = this.value.toLowerCase();
    const books = getBooks();
    const filtered = books.filter(
      (b) =>
        b.title.toLowerCase().includes(keyword) ||
        b.author.toLowerCase().includes(keyword)
    );
    renderBooks(filtered);
  });

  // ======= WISHLIST =======
  window.addToWishlist = function (id) {
    const books = getBooks();
    const book = books.find((b) => b.id === id);
    let wishlist = JSON.parse(localStorage.getItem("wishlist")) || [];

    if (wishlist.some((b) => b.id === id)) {
      alert(`❤️ "${book.title}" sudah ada di wishlist.`);
      return;
    }

    wishlist.push(book);
    localStorage.setItem("wishlist", JSON.stringify(wishlist));
    alert(`✅ "${book.title}" berhasil ditambahkan ke wishlist!`);
  };

  // ======= FORM PEMINJAMAN =======
  window.bukaFormPinjam = function (bookId) {
    const user = getCurrentUser();
    if (!user) {
      alert("⚠️ Silakan login terlebih dahulu sebelum meminjam buku.");
      window.location.href = "auth.html";
      return;
    }

    const books = getBooks();
    const book = books.find((b) => b.id === bookId);
    if (!book) return alert("❌ Buku tidak ditemukan.");
    if (book.stok <= 0) return alert("❌ Buku sedang tidak tersedia.");

    const tanggalPinjam = new Date();
    const tanggalKembaliDefault = new Date();
    tanggalKembaliDefault.setDate(tanggalPinjam.getDate() + 7);

    const idPeminjaman = `ELIB-${Date.now().toString().slice(-6)}`;

    const formHTML = `
      <div class="modal fade" id="formPinjamModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">Form Peminjaman Buku</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form id="formPinjam">
                <div class="mb-2">
                  <label class="form-label">ID Peminjaman</label>
                  <input type="text" class="form-control" value="${idPeminjaman}" readonly>
                </div>
                <div class="mb-2">
                  <label class="form-label">Nama Peminjam</label>
                  <input type="text" class="form-control" id="namaPeminjam" placeholder="Masukkan nama kamu" required>
                </div>
                <div class="mb-2">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" id="emailPeminjam" placeholder="Masukkan email kamu" required>
                </div>
                <div class="mb-2">
                  <label class="form-label">Judul Buku</label>
                  <input type="text" class="form-control" value="${book.title}" readonly>
                </div>
                <div class="mb-2">
                  <label class="form-label">Penulis</label>
                  <input type="text" class="form-control" value="${book.author}" readonly>
                </div>
                <div class="mb-2">
                  <label class="form-label">Tahun Terbit</label>
                  <input type="text" class="form-control" value="${book.year}" readonly>
                </div>
                <div class="mb-2">
                  <label class="form-label">Nomor Inventaris</label>
                  <input type="text" class="form-control" value="${book.inventory}" readonly>
                </div>
                <div class="mb-2">
                  <label class="form-label">Alamat</label>
                  <input type="text" class="form-control" id="alamatPeminjam" placeholder="Alamat lengkap kamu" required>
                </div>
                <div class="mb-2">
                  <label class="form-label">No. Telepon</label>
                  <input type="tel" class="form-control" id="teleponPeminjam" placeholder="Nomor telepon aktif" required>
                </div>
                <div class="mb-2">
                  <label class="form-label">Tanggal Pinjam</label>
                  <input type="date" class="form-control" value="${tanggalPinjam.toISOString().split("T")[0]}" readonly>
                </div>
                <div class="mb-2">
                  <label class="form-label">Tanggal Kembali</label>
                  <input type="date" id="tanggalKembali" class="form-control"
                         value="${tanggalKembaliDefault.toISOString().split('T')[0]}" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Catatan / Keterangan</label>
                  <textarea id="catatan" class="form-control" rows="2" placeholder="Opsional"></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100">Konfirmasi Peminjaman</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    `;

    document.getElementById("formPinjamModal")?.remove();
    document.body.insertAdjacentHTML("beforeend", formHTML);
    const modal = new bootstrap.Modal(document.getElementById("formPinjamModal"));
    modal.show();

    document.getElementById("formPinjam").addEventListener("submit", (e) => {
      e.preventDefault();

      const nama = document.getElementById("namaPeminjam").value.trim();
      const email = document.getElementById("emailPeminjam").value.trim();
      const alamat = document.getElementById("alamatPeminjam").value.trim();
      const telepon = document.getElementById("teleponPeminjam").value.trim();
      const tanggalKembali = document.getElementById("tanggalKembali").value;
      const catatan = document.getElementById("catatan").value.trim();

      if (!nama || !email || !alamat || !telepon) {
        alert("⚠️ Harap isi semua kolom wajib!");
        return;
      }

      // Kurangi stok
      book.stok = Math.max(0, (book.stok || 1) - 1);
      saveBooks(books);

      // Tambah ke riwayat
      const riwayat = getRiwayat();
      riwayat.push({
        idPeminjaman,
        nama,
        email,
        alamat,
        telepon,
        title: book.title,
        author: book.author,
        img: book.image,
        year: book.year,
        inventory: book.inventory,
        tanggalPinjam: tanggalPinjam.toISOString().split("T")[0],
        tanggalKembali,
        catatan,
        status: "Dipinjam",
      });
      saveRiwayat(riwayat);

      modal.hide();
      alert(`✅ Buku "${book.title}" berhasil dipinjam!\nKembalikan sebelum ${tanggalKembali}.`);
      location.reload();
    });
  };

    // ======= DETAIL BUKU =======
  window.viewBook = function (id) {
    const books = getBooks();
    const book = books.find((b) => b.id === id);
    if (!book) return alert("❌ Buku tidak ditemukan.");

    const modalHTML = `
      <div class="modal fade" id="bookModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title">${book.title}</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
              <img src="${book.image}" class="img-fluid rounded mb-3" alt="${book.title}" 
                   style="max-height: 300px; object-fit: cover;">
              <p class="text-muted mb-1"><i class="bi bi-person"></i> ${book.author}</p>
              <p class="text-muted mb-1"><i class="bi bi-calendar"></i> Tahun Terbit: ${book.year}</p>
              <p class="text-muted mb-2"><i class="bi bi-upc-scan"></i> No. Inventaris: ${book.inventory}</p>
              <p class="mt-2">${book.description}</p>
            </div>
            <div class="modal-footer">
              <button class="btn btn-success" onclick="bukaFormPinjam(${book.id})"
                ${book.stok <= 0 ? 'disabled' : ''}>
                ${book.stok <= 0 ? 'Stok Habis' : '<i class="bi bi-journal-check"></i> Pinjam'}
              </button>
              <button class="btn btn-danger" onclick="addToWishlist(${book.id})">
                <i class="bi bi-heart"></i> Wishlist
              </button>
              <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    `;

    // Hapus modal lama kalau ada
    document.getElementById("bookModal")?.remove();

    // Tambahkan ke body
    document.body.insertAdjacentHTML("beforeend", modalHTML);
    new bootstrap.Modal(document.getElementById("bookModal")).show();
  };

});