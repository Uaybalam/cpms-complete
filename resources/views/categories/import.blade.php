@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3>Importar Precios</h3></div>
            <div class="card-body">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Importar CSV</title>
                    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
                </head>
                <body>
                    <div class="container mt-5">
                        <h2>Importar CSV</h2>
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                                <pre>{{ session('output') }}</pre>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="/csv-import" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="csv_file">Seleccionar archivo CSV:</label>
                                <input type="file" class="form-control-file" id="csv_file" name="csv_file" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Importar</button>
                        </form>
                    </div>
                </body>

            </div>
        </div>
    </div>

</div>
@endsection
