@extends('layouts.admin')

@section('content')
    <div class="main-title mt-4">
        <h5>Usuário {{ $user->id ? '(- #' . $user->id . ') - ' . $user->name : '' }}</h5>
        {{-- <p>Gerencie as veiculo cadastrados</p> --}}
    </div>

    @include('partials._alert')


    <div class="row">

        <div class="col-6 mt-4">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" required name="name" id="name" disabled
                placeholder="Placa do veiculo" value="{{ old('name', $user->name) }}">
        </div>

        <div class="col-6 mt-4">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" required name="email" id="email" disabled
                placeholder="email do veiculo" value="{{ old('email', $user->email) }}">
        </div>

        <div class="col-6 mt-4">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" required name="phone" id="phone" disabled
                placeholder="phoneo do veiculo" value="{{ old('phone', $user->phone) }}">
        </div>

        <div class="col-6 mt-4">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" class="form-control" required name="cpf" id="cpf" disabled
                placeholder="Marca do veiculo" value="{{ old('cpf', $user->cpf) }}">
        </div>

    </div>

    <div class="main-controls text-right mt-4">
        <a class="btn btn-outline-secondary" href="{{ url('vehicles') }}">Voltar</a>
    </div>
@endsection
