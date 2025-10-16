@extends('layouts.app')

@section('content')
<h1 class="mb-4">Sélection des fournitures</h1>

<form action="{{ route('merceries.compare') }}" method="POST">
    @csrf
    <div class="row">
        @foreach($supplies as $supply)
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $supply->name }}</h5>
                        <p class="card-text">{{ $supply->description }}</p>
                        <div class="mb-2">
                            <label for="quantity_{{ $supply->id }}" class="form-label">Quantité</label>
                            <input type="number" min="0" name="items[{{ $supply->id }}][quantity]" id="quantity_{{ $supply->id }}" class="form-control" value="0">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-primary mt-3">Comparer les merceries</button>
</form>
@endsection
