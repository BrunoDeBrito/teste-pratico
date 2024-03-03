<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Vehicles};
use App\Notifications\CustomerNotification;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehiclesController extends Controller
{
    private function isAdmin()
    {

        $user = Auth::user();

        if ($user->role == User::ROLE_ADMIN) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * ANCHOR Carrega as informações para apresentação do formulário
     *
     */
    private function form($request, $vehicles)
    {

        if (!$this->isAdmin()) {
            return redirect('vehicles')->withErrors('Você não tem permissão para acessar esta página!');
        }

        $users = User::where('role', 1)->orderBy('name', 'asc')->get();

        $years = [];

        // Definir os anos inicial e final
        $initDate = 1880;
        $lastDate = date('Y') + 2;

        // Adicionar os anos ao array
        for ($year = $lastDate; $year >= $initDate; $year--) {
            $years[$year] = $year;
        }

        $data = [
            'vehicles' => $vehicles,
            'users'    => $users,
            'years'    => $years,
        ];

        return view('vehicles.admin.create-edit', $data);
    }

    /**
     * ANCHOR Grava os dados da Veiculo
     *
     * @return void
     */
    private function save($request, $vehicles)
    {

        try {

            DB::beginTransaction();

            $vehicles->plate   = strtoupper(strtolower($request->plate));
            $vehicles->renavam = $request->renavam;
            $vehicles->model   = ucwords(strtolower($request->model));
            $vehicles->brand   = ucwords(strtolower($request->brand));
            $vehicles->year    = $request->year;
            $vehicles->user_id = $request->user_id;

            $vehicles->save();

            DB::commit();

        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }

    }

    /**
     * ANCHOR Valida as informações da Veiculo
     *
     * @param [type] $request
     * @return object
     */
    private function validator($request)
    {
        $validator = Validator::make($request->all(), [
            'id'      => 'nullable|numeric|required_if:_method,PUT',
            'year'    => 'required|string|max:4',
            'plate'   => 'required|string|max:9|unique:vehicles,plate' . ($request->id ? (',' . $request->id) : ''),
            'brand'   => 'required|string|max:50',
            'model'   => 'required|string|max:50',
            'user_id' => 'required|numeric|exists:users,id',
            'renavam' => 'required|string|max:11|unique:vehicles,renavam' . ($request->id ? (',' . $request->id) : ''),
        ], [
            'id.required'      => 'O campo id é obrigatório!',
            'year.required'    => 'O campo ano é obrigatório!',
            'year.max'         => 'O campo ano deve ter no máximo 4 caracteres!',
            'plate.required'   => 'O campo placa é obrigatório!',
            'plate.max'        => 'O campo placa deve ter no máximo 9 caracteres!',
            'plate.unique'     => 'A placa informada já está cadastrada!',
            'brand.required'   => 'O campo marca é obrigatório!',
            'brand.max'        => 'O campo marca deve ter no máximo 50 caracteres!',
            'model.required'   => 'O campo modelo é obrigatório!',
            'model.max'        => 'O campo modelo deve ter no máximo 50 caracteres!',
            'user_id.required' => 'O campo usuário é obrigatório!',
            'user_id.numeric'  => 'O campo usuário deve ser um número!',
            'user_id.exists'   => 'O usuário informado não está cadastrado!',
            'renavam.required' => 'O campo renavam é obrigatório!',
            'renavam.max'      => 'O campo renavam deve ter no máximo 11 caracteres!',
            'renavam.unique'   => 'O renavam informado já está cadastrado!',

        ]);

        return $validator;

    }

    /**
     * ANCHOR Apresenta a listagem de vehicles
     *
     */
    public function index(Request $request)
    {

        $user = Auth::user();

        $vehicles = Vehicles::search($request->search, $user)
            ->get();

        $data = [
            'vehicles' => $vehicles,
            'user'     => $user,
        ];

        return view('vehicles.index', $data);
    }

    /**
     * ANCHOR Apresenta o formulario de criação
     *
     * @param Vehicles $vehicles
     */
    public function create(Request $request)
    {

        if (!$this->isAdmin()) {
            return redirect('vehicles')->withErrors('Você não tem permissão para acessar esta página!');
        }

        return $this->form($request, new Vehicles());
    }

    /**
     * ANCHOR Criação das vehicles
     *
     * @param Request $request
     */
    public function insert(Request $request)
    {

        try {

            if (!$this->isAdmin()) {
                return redirect('vehicles')->withErrors('Você não tem permissão para acessar esta página!');
            }

            //NOTE Realiza a validação dos dados enviados pelo formulário
            $validator = $this->validator($request);

            if (!$validator->fails()) {

                $vehicles = new Vehicles();
                $this->save($request, $vehicles);

                return redirect('vehicles')
                    ->withSuccess('Veiculo cadastrado com sucesso!');

            } else {
                return back()->withErrors($validator->errors()->first());
            }
        } catch (Exception $e) {
            return back()->withInput()->whithErrors('Não e possivel inserir o veiculo:' . $e->getMessage() . '.');
        }

    }

    /**
     * ANCHOR Apresenta formulario de atualização das vehicles
     *
     * @param Vehicles $vehicles
     * @param int $id
     */
    public function edit(Request $request, $id)
    {

        if (!$this->isAdmin()) {
            return redirect('vehicles')->withErrors('Você não tem permissão para acessar esta página!');
        }

        $vehicles = Vehicles::getUserVehicles($id)->first();

        if ($vehicles) {

            return $this->form($request, $vehicles);

        } else {

            return redirect('vehicles')->withErrors('Veiculo inválido!');
        }

    }

    /**
     * ANCHOR Altera os dados de um Veiculo
     *
     * @param Request $request
     * @param Vehicles $vehicles
     * @param int $id
     */
    public function update(Request $request)
    {

        try {

            if (!$this->isAdmin()) {
                return redirect('vehicles')->withErrors('Você não tem permissão para acessar esta página!');
            }

            $validator = $this->validator($request);
            $user      = Auth::user();

            if (!$validator->fails()) {

                $vehicles = Vehicles::find($request->id);

                if ($vehicles) {

                    $this->save($request, $vehicles);

                    $sendNotify = Vehicles::getUserVehicles($vehicles->id)->first();

                    $user->notify(new CustomerNotification($sendNotify->user_name, $sendNotify->user_email, "Veiculo Alterado", "O veiculo " . $sendNotify->plate . " foi alterado com sucesso por " . $user->name));

                    return redirect('vehicles')
                        ->withSuccess('Veiculo alterada com sucesso!');

                } else {

                    return back()->withErrors('Veiculo inválido!');
                }

            } else {

                return back()->withErrors($validator->errors()->first());

            }

        } catch (Exception $e) {
            return back()->withInput()->withErrors('Não e possivel alterar o veiculo:' . $e->getMessage() . '.');
        }

    }

    /**
     * ANCHOR Remove um Veiculo
     *
     * @param Request $request
     */
    public function delete(Request $request)
    {

        try {

            if (!$this->isAdmin()) {
                return redirect('vehicles')->withErrors('Você não tem permissão para acessar esta página!');
            }

            $user = Auth::user();

            $vehicles = Vehicles::find($request->id);

            if ($vehicles) {

                $sendNotify = Vehicles::getUserVehicles($vehicles->id)->first();

                $user->notify(new CustomerNotification($sendNotify->user_name, $sendNotify->user_email, "Veiculo Removido", "O veiculo " . $sendNotify->plate . " foi removido com sucesso por " . $user->name));

                $vehicles->delete();

                return back()->withSuccess('Veiculo removida com sucesso!');
            }

            return back()->withErrors('Não foi possível remover a Veiculo. Dados inválidos!');

        } catch (Exception $e) {

            return back()->withInput()->withErrors('Não foi possível remover a Veiculo: ' . $e->getMessage() . '.');
        }
    }
}
