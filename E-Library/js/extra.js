// ============================
// DATA AWAL BUKU
// ============================
const books = [
  {
    id: 1,
    title: "Belajar Web Design Modern",
    author: "Ahmad Rizky",
    img: "img/book1.jpg",
    desc: "Panduan lengkap memahami konsep dan praktik desain web modern menggunakan HTML5, CSS3, dan framework Bootstrap. Buku ini cocok untuk pemula yang ingin membangun situs web profesional dari nol."
  },
  {
    id: 2,
    title: "Mahir JavaScript untuk Pemula",
    author: "Sinta Wulandari",
    img: "img/book2.jpg",
    desc: "Pelajari dasar hingga konsep lanjutan JavaScript, bahasa pemrograman yang menjadi fondasi utama web interaktif. Disertai contoh studi kasus nyata untuk meningkatkan kemampuan logika pemrograman."
  },
  {
    id: 3,
    title: "Panduan WordPress Mastery",
    author: "Dwi Santoso",
    img: "img/book3.jpg",
    desc: "Buku ini mengupas tuntas cara membuat website menggunakan WordPress, mulai dari instalasi, pengaturan tema, hingga optimasi SEO dan keamanan situs."
  },
  {
    id: 4,
    title: "Langkah Jadi Full Stack Developer",
    author: "Putra Kurniawan",
    img: "img/book4.jpg",
    desc: "Panduan komprehensif untuk menjadi full stack developer profesional, membahas frontend, backend, dan integrasi database menggunakan Node.js dan MongoDB."
  },
  {
    id: 5,
    title: "Dasar-Dasar Data Analyst",
    author: "Yuliana Saputra",
    img: "img/book5.jpg",
    desc: "Pahami konsep dasar analisis data, visualisasi, dan pengolahan dataset menggunakan Python dan Excel. Disertai latihan nyata untuk pemula hingga menengah."
  },
  {
    id: 6,
    title: "Tren Web Development 2025",
    author: "Fajar Pratama",
    img: "img/book6.jpg",
    desc: "Temukan teknologi dan tren terbaru dalam pengembangan web tahun 2025. Buku ini memberikan insight penting tentang AI, framework modern, dan praktik coding efisien."
  }
];

// ============================
// LOCAL STORAGE HELPERS
// ============================
function getWishlist() {
  return JSON.parse(localStorage.getItem("wishlist")) || [];
}
function getRiwayat() {
  return JSON.parse(localStorage.getItem("riwayat")) || [];
}
function saveWishlist(data) {
  localStorage.setItem("wishlist", JSON.stringify(data));
  updateAdminData();
}
function saveRiwayat(data) {
  localStorage.setItem("riwayat", JSON.stringify(data));
  updateAdminData();
}

// ============================
// TAMPILKAN KOLEKSI BUKU
// ============================
function loadBooks() {
  const container = document.getElementById("book-list");
  if (!container) return;
  container.innerHTML = "";

  books.forEach(book => {
    const col = document.createElement("div");
    col.className = "col-lg-4 col-md-6 wow fadeInUp";
    col.innerHTML = `
      <div class="course-item bg-light text-center">
        <div class="position-relative overflow-hidden">
          <img class="img-fluid" src="${book.img}" alt="${book.title}">
        </div>
        <div class="text-center p-4">
          <h5 class="mb-2">${book.title}</h5>
          <p class="text-muted">by ${book.author}</p>
          <p>${book.desc}</p>
          <div id="qrcode-${book.id}" class="d-flex justify-content-center mb-3"></div>
          <button class="btn btn-primary m-1" onclick="pinjamBuku(${book.id})">Pinjam</button>
          <button class="btn btn-outline-primary m-1" onclick="addWishlist(${book.id})">Wishlist</button>
        </div>
      </div>
    `;
    container.appendChild(col);

    // Buat QR code untuk setiap buku
    new QRCode(document.getElementById(`qrcode-${book.id}`), {
      text: `https://elibrary.example.com/book/${book.id}`,
      width: 80,
      height: 80
    });
  });
}

// ============================
// WISHLIST
// ============================
function addWishlist(id) {
  const wishlist = getWishlist();
  const book = books.find(b => b.id === id);
  if (!book) return;

  if (wishlist.some(b => b.id === id)) {
    alert("Buku sudah ada di wishlist!");
    return;
  }

  wishlist.push(book);
  saveWishlist(wishlist);
  alert(`${book.title} telah ditambahkan ke wishlist!`);
}

function loadWishlist() {
  const container = document.getElementById("wishlist-list");
  if (!container) return;
  const wishlist = getWishlist();

  if (wishlist.length === 0) {
    container.innerHTML = `<p class="text-center text-muted">Belum ada buku di wishlist.</p>`;
    return;
  }

  container.innerHTML = wishlist.map(book => `
    <div class="col-lg-4 col-md-6">
      <div class="course-item bg-light text-center">
        <img class="img-fluid" src="${book.img}" alt="${book.title}">
        <h5 class="mt-3">${book.title}</h5>
        <p>${book.author}</p>
        <p class="text-success">Wishlist</p>
        <button class="btn btn-danger btn-sm mb-3" onclick="hapusWishlist(${book.id})">
          <i class="fa fa-trash"></i> Hapus
        </button>
      </div>
    </div>
  `).join("");
}

