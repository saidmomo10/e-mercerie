@extends('layouts.app')

@section('content')
<div class="tables-wrapper">
  <div class="row">
    <div class="col-lg-12">
      <div class="card-style mb-30">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="mb-0">Mes Fournitures</h4>
          <a href="{{ route('merchant.supplies.create') }}" class="main-btn primary-btn btn-hover">
            <i class="lni lni-plus"></i> Ajouter une fourniture
          </a>
        </div>

        <div class="table-wrapper table-responsive">
          <table class="table striped-table">
            <thead>
              <tr>
                <th><h6>Fourniture</h6></th>
                <th><h6>Prix</h6></th>
                <th><h6>Quantité</h6></th>
                <th><h6>Actions</h6></th>
              </tr>
            </thead>
            <tbody>
              @forelse($merchantSupplies as $supply)
                <tr>
                  <td><p>{{ $supply->supply->name }}</p></td>
                  <td><p>{{ number_format($supply->price, 0, ',', ' ') }}</p></td>
                  <td><p>{{ $supply->stock_quantity }}</p></td>
                  <td class="d-flex gap-2">
                    <!-- Bouton modifier -->
                    <a href="{{ route('merchant.supplies.edit', $supply->id) }}" 
                       class="text-secondary" title="Modifier">
                      <i class="fa-solid fa-pencil"></i>
                    </a>

                    <!-- Bouton supprimer -->
                    <form action="{{ route('merchant.supplies.destroy', $supply->id) }}" method="POST" class="delete-form d-inline">
                      @csrf
                      @method('DELETE')
                      <div class="actions">
                        <button type="button" class="text-danger btn-delete" title="Supprimer" style="border:none; background:none; padding:0;">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </div>  
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center py-4 text-muted">Aucune fourniture enregistrée</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- SweetAlert2 Confirmation pour suppression --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function () {
      const form = this.closest('form');
      Swal.fire({
        title: 'Supprimer cette fourniture ?',
        text: "Cette action est irréversible.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        customClass: { popup: 'rounded-4 shadow-lg' }
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>

{{-- Modal de complétion de profil --}}
@if(session('showProfileModal'))
<div class="modal fade" id="profileCompletionModal" tabindex="-1" aria-labelledby="profileCompletionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title" id="profileCompletionModalLabel">Complétez votre profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <p>Pour ajouter une fourniture, veuillez d’abord compléter votre profil.</p>
        <form action="{{ route('merceries.profile.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="city" class="form-label">Ville</label>
            <input type="text" name="city" id="city" class="form-control" value="{{ auth()->user()->city }}">
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Téléphone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ auth()->user()->phone }}">
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Adresse</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ auth()->user()->address }}">
          </div>
          <div class="mb-3">
            <label for="avatar" class="form-label">Avatar (facultatif)</label>
            <input type="file" name="avatar" id="avatar" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary w-100">Mettre à jour le profil</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalElement = document.getElementById('profileCompletionModal');
    if(modalElement){
        var profileModal = new bootstrap.Modal(modalElement);
        profileModal.show();
    }
});
</script>
@endif

@endsection
