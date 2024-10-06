<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ExpenseRegistered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/expenses",
     *     tags={"Despesas"},
     *     summary="Lista todas as despesas",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Expense"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     */
    public function index()
    {
        try {
            $expenses = Expense::where('user_id', Auth::id())->get();
            return response()->json(ExpenseResource::collection($expenses), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar despesas', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/expenses",
     *     tags={"Despesas"},
     *     summary="Cria uma nova despesa",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreExpenseRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Despesa criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Expense")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos"
     *     )
     * )
     */
    public function store(StoreExpenseRequest $request)
    {
        try {
            $expense = Auth::user()->expenses()->create($request->validated());
            Auth::user()->notify(new ExpenseRegistered($expense));

            return response()->json(new ExpenseResource($expense), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar despesa', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/{id}",
     *     tags={"Despesas"},
     *     summary="Exibe uma despesa específica",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da despesa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Expense")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Despesa não encontrada"
     *     )
     * )
     */
    public function show(Expense $expense)
    {
        try {
            $this->authorize('view', $expense);
            return response()->json(new ExpenseResource($expense), 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['error' => 'Acesso negado', 'message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao exibir despesa', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/expenses/{id}",
     *     tags={"Despesas"},
     *     summary="Atualiza uma despesa",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da despesa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateExpenseRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Despesa atualizada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Expense")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Despesa não encontrada"
     *     )
     * )
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        try {
            $this->authorize('update', $expense);
            $expense->update($request->validated());
            return response()->json(new ExpenseResource($expense), 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['error' => 'Acesso negado', 'message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar despesa', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/expenses/{id}",
     *     tags={"Despesas"},
     *     summary="Remove uma despesa",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da despesa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Despesa removida com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Despesa não encontrada"
     *     )
     * )
     */
    public function destroy(Expense $expense)
    {
        try {
            $this->authorize('delete', $expense);
            $expense->delete();
            return response()->json(null, 204);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['error' => 'Acesso negado', 'message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao remover despesa', 'message' => $e->getMessage()], 500);
        }
    }
}
