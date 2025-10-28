@extends('layouts.app')

@section('content')
<div class="notification-wrapper">
        <div class="container-fluid">
            <!-- ========== title-wrapper start ========== -->
            <div class="title-wrapper pt-30 mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="title">
                            <h2>Notifications</h2>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <!-- <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success me-2">
                                        <i class="fa-solid fa-check-double"></i> Tout marquer comme lu
                                </button>
                        </form> -->
                        <form action="{{ route('notifications.clearAll') }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                            <button type="button" id="clear-all-btn" class="btn btn-outline-danger">
                                <i class="fa-solid fa-trash"></i> Tout supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-style">
                @forelse($notifications as $notification)
                    <div class="single-notification {{ $notification->read_at ? 'readed' : '' }}">
                        <!-- <div class="checkbox">
                            <div class="form-check checkbox-style mb-20">
                                <input class="form-check-input" type="checkbox" value="" id="notif-{{ $notification->id }}" />
                            </div>
                        </div> -->
                        <div class="notification d-flex align-items-center">
                            <!-- <div class="image {{ $notification->read_at ? 'info-bg' : 'warning-bg' }} me-3">
                                <span>{{ strtoupper(substr($notification->data['message'] ?? 'N', 0, 1)) }}</span>
                            </div> -->
                            <div class="content flex-grow-1">
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="text-decoration-none read text-dark">
                                    <h6>{{ $notification->data['message'] ?? 'Notification' }}</h6>
                                    @if(isset($notification->data['subtitle']))
                                        <p class="text-sm text-gray">{{ $notification->data['subtitle'] }}</p>
                                    @endif
                                </a>
                                <small class="text-muted">{{ $notification->created_at->format('d/m/Y H:i') }}
                                    @if(!$notification->read_at)
                                        <span class="badge bg-primary ms-2">Nouveau</span>
                                    @endif
                                </small>
                            </div>

                            <div class="action ms-3 d-flex align-items-center">
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Marquer comme lu"><i class="fa-solid fa-check"></i></button>
                                    </form>
                                @endif
                                @if(isset($notification->data['url']))
                                    <a href="{{ $notification->data['url'] . '?notif=' . $notification->id }}" class="btn btn-sm read mt-2 d-block" title="Voir"><i class="fa-solid fa-eye"></i></a>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn" title="Supprimer"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info text-center">
                        <i class="fa-solid fa-bell-slash fa-3x mb-3"></i>
                        <h4>Aucune notification</h4>
                        <p class="mb-0">Vous n'avez aucune notification pour le moment.</p>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links() }}
            </div>

        </div>
        <!-- end container -->
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert confirmation for 'Tout supprimer'
    const clearAllBtn = document.getElementById('clear-all-btn');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Cette action supprimera toutes vos notifications !',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    clearAllBtn.closest('form').submit();
                }
            });
        });
    }
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    document.querySelectorAll('.btn.read.d-block').forEach(btn => {
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
                    },
                    body: JSON.stringify({})
                }).then(() => {
                    // Remove the "Nouveau" badge from the notification card
                    const notifCard = document.getElementById(`notif-${notifId}`);
                    if (notifCard) {
                        const parent = notifCard.closest('.single-notification');
                        if (parent) {
                            const nouveauBadge = parent.querySelector('.badge.bg-primary');
                            if (nouveauBadge) nouveauBadge.remove();
                        }
                    }
                    window.location.href = href;
                }).catch(() => {
                    window.location.href = href;
                });
            }
        });
    });
});
</script>
@endpush