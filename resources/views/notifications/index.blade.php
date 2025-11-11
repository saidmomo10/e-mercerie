@extends('layouts.app')

@section('content')
<style>
  /* === Styles Modernisés === */
  

  .btn-custom {
      background-color: #6a0b52;
      color: #fff;
      border: none;
      transition: all 0.3s ease;
  }

  .btn-custom:hover {
      background-color: #8b166a;
      transform: translateY(-1px);
  }

  .btn-outline-danger {
      border: 1px solid #dc3545;
      color: #dc3545;
      transition: all 0.3s ease;
  }

  .btn-outline-danger:hover {
      background-color: #dc3545;
      color: #fff;
  }

  .card-style {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
      padding: 1.5rem;
  }

  .single-notification {
      border-bottom: 1px solid #eee;
      padding: 1rem 0;
      transition: all 0.2s ease-in-out;
  }

  .single-notification:last-child {
      border-bottom: none;
  }

  .single-notification:hover {
      background: #fafafa;
      border-radius: 10px;
  }

  .single-notification.readed {
      opacity: 0.8;
  }

  .notification h6 {
      margin-bottom: 0.25rem;
      font-weight: 600;
      color: #333;
  }

  .notification .text-sm {
      font-size: 0.9rem;
      color: #777;
  }

  .notification small {
      font-size: 0.85rem;
  }

  .notification .badge.bg-primary {
      background-color: #6a0b52 !important;
  }

  .action button,
  .action a {
      border-radius: 8px;
      transition: all 0.2s ease;
  }

  .action a.read {
      background-color: #f8f5fa;
      color: #6a0b52;
      border: 1px solid #e5d1e9;
      padding: 6px 8px;
  }

  .action a.read:hover {
      background-color: #6a0b52;
      color: #fff;
  }

  .pagination {
      margin-top: 1.5rem;
  }

  .alert-info {
      background-color: #f9f5fb;
      color: #4F0341;
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  }

  /* ===== Pagination Stylisée ===== */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    gap: 0.5rem;
}

.pagination li {
    border-radius: 8px;
    overflow: hidden;
}

.pagination li a,
.pagination li span {
    display: block;
    padding: 0.5rem 0.9rem;
    color: #6a0b52;
    background-color: #f8f5fa;
    border: 1px solid #e5d1e9;
    transition: all 0.3s ease;
    text-decoration: none;
    font-weight: 500;
}

.pagination li a:hover {
    background-color: #6a0b52;
    color: #fff;
    transform: translateY(-1px);
}

.pagination .active span {
    background-color: #6a0b52;
    color: #fff;
    border-color: #6a0b52;
}

.pagination .disabled span {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<div class="container-fluid">
  <!-- === Titre principal === -->
  <div class="page-title">
    <h1><i class="fa-solid fa-bell me-2"></i> Mes Notifications</h2>
  </div>

    <form action="{{ route('notifications.clearAll') }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="button" id="clear-all-btn" class="btn btn-outline-danger mb-4">
        <i class="fa-solid fa-trash"></i> Tout supprimer
        </button>
    </form>

  <!-- === Liste des notifications === -->
  <div class="card-style">
    @forelse($notifications as $notification)
      <div class="single-notification {{ $notification->read_at ? 'readed' : '' }}">
        <div class="notification d-flex align-items-start justify-content-between">
          <div class="content flex-grow-1">
            <a href="{{ $notification->data['url'] ?? '#' }}" class="text-decoration-none text-dark read">
              <h6>{{ $notification->data['message'] ?? 'Notification' }}</h6>
              @if(isset($notification->data['subtitle']))
                <p class="text-sm">{{ $notification->data['subtitle'] }}</p>
              @endif
            </a>
            <small class="text-muted">
              {{ $notification->created_at->format('d/m/Y H:i') }}
              @if(!$notification->read_at)
                <span class="badge bg-primary ms-2">Nouveau</span>
              @endif
            </small>
          </div>
          <div class="action ms-3 d-flex gap-2">
            @if(!$notification->read_at)
              <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary" title="Marquer comme lu">
                  <i class="fa-solid fa-check"></i>
                </button>
              </form>
            @endif
            @if(isset($notification->data['url']))
              <a href="{{ $notification->data['url'] . '?notif=' . $notification->id }}" 
                 class="btn btn-sm read" title="Voir">
                <i class="fa-solid fa-eye"></i>
              </a>
            @endif
            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="alert alert-info text-center py-4">
        <i class="fa-solid fa-bell-slash fa-3x mb-3"></i>
        <h4>Aucune notification</h4>
        <p class="mb-0">Vous n'avez aucune notification pour le moment.</p>
      </div>
    @endforelse
  </div>

  <!-- Pagination -->
  <div class="d-flex justify-content-center mt-4">
    {{ $notifications->links() }}
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert2 - Suppression globale
    const clearAllBtn = document.getElementById('clear-all-btn');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Cette action supprimera toutes vos notifications.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4F0341',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then((result) => {
                if (result.isConfirmed) {
                    clearAllBtn.closest('form').submit();
                }
            });
        });
    }

    // Marquer comme lu via clic
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    document.querySelectorAll('.btn.read').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '#';
            const url = new URL(href, window.location.origin);
            const notifId = url.searchParams.get('notif');
            if (notifId) {
                e.preventDefault();
                fetch(`/notifications/${notifId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                }).then(() => window.location.href = href)
                .catch(() => window.location.href = href);
            }
        });
    });
});
</script>
@endpush
