@extends($user->role == 2 ? 'layouts.admin' : 'layouts.app')
@section('content')
    <div class="main-title mt-4">
        <h5>Veiculos</h5>
        <p>Gerencie as veiculos cadastrados</p>
    </div>

    @include('partials._alert')
    {{--
    @php
        $paginate = [
            '1'  => 1,
            '10'  => 10,
            '35'  => 35,
            '50'  => 50,
            '100' => 100,
        ];
    @endphp --}}

    <form>

        <div class="row mb-4">

            <div class="col-8">
                <input type="text" name="search" class="form-control" value="{{ Request::get('search') }}"
                    placeholder="Pesquise Placa, Renavam, Ano, Etc...">
            </div>

            {{-- <div class="col-4">
                <select class="form-select  " name='pagination' aria-label="Default select example">
                    @foreach ($paginate as $key => $value)
                        <option value="{{ $value }}">{{ $key }}</option>
                    @endforeach
                </select>
            </div> --}}
        </div>
</form>

    @if ($vehicles->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Placa</th>
                        <th scope="col">Renavam</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Responsavel</th>
                        <th scope="col">Criado em :</th>
                        @if ($user->role == 2)
                            <th scope="col">Editar</th>
                            <th scope="col">Remover</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vehicles as $item)
                        <tr>
                            <td> {{ $item->id }} </td>
                            <td> {{ $item->plate }} </td>
                            <td> {{ $item->renavam }} </td>
                            <td> {{ $item->model }} </td>
                            <td> {{ $item->brand }} </td>
                            <td> <a href="{{ 'user/'.$item->user_id.'/info' }}" class="btn btn-outline-dark">{{ $item->user_name }}</a> </td>
                            <td> {{ $item->created_at->format('d/m/Y H:i') }} </td>
                            @if ($user->role == 2)
                                <td>
                                    <a class="btn btn-success" href="{{ url('vehicles/' . $item->id . '/edit') }}"><i
                                            class="bi bi-pencil-square"></i>
                                    </a>

                                </td>
                                <td>
                                    <form action="/vehicles/{{ $item->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger btn-remove">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            Nenhum veiculo cadastrado para este usuário
        </div>
    @endif

    @if ($user->role == 2)
        <div class="main-controls text-right mt-2">
            <a class="btn btn-primary" href="{{ url('vehicles/create') }}">Novo Veiculo </a>
        </div>
    @endif
@endsection
