<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Vehicles};
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

        $users = User::orderBy('name', 'asc')->get();

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
            'users' => $users,
            'years' => $years,
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

            $vehicles->plate = strtoupper(strtolower($request->plate));
            $vehicles->renavam = $request->renavam;
            $vehicles->model = ucwords(strtolower($request->model));
            $vehicles->brand = ucwords(strtolower($request->brand));
            $vehicles->year = $request->year;
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
            'id' => 'nullable|numeric|required_if:_method,PUT',
            'plate' => 'required|string|max:9|unique:vehicles,plate' . ($request->id ? (',' . $request->id) : ''),
            'renavam' => 'required|string|max:11|unique:vehicles,renavam' . ($request->id ? (',' . $request->id) : ''),
            'model' => 'required|string|max:50',
            'brand' => 'required|string|max:50',
            'year' => 'required|numeric',
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

        $vehicles = Vehicles::search($request->search)
            ->get();

        $data = [
            'vehicles' => $vehicles,
            'user' => $user,
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

            if (!$validator->fails()) {

                $vehicles = Vehicles::find($request->id);

                if ($vehicles) {

                    $this->save($request, $vehicles);

                    return redirect('vehicles')
                        ->withSuccess('Veiculo alterada com sucesso!');

                } else {

                    return back()->withErrors('Veiculo inválido!');
                }

            } else {

                return back()->withErrors($validator->errors()->first());

            }

        } catch (Exception $e) {
            return back()->withInput()->whithErrors('Não e possivel alterar o veiculo:' . $e->getMessage() . '.');
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

            $vehicles = Vehicles::find($request->id);

            if ($vehicles) {

                $vehicles->delete();

                return back()->withSuccess('Veiculo removida com sucesso!');
            }

            return back()->withErrors('Não foi possível remover a Veiculo. Dados inválidos!');

        } catch (Exception $e) {

            return back()->withInput()->withErrors('Não foi possível remover a Veiculo: ' . $e->getMessage() . '.');
        }
    }
}
