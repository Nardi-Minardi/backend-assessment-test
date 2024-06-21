<?php

namespace App\Http\Controllers;

use App\Http\Requests\DebitCardTransactionCreateRequest;
use App\Http\Requests\DebitCardTransactionDestroyRequest;
use App\Http\Requests\DebitCardTransactionShowIndexRequest;
use App\Http\Requests\DebitCardTransactionShowRequest;
use App\Http\Requests\DebitCardTransactionUpdateRequest;
use App\Http\Resources\DebitCardTransactionResource;
use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DebitCardTransactionController extends BaseController
{
    use AuthorizesRequests;
    /**
     * Get debit card transactions list
     *
     * @param DebitCardTransactionShowIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(DebitCardTransactionShowIndexRequest $request, $debitCardId): JsonResponse
    {
      $debitCard = DebitCard::find($debitCardId);
      // dd($debitCard);
      $this->authorize('view', $debitCard);

        $debitCardTransactions = $debitCard
            ->debitCardTransactions()
            ->get();

        return response()->json(DebitCardTransactionResource::collection($debitCardTransactions), HttpResponse::HTTP_OK);
    }

    /**
     * Create a new debit card transaction
     *
     * @param DebitCardTransactionCreateRequest $request
     *
     * @return JsonResponse
     */
    public function store(DebitCardTransactionCreateRequest $request)
    {
        $debitCard = DebitCard::find($request->input('debit_card_id'));
        $this->authorize('create', $debitCard);

        $debitCardTransaction = $debitCard->debitCardTransactions()->create([
            'amount' => $request->input('amount'),
            'currency_code' => $request->input('currency_code'),
        ]);

        return response()->json([
            'message' => 'Debit card transaction created successfully',
            // 'data' => new DebitCardTransactionResource($debitCardTransaction),
        ], HttpResponse::HTTP_CREATED);
    }

    /**
     * Show a debit card transaction
     *
     * @param DebitCardTransactionShowRequest $request
     * @param DebitCardTransaction            $debitCardTransaction
     *
     * @return JsonResponse
     */
    public function show(DebitCardTransactionShowRequest $request, DebitCardTransaction $debitCardTransaction)
    {
        $this->authorize('view', $debitCardTransaction);
        return response()->json(new DebitCardTransactionResource($debitCardTransaction), HttpResponse::HTTP_OK);
    }
}
