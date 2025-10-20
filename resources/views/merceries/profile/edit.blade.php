@extends('layouts.app')

@section('title', 'Mon profil - Mercerie')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 text-center">
                    <h4 class="fw-bold mb-0">Mon profil mercerie</h4>
                    <p class="text-muted small mb-0">Mettez à jour vos informations personnelles</p>
                </div>

                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif

                    <form action="{{ route('merceries.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Avatar --}}
                        <div class="mb-4 text-center">
                            <div class="position-relative d-inline-block">
                                <img id="avatarPreview" src="{{ $mercerie->avatar ? asset('storage/' . $mercerie->avatar) : asset('images/defaults/mercerie-avatar.png') }}"
                                     alt="Avatar"
                                     class="rounded-circle border shadow-sm"
                                     width="120"
                                     height="120"
                                     style="object-fit: cover;">

                                <label for="avatar" 
                                       class="position-absolute bottom-0 end-0"
                                       style="cursor:pointer;
                                       background-color: blue;
                                       color: white;
                                       border-radius: 50%;
                                       width: 35px;
                                       height: 35px;
                                       display: flex;
                                       align-items: center;
                                       justify-content: center;"
                                       title="Changer l'avatar">
                                    <i class="fa-solid fa-camera"></i>
                                </label>
                                <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                            </div>
                            @error('avatar')
                                <p class="text-danger small mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ville --}}
                        <div class="mb-3">
                            <label for="city" class="form-label fw-semibold">Ville</label>
                            <input type="text" id="city" name="city" 
                                   class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $mercerie->city ?? '') }}" 
                                   placeholder="Entrez votre ville" required>
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Téléphone --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">Numéro de téléphone</label>
                            <input type="text" id="phone" name="phone" 
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $mercerie->phone ?? '') }}" 
                                   placeholder="Ex: +229 90 00 00 00" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Adresse --}}
                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">Adresse</label>
                            <textarea id="address" name="address" 
                                      class="form-control @error('address') is-invalid @enderror"
                                      rows="3" placeholder="Entrez votre adresse complète" required>{{ old('address', $mercerie->address ?? '') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Preview JS --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('avatar');
    const preview = document.getElementById('avatarPreview');

    input.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (ev) {
            preview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush

@endsection
