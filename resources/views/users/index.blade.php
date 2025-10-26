@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">User List</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            + Tambah User
        </button>
    </div>
    <div class="card-body">
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
</div>

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

<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <img src="" alt="qr_img" id="qrCodeImage" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="barModal" tabindex="-1" aria-labelledby="barModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <img src="" alt="bar_img" id="barCodeImage" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('template/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('template/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('template/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('template/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
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
                            <button class="btn btn-sm btn-primary bar-btn" data-id="${data.id}"><i class="fas fa-barcode"></i></button>
                            <button class="btn btn-sm btn-primary qr-btn" data-id="${data.id}"><i class="fas fa-qrcode"></i></button>
                            <button class="btn btn-sm btn-info view-btn" data-id="${data.id}"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-warning edit-btn" data-id="${data.id}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${data.id}"><i class="fas fa-trash"></i></button>
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

        // Generate QR Code
        $('#userTable').on('click', '.qr-btn', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: '{{ route("users.qrcode") }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    $('#qrCodeImage').attr('src', response.qr);
                    $('#qrModal').modal('show');
                },
                error: function() {
                    alert('Gagal menghasilkan QR code!');
                }
            });
        });

        // Generate Barcode Code
        $('#userTable').on('click', '.bar-btn', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: '{{ route("users.barcode") }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                method: 'POST',
                data: { id: id },
                success: function(response) {
                    $('#barCodeImage').attr('src', response.bar);
                    $('#barModal').modal('show');
                },
                error: function() {
                    alert('Gagal menghasilkan Barcode!');
                }
            });
        });
    });
</script>
@endpush