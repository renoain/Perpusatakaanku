<?php
require_once 'config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

// Ambil semua buku
$books = $conn->query("SELECT id, title, stok FROM buku ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Borrow Function</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f8f9fa; }
        .test-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .debug-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 15px 0; font-family: monospace; white-space: pre-wrap; }
        .btn-test { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="test-container">
        <h2 class="mb-4"><i class="fa fa-flask me-2"></i>Test Peminjaman Buku</h2>
        
        <div class="alert alert-info">
            <i class="fa fa-info-circle me-2"></i>
            <strong>Info:</strong> Halaman ini untuk testing fungsi peminjaman buku. Pilih buku dan isi form, lalu klik tombol test.
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Daftar Buku Tersedia</h5>
            </div>
            <div class="card-body">
                <select id="bookSelect" class="form-select mb-3">
                    <option value="">-- Pilih Buku --</option>
                    <?php foreach ($books as $book): ?>
                        <option value="<?= $book['id'] ?>" data-stock="<?= $book['stok'] ?>">
                            <?= $book['title'] ?> (Stok: <?= $book['stok'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <div id="selectedBookInfo" class="info" style="display: none;">
                    <strong>Buku Terpilih:</strong>
                    <div id="bookInfo"></div>
                </div>
            </div>
        </div>

        <h4 class="mt-4 mb-3">Form Test Peminjaman</h4>
        
        <form id="testBorrowForm">
            <input type="hidden" id="bookId" name="book_id">
            
            <div class="mb-3">
                <label class="form-label"><i class="fa fa-book me-2"></i>Book ID:</label>
                <input type="text" id="bookIdDisplay" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fa fa-calendar-alt me-2"></i>Tanggal Pinjam:</label>
                <input type="date" id="borrowDate" name="borrow_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fa fa-calendar-check me-2"></i>Tanggal Kembali:</label>
                <input type="date" id="returnDate" name="return_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fa fa-comment me-2"></i>Catatan:</label>
                <textarea id="note" name="note" class="form-control" rows="3" placeholder="Test peminjaman"></textarea>
            </div>

            <button type="button" class="btn btn-primary btn-test" onclick="testBorrow()">
                <i class="fa fa-rocket me-2"></i>Test Borrow
            </button>
            <button type="button" class="btn btn-secondary btn-test" onclick="clearResult()">
                <i class="fa fa-eraser me-2"></i>Clear Result
            </button>
        </form>

        <div id="result" style="margin-top: 30px;"></div>
        
        <div id="debugInfo" class="debug-box" style="display: none;">
            <strong>Debug Information:</strong>
            <div id="debugContent"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set tanggal hari ini
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('borrowDate').value = today;
        document.getElementById('borrowDate').min = today;
        
        // Set tanggal kembali 7 hari dari sekarang
        const returnDate = new Date();
        returnDate.setDate(returnDate.getDate() + 7);
        document.getElementById('returnDate').value = returnDate.toISOString().split('T')[0];
        document.getElementById('returnDate').min = today;
        
        // Set max tanggal kembali 14 hari dari sekarang
        const maxReturn = new Date();
        maxReturn.setDate(maxReturn.getDate() + 14);
        document.getElementById('returnDate').max = maxReturn.toISOString().split('T')[0];

        // Handle book selection
        document.getElementById('bookSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const bookId = this.value;
            const stock = selectedOption.dataset.stock;
            const title = selectedOption.text;
            
            if (bookId) {
                document.getElementById('bookId').value = bookId;
                document.getElementById('bookIdDisplay').value = bookId;
                document.getElementById('selectedBookInfo').style.display = 'block';
                document.getElementById('bookInfo').innerHTML = `
                    <div class="mt-2">
                        <strong>ID:</strong> ${bookId}<br>
                        <strong>Judul:</strong> ${title}<br>
                        <strong>Stok:</strong> ${stock}
                    </div>
                `;
            } else {
                document.getElementById('bookId').value = '';
                document.getElementById('bookIdDisplay').value = '';
                document.getElementById('selectedBookInfo').style.display = 'none';
            }
        });

        async function testBorrow() {
            const resultDiv = document.getElementById('result');
            const debugDiv = document.getElementById('debugInfo');
            const debugContent = document.getElementById('debugContent');
            
            resultDiv.innerHTML = '<div class="info">⏳ Sedang mengirim request...</div>';
            debugDiv.style.display = 'block';
            debugContent.innerHTML = '⏳ Processing...';

            const bookId = document.getElementById('bookId').value;
            const borrowDate = document.getElementById('borrowDate').value;
            const returnDate = document.getElementById('returnDate').value;
            const note = document.getElementById('note').value;

            const debugInfo = {
                timestamp: new Date().toISOString(),
                bookId: bookId,
                bookIdType: typeof bookId,
                borrowDate: borrowDate,
                returnDate: returnDate,
                note: note
            };

            debugContent.innerHTML = 'Request Data:\n' + JSON.stringify(debugInfo, null, 2);

            // Validasi
            if (!bookId || bookId === '0') {
                resultDiv.innerHTML = '<div class="error">❌ Validation Error<br>Silakan pilih buku terlebih dahulu!</div>';
                return;
            }

            if (!borrowDate || !returnDate) {
                resultDiv.innerHTML = '<div class="error">❌ Validation Error<br>Tanggal peminjaman dan pengembalian harus diisi!</div>';
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'borrow');
                formData.append('book_id', String(bookId));
                formData.append('borrow_date', borrowDate);
                formData.append('return_date', returnDate);
                formData.append('note', note);

                debugContent.innerHTML += '\n\nFormData Contents:\n';
                for (let pair of formData.entries()) {
                    debugContent.innerHTML += `${pair[0]}: ${pair[1]} (${typeof pair[1]})\n`;
                }

                const response = await fetch('api_books.php', {
                    method: 'POST',
                    body: formData
                });

                debugContent.innerHTML += `\n\nResponse Status: ${response.status}\n`;
                debugContent.innerHTML += `Response OK: ${response.ok}\n`;

                const responseText = await response.text();
                debugContent.innerHTML += `\nRaw Response:\n${responseText}\n`;

                let result;
                try {
                    result = JSON.parse(responseText);
                    debugContent.innerHTML += `\nParsed JSON:\n${JSON.stringify(result, null, 2)}`;
                } catch (e) {
                    resultDiv.innerHTML = `<div class="error">❌ JSON Parse Error<br>${e.message}<br><br>Response was:<br>${responseText}</div>`;
                    return;
                }

                if (result.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            ✅ <strong>Success!</strong><br>
                            ${result.message}<br><br>
                            <strong>Details:</strong><br>
                            Borrow ID: ${result.borrow_id || 'N/A'}<br>
                            Book ID: ${result.book_id || 'N/A'}<br>
                            Book Title: ${result.book_title || 'N/A'}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            ❌ <strong>Error!</strong><br>
                            ${result.message}
                        </div>
                    `;
                }

            } catch (error) {
                resultDiv.innerHTML = `<div class="error">❌ Exception<br>${error.message}</div>`;
                debugContent.innerHTML += `\n\nException:\n${error.stack}`;
            }
        }

        function clearResult() {
            document.getElementById('result').innerHTML = '';
            document.getElementById('debugInfo').style.display = 'none';
            document.getElementById('debugContent').innerHTML = '';
        }
    </script>
</body>
</html>