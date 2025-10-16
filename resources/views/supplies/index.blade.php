@extends('layouts.app')

@section('content')
<h1 class="mb-4">Liste des fournitures</h1>

<div class="row">
@forelse($supplies as $supply)
    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $supply->name }}</h5>
                <p class="card-text">{{ $supply->description }}</p>
            </div>
        </div>
    </div>
@empty
    <p>Aucune fourniture disponible.</p>
@endforelse
</div>
@endsection
