<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Category - REST API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🏷️ Daftar Kategori</h2>
        <div>
            <a href="/products-ui" class="btn btn-outline-secondary me-2">Kelola Produk</a>
            <button class="btn btn-primary" onclick="openCreateModal()">+ Tambah Kategori</button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="category-list">
                    <tr><td colspan="3" class="text-center">Loading data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="categoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="categoryForm">
            <input type="hidden" id="categoryId">
            <div class="mb-3">
                <label class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="categoryName" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Simpan Data</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const API_URL = '/api/categories';
    const modalEl = new bootstrap.Modal(document.getElementById('categoryModal'));

    async function getCategories() {
        try {
            const response = await fetch(API_URL);
            const resData = await response.json();
            
            let html = '';
            if (resData.data.length === 0) {
                html = `<tr><td colspan="3" class="text-center text-muted">Belum ada data kategori.</td></tr>`;
            } else {
                resData.data.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.id}</td>
                            <td>${item.name}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning me-1" onclick="openEditModal(${item.id}, '${item.name}')">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory(${item.id})">Hapus</button>
                            </td>
                        </tr>
                    `;
                });
            }
            document.getElementById('category-list').innerHTML = html;
        } catch (error) {
            document.getElementById('category-list').innerHTML = `<tr><td colspan="3" class="text-center text-danger">Gagal memuat data dari API.</td></tr>`;
        }
    }

    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Kategori';
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryForm').reset();
        modalEl.show();
    }

    function openEditModal(id, name) {
        document.getElementById('modalTitle').innerText = 'Edit Kategori';
        document.getElementById('categoryId').value = id;
        document.getElementById('categoryName').value = name;
        modalEl.show();
    }

    document.getElementById('categoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('categoryId').value;
        const name = document.getElementById('categoryName').value;

        const isEdit = id !== '';
        const url = isEdit ? `${API_URL}/${id}` : API_URL;
        const method = isEdit ? 'PUT' : 'POST';

        await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name })
        });

        modalEl.hide();
        getCategories();
    });

    async function deleteCategory(id) {
        if (confirm('Yakin ingin menghapus kategori ini?')) {
            await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            });
            getCategories();
        }
    }

    getCategories();
</script>
</body>
</html>