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
            <a href="{{ route('admin.users.createAdmin') }}" class="btn btn-success me-2">
                <i class="bi bi-person-plus me-1"></i>Add Admin
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'active']) }}" class="btn {{ $status == 'active' ? 'btn-primary' : 'btn-outline-primary' }} me-2">
                Active Users
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}" class="btn {{ $status == 'inactive' ? 'btn-secondary' : 'btn-outline-secondary' }}">
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
                        <th>AKSI</th>
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
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- Tombol Reset Password --}}
                                    <a href="{{ route('admin.users.reset-password', $user->id) }}"
                                        class="btn btn-icon btn-info btn-sm"
                                        title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </a>

                                    @if($user->is_active)
                                        {{-- Tombol Nonaktifkan (menggantikan Hapus) --}}
                                        <button type="button"
                                            class="btn btn-icon btn-warning btn-sm deactivate-btn"
                                            data-action="{{ route('admin.users.deactivate', $user->id) }}"
                                            title="Nonaktifkan User">
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                    @else
                                        {{-- Tombol Aktifkan --}}
                                        <form method="POST" action="{{ route('admin.users.activate', $user->id) }}" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-icon btn-success btn-sm" title="Aktifkan User">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                        </form>
                                        {{-- Tombol Hapus (Muncul saat user inactive) --}}
                                        <button type="button"
                                            class="btn btn-icon btn-danger btn-sm delete-btn"
                                            data-action="{{ route('admin.users.destroy', $user->id) }}"
                                            title="Hapus User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL CONFIRM DEACTIVATE --}}
<div class="modal fade" id="confirmDeactivateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Konfirmasi Nonaktifkan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-0">Yakin ingin menonaktifkan user ini? User tidak akan bisa login kembali.</p>
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-sm btn-warning" id="confirmDeactivateBtn">
                    Ya, Nonaktifkan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CONFIRM DELETE --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Konfirmasi Hapus
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-0">Yakin ingin menghapus user ini secara permanen?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-sm btn-danger" id="confirmDeleteBtn">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let deactivateAction = null;

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.deactivate-btn');
    if (!btn) return;

    deactivateAction = btn.getAttribute('data-action');

    const modal = new bootstrap.Modal(
        document.getElementById('confirmDeactivateModal')
    );
    modal.show();
});

document.getElementById('confirmDeactivateBtn')
    .addEventListener('click', function () {

        if (!deactivateAction) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deactivateAction;

        form.innerHTML = `
            @csrf
            @method('PATCH')
        `;

        document.body.appendChild(form);
        form.submit();
});

// Script untuk Delete
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

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (!deleteAction) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = deleteAction;
    form.innerHTML = `@csrf @method('DELETE')`;
    document.body.appendChild(form);
    form.submit();
});
</script>
@endpush
@endsection