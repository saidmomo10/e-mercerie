@extends('layouts.app')

@section('content')

<div class="header-search d-none d-md-flex">
    <form action="{{ route('merceries.index') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher une mercerie..." />
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
</div>

<div class="container mt-4">
    <h1 class="mb-4">Liste des merceries</h1>

    @if($merceries->isEmpty())
        <div class="alert alert-info">
            Aucune mercerie trouvée pour "{{ request('search') }}"
        </div>
    @else
        <div class="row">
            @foreach($merceries as $mercerie)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $mercerie->name }}</h5>
                            <p class="card-text">Email : {{ $mercerie->email }}</p>
                            <a href="{{ route('merceries.show', $mercerie->id) }}" class="btn btn-primary btn-sm">
                                Voir les fournitures
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