function hapusWishlist(id) {
  let wishlist = getWishlist().filter(b => b.id !== id);
  saveWishlist(wishlist);
  loadWishlist();
}

// ============================
// RIWAYAT PEMINJAMAN (FIXED SYNC)
// ============================
function pinjamBuku(id) {
  const riwayat = getRiwayat();
  const book = books.find(b => b.id === id);
  if (!book) return;

  const today = new Date();
  const tanggalPinjam = today.toISOString(); // simpan dalam format ISO agar bisa dihitung
  const newEntry = {
    ...book,
    tanggalPinjam,
    status: "Dipinjam"
  };

  riwayat.push(newEntry);
  saveRiwayat(riwayat);

  alert(`Kamu meminjam: ${book.title}`);
}

function loadRiwayat() {
  const container = document.getElementById("riwayat-list");
  if (!container) return;

  const riwayat = getRiwayat();
  if (riwayat.length === 0) {
    container.innerHTML = `<p class="text-center text-muted">Belum ada riwayat peminjaman.</p>`;
    return;
  }

  container.innerHTML = riwayat.map((book, index) => {
    const pinjamDate = new Date(book.tanggalPinjam);
    const now = new Date();
    const diffDays = Math.floor((now - pinjamDate) / (1000 * 60 * 60 * 24));
    let status = book.status;
    let denda = 0;

    if (status === "Dipinjam" && diffDays > 7) {
      denda = (diffDays - 7) * 1000;
      status = `Terlambat ${diffDays - 7} hari (Denda: Rp${denda.toLocaleString()})`;
    }

    return `
      <div class="col-lg-4 col-md-6">
        <div class="course-item bg-light text-center">
          <img class="img-fluid" src="${book.img}" alt="${book.title}">
          <h5 class="mt-3">${book.title}</h5>
          <p>${book.author}</p>
          <p class="text-muted">${status}</p>
          <p class="small text-secondary">Dipinjam: ${pinjamDate.toLocaleDateString("id-ID")}</p>
          <div class="d-flex justify-content-center mb-3">
            <button class="btn btn-success btn-sm me-2" onclick="kembalikanBuku(${index})">
              <i class="fa fa-undo"></i> Kembalikan
            </button>
            <button class="btn btn-danger btn-sm" onclick="hapusRiwayat(${index})">
              <i class="fa fa-trash"></i> Hapus
            </button>
          </div>
        </div>
      </div>
    `;
  }).join("");
}

function kembalikanBuku(index) {
  const riwayat = getRiwayat();
  if (!riwayat[index]) return;

  const pinjamDate = new Date(riwayat[index].tanggalPinjam);
  const today = new Date();
  const diffDays = Math.floor((today - pinjamDate) / (1000 * 60 * 60 * 24));
  const denda = diffDays > 7 ? (diffDays - 7) * 1000 : 0;

  riwayat[index].status = `Dikembalikan (${denda > 0 ? "Denda Rp" + denda.toLocaleString() : "Tepat waktu"})`;
  saveRiwayat(riwayat);
  loadRiwayat();
}

function hapusRiwayat(index) {
  const riwayat = getRiwayat();
  riwayat.splice(index, 1);
  saveRiwayat(riwayat);
  loadRiwayat();
}

// ============================
// ADMIN DASHBOARD AUTO UPDATE
// ============================
function updateAdminData() {
  const totalBooks = books.length;
  const wishlist = getWishlist();
  const riwayat = getRiwayat();

  document.getElementById("statTotalBuku")?.innerText = totalBooks;
  document.getElementById("statWishlist")?.innerText = wishlist.length;
  document.getElementById("statDipinjam")?.innerText = riwayat.length;
}

// ============================
// INISIALISASI
// ============================
document.addEventListener("DOMContentLoaded", () => {
  loadBooks();
  loadWishlist();
  loadRiwayat();
  updateAdminData();
});

// ====== STATUS LOGIN USER (Global di semua halaman) ======
document.addEventListener("DOMContentLoaded", function () {
  const currentUser = JSON.parse(localStorage.getItem("currentUser"));
  const loginBtn = document.querySelector(".btn.btn-primary, .btn.btn-danger");

  if (!loginBtn) return;

  // Jika user sudah login
  if (currentUser && currentUser.email) {
    loginBtn.textContent = "Logout";
    loginBtn.classList.add("btn-danger");
    loginBtn.classList.remove("btn-primary");
    loginBtn.innerHTML += `<i class="fa fa-sign-out-alt ms-2"></i>`;

    loginBtn.addEventListener("click", function (e) {
      e.preventDefault();
      if (confirm("Yakin ingin logout?")) {
        localStorage.removeItem("currentUser");
        alert("👋 Kamu telah logout!");
        window.location.href = "index.html";
      }
    });
  } 
  // Jika belum login
  else {
    loginBtn.textContent = "Login / Register";
    loginBtn.classList.add("btn-primary");
    loginBtn.classList.remove("btn-danger");
    loginBtn.innerHTML += `<i class="fa fa-arrow-right ms-2"></i>`;

    loginBtn.addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href = "auth.html";
    });
  }
});
