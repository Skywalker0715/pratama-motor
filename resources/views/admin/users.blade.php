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
            <i class="fas fa-users me-2"></i>Data User
        </h5>
    </div>
    
    <div class="card-body p-0">
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
                                        <i class="fas fa-user-shield me-1"></i>Admin
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-user me-1"></i>{{ ucfirst($user->role) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                </span>
                            </td>
                            <td class="text-center">
                            <form class="delete-form">
                             <button type="button"
                             class="btn btn-icon btn-danger delete-btn"
                             data-action="{{ route('admin.users.destroy', $user->id) }}"
                             title="Hapus User">
                            <i class="fas fa-trash-alt"></i>
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
                <h6 class="modal-title text-white">
                    <i class="fas fa-trash-alt"></i> Konfirmasi Hapus
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-0">Yakin ingin menghapus user ini?</p>
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-sm btn-danger" id="confirmDeleteBtn">
                    Hapus
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