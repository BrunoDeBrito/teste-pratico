@extends('layouts.admin')

@section('content')
    <div class="main-title mt-4">
        <h5>Veiculo {{ $vehicles->id ? '(- #' . $vehicles->id . ') - ' . $vehicles->plate : '' }}</h5>
        <p>Gerencie as veiculo cadastrados</p>
    </div>

    @include('partials._alert')

    <form action="{{ url('/vehicles') }}" method="POST">

        @csrf
        @method($vehicles->id ? 'PUT' : 'POST')

        <input type="hidden" required name="id" value="{{ $vehicles->id }}" />

        <div class="row">

            <div class="col-6 mt-4">
                <label for="plate" class="form-label">Placa da Veiculo</label>
                <input type="text" class="form-control" required name="plate" id="plate" maxlength="9"
                    placeholder="Placa do veiculo" value="{{ old('plate', $vehicles->plate) }}">
            </div>

            <div class="col-6 mt-4">
                <label for="renavam" class="form-label">Renavam da Veiculo</label>
                <input type="text" class="form-control" required name="renavam" id="renavam" maxlength="11"
                    placeholder="Renavam do veiculo" value="{{ old('renavam', $vehicles->renavam) }}">
            </div>

            <div class="col-6 mt-4">
                <label for="model" class="form-label">Modelo da Veiculo</label>
                <input type="text" class="form-control" required name="model" id="model" max="90"
                    placeholder="Modelo do veiculo" value="{{ old('model', $vehicles->model) }}">
            </div>
            <div class="col-6 mt-4">
                <label for="brand" class="form-label">Marca da Veiculo</label>
                <input type="text" class="form-control" required name="brand" id="brand" maxlength="70"
                    placeholder="Marca do veiculo" value="{{ old('brand', $vehicles->brand) }}">
            </div>

            <div class="col-6 mt-4">
                <label for="year" class="form-label">Ano da Veiculo</label>
                <select class="form-select" required name="year" id="year" aria-label="Default select example">
                    <option selected>Selecione o Ano do seu veiculo</option>
                    @foreach ($years as $k => $value)
                        <option value="{{ old('year', $k) }}" {!! $k == $vehicles->year ? 'selected="selected"' : '' !!}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 mt-4">
                <label for="user_id" class="form-label">Responsável</label>
                <select class="form-select" required name="user_id" id="user_id" aria-label="Default select example">
                    <option selected>Selecione responsável pelo veiculo</option>
                    @foreach ($users as $item)
                        <option value="{{ old('user_id', $item->id) }}" {!! $item->name == $vehicles->user_name ? 'selected="selected"' : '' !!}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="main-controls text-right mt-4">
            <a class="btn btn-light" href="{{ url('vehicles') }}">Voltar</a>
            <button type="submit"
                class="btn btn-{{ $vehicles->id ? 'primary' : 'success' }}">{{ $vehicles->id ? 'Alterar' : 'Cadastrar' }}</button>
        </div>

    </form>
@endsection
