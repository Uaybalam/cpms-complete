<form action="{{ route('categories.store') }}" class="forms-sample" method="POST">
    @csrf
    <div class="form-group">
        <label for="exampleInputName1">Nombre</label>
        <input type="text" name="name" value="{{ isset($category) ? $category->name : '' }}" class="form-control" id="exampleInputName1" placeholder="Name">
        @if (isset($category))
        <input type="hidden" name="category_id" value="{{ $category->id }}">
        @endif
    </div>
    <button type="submit" class="btn btn-primary mr-2">Crear</button>
    <button class="btn btn-light">Cancelar</button>
</form>
