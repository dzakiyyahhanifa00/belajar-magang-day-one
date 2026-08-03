<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Product - REST API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📦 Daftar Produk</h2>
        <div>
            <a href="/categories-ui" class="btn btn-outline-secondary me-2">Kelola Kategori</a>
            <button class="btn btn-primary" onclick="openCreateModal()">+ Tambah Produk</button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Kategori</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="product-list">
                    <tr><td colspan="6" class="text-center">Loading data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Tambah / Edit -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Tambah Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="productForm">
            <input type="hidden" id="productId">
            
            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select class="form-select" id="productCategory" required>
                    <option value="">-- Pilih Kategori --</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input type="text" class="form-control" id="productName" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" class="form-control" id="productPrice" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Stok</label>
                <input type="number" class="form-control" id="productStock" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" id="productDescription" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100">Simpan Data</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const API_URL = '/api/products';
    const API_CATEGORY_URL = '/api/categories';
    const modalEl = new bootstrap.Modal(document.getElementById('productModal'));

    // 1. Ambil opsi Kategori untuk isi Dropdown
    async function loadCategories() {
        try {
            const response = await fetch(API_CATEGORY_URL);
            const resData = await response.json();
            let options = '<option value="">-- Pilih Kategori --</option>';
            
            const categories = resData.data || resData; // Handle format resource atau array biasa
            categories.forEach(cat => {
                options += `<option value="${cat.id}">${cat.name}</option>`;
            });
            document.getElementById('productCategory').innerHTML = options;
        } catch (e) {
            console.error('Gagal memuat kategori:', e);
        }
    }

    // 2. Tampilkan Semua Produk (READ)
    async function getProducts() {
        try {
            const response = await fetch(API_URL);
            const resData = await response.json();
            
            let html = '';
            const products = resData.data || resData;

            if (products.length === 0) {
                html = `<tr><td colspan="6" class="text-center text-muted">Belum ada data produk.</td></tr>`;
            } else {
                products.forEach(item => {
                    const categoryName = item.category ? item.category.name : '-';
                    html += `
                        <tr>
                            <td>${item.id}</td>
                            <td><span class="badge bg-secondary">${categoryName}</span></td>
                            <td>${item.name}</td>
                            <td>Rp ${Number(item.price).toLocaleString('id-ID')}</td>
                            <td>${item.stock ?? 0}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning me-1" onclick='openEditModal(${JSON.stringify(item)})'>Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteProduct(${item.id})">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            }
            document.getElementById('product-list').innerHTML = html;
        } catch (error) {
            document.getElementById('product-list').innerHTML = `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data dari API. Pastikan server Laravel aktif!</td></tr>`;
        }
    }

    // 3. Modal Tambah Data
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Produk';
        document.getElementById('productId').value = '';
        document.getElementById('productForm').reset();
        modalEl.show();
    }

    // 4. Modal Edit Data
    function openEditModal(item) {
        document.getElementById('modalTitle').innerText = 'Edit Produk';
        document.getElementById('productId').value = item.id;
        document.getElementById('productCategory').value = item.category_id || '';
        document.getElementById('productName').value = item.name;
        document.getElementById('productPrice').value = item.price;
        document.getElementById('productStock').value = item.stock || 0;
        document.getElementById('productDescription').value = item.description || '';
        modalEl.show();
    }

    // 5. Submit Form (Simpan / Update)
    document.getElementById('productForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('productId').value;
        
        const payload = {
            category_id: document.getElementById('productCategory').value,
            name: document.getElementById('productName').value,
            price: document.getElementById('productPrice').value,
            stock: document.getElementById('productStock').value,
            description: document.getElementById('productDescription').value
        };

        const isEdit = id !== '';
        const url = isEdit ? `${API_URL}/${id}` : API_URL;
        const method = isEdit ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            modalEl.hide();
            getProducts();
        } else {
            const errData = await res.json();
            alert('Gagal menyimpan: ' + (errData.message || JSON.stringify(errData.errors)));
        }
    });

    // 6. Hapus Produk
    async function deleteProduct(id) {
        if (confirm('Yakin ingin menghapus produk ini?')) {
            await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            });
            getProducts();
        }
    }

    // Inisialisasi
    loadCategories();
    getProducts();
</script>
</body>
</html>