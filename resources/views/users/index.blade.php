<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daftar User</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            + Tambah User
        </button>
    </div>

    <table class="table table-bordered" id="userTable">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createUserForm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @csrf
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
    const table = $('#userTable').DataTable({
        ajax: {
            url: '{{ route("users.data") }}',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            {
                data: null,
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-info view-btn" data-id="${data.id}">Detail</button>
                        <button class="btn btn-sm btn-warning edit-btn" data-id="${data.id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data.id}">Hapus</button>
                    `;
                },
                orderable: false
            }
        ]
    });

    // Create User
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("users.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('#createUserModal').modal('hide');
                table.ajax.reload();
                alert('User berhasil dibuat!');
            },
            error: function() {
                alert('Gagal menambah user!');
            }
        });
    });

    // Delete User
    $('#userTable').on('click', '.delete-btn', function() {
        if (confirm('Yakin ingin menghapus user ini?')) {
            const id = $(this).data('id');
            $.ajax({
                url: '/users/' + id,
                method: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function() {
                    table.ajax.reload();
                    alert('User berhasil dihapus!');
                }
            });
        }
    });

    // Detail User
    $('#userTable').on('click', '.view-btn', function() {
        const id = $(this).data('id');
        alert('Detail user ID: ' + id);
    });

    // Edit User
    $('#userTable').on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        alert('Edit user ID: ' + id);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
