@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark">Data User</h2>
    <p class="text-muted mb-0">Manajemen pengguna sistem admin dan kasir</p>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-people me-2"></i>Data User
        </h5>
    </div>

    <div class="card-body p-0">
        <div class="mb-3 px-3 pt-3">
            <a href="{{ route('admin.users', ['status' => 'active']) }}" class="btn {{ $status == 'active' ? 'btn-primary' : 'btn-outline-primary' }} me-2">
                Active Users
            </a>
            <a href="{{ route('admin.users', ['status' => 'inactive']) }}" class="btn {{ $status == 'inactive' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                Inactive Users
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle small mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th width="50">NO</th>
                        <th>NAMA</th>
                        <th>EMAIL</th>
                        <th width="120">ROLE</th>
                        <th width="100">STATUS</th>
                        <th width="100">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        <tr>
                        <td class="text-center">
                               {{ $users->firstItem() + $index }}
                           </td>
                            <td class="text-center">{{ $user->name }}</td>
                            <td class="text-center">{{ $user->email }}</td>
                            <td class="text-center">
                                @if($user->role === 'admin')
                                    <span class="badge bg-primary">
                                        <i class="bi bi-shield-check me-1"></i>Admin
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-person me-1"></i>{{ ucfirst($user->role) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->is_active)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!$user->is_active)
                                    <form method="POST" action="{{ route('admin.users.activate', $user->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-icon btn-success"
                                            title="Aktifkan User">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form class="delete-form" style="display: inline;">
                                     <button type="button"
                                         class="btn btn-icon btn-danger delete-btn"
                                         data-action="{{ route('admin.users.destroy', $user->id) }}"
                                         title="Hapus User">
                                         <i class="bi bi-trash"></i>
                                     </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL CONFIRM DELETE --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Konfirmasi Hapus Permanen
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-0">Yakin ingin menonaktifkan user ini? User tidak akan bisa login kembali, namun semua data historis akan tetap utuh.</p>
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-sm btn-danger" id="confirmDeleteBtn">
                    Ya, Hapus Permanen
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let deleteAction = null;

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-btn');
    if (!btn) return;

    deleteAction = btn.getAttribute('data-action');

    const modal = new bootstrap.Modal(
        document.getElementById('confirmDeleteModal')
    );
    modal.show();
});

document.getElementById('confirmDeleteBtn')
    .addEventListener('click', function () {

        if (!deleteAction) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteAction;

        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;

        document.body.appendChild(form);
        form.submit();
});
</script>
@endpush
@endsection