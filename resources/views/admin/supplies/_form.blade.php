<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(in_array($method, ['PUT','PATCH']))
        @method($method)
    @endif

    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $supply->name ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Catégorie</label>
        <input type="text" name="category" class="form-control" value="{{ old('category', $supply->category ?? '') }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Unité</label>
        <input type="text" name="unit" class="form-control" value="{{ old('unit', $supply->unit ?? '') }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description', $supply->description ?? '') }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Image URL (chemin relatif ou URL)</label>
        <input type="text" name="image_url" class="form-control" value="{{ old('image_url', $supply->image_url ?? '') }}">
        <small class="form-text text-muted">Ex: /images/supplies/tissu-coton.svg ou https://.../image.png</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Uploader une image</label>
        <input type="file" name="image_file" accept="image/*" class="form-control">
        @if(!empty($supply->image_url))
            <div class="mt-2">
                <small>Image actuelle :</small>
                <div>
                    <img src="{{ $supply->image_url }}" alt="" style="max-width:120px;max-height:80px;object-fit:contain">
                </div>
            </div>
        @endif
    </div>

    <div>
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        <a href="{{ route('admin.supplies.index') }}" class="btn btn-secondary">Annuler</a>
    </div>
</form>
