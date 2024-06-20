<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LoanResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use App\Models\Loan;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\LoanService;
use App\Http\Requests\LoanCreateRequest;
use App\Http\Requests\LoanPayPartialRequest;
use App\Http\Requests\LoanPayRepaidRequest;

class LoanController extends BaseController
{
  use AuthorizesRequests;

  public function index(): JsonResponse
  {
    try {
      $loans = Auth::user()->loans;

      if ($loans->isEmpty()) {
        return response()->json(["message" => "No records found"], HttpResponse::HTTP_NOT_FOUND);
      }

      return response()->json(LoanResource::collection($loans), HttpResponse::HTTP_OK);
    } catch (\Throwable $th) {
      $response = ["message" => $th->getMessage()];
      return response()->json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show($id): JsonResponse
  {
    try {
      $loan = Loan::find($id);
      if (!$loan) {
        return response()->json(["message" => "Loan not found"], HttpResponse::HTTP_NOT_FOUND);
      }
      
      $this->authorize('view', $loan);

      return response()->json(new LoanResource($loan), HttpResponse::HTTP_OK);
    } catch (\Throwable $th) {
      $response = ["message" => $th->getMessage()];
      return response()->json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function store(LoanCreateRequest $request, LoanService $loanService): JsonResponse
  {
    try {
      $this->authorize('create', Loan::class);
      $loan = $loanService->createLoan(
        Auth::user(),
        $request->input('amount'),
        $request->input('currency_code'),
        $request->input('terms'),
        $request->input('processed_at')
      );
    

      return response()->json(["message" => "Loan created successfully", "data" => new LoanResource($loan)], HttpResponse::HTTP_CREATED);
    } catch (\Throwable $th) {
      $response = ["message" => $th->getMessage()];
      return response()->json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function updatePartial(LoanPayPartialRequest $request, LoanService $loanService): JsonResponse
  {
    try {
      $loan = Loan::find($request->input('loan_id'));
      $this->authorize('update', $loan);
     
      //cek scheduled repayment is due
      $scheduled_repayment = $loan->scheduledRepayments()->where('id', $request->input('scheduled_repayment_id'))->where('status', Loan::STATUS_DUE)->first();
      if (!$scheduled_repayment) {
        return response()->json(["message" => "Scheduled repayment is repaid"], HttpResponse::HTTP_BAD_REQUEST);
      }

      $received_payments = $loanService->repayLoan(
        Auth::user()->loans()->find($request->input('loan_id')),
        $request->input('amount'),
        \Carbon\Carbon::now()->toDateTimeString(),
        $request->input('scheduled_repayment_id')
      );
      

      return response()->json(["message" => "Loan updated successfully"], HttpResponse::HTTP_CREATED);
    } catch (\Throwable $th) {
      $response = ["message" => $th->getMessage()];
      return response()->json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function updateRepaid(LoanPayRepaidRequest $request, LoanService $loanService): JsonResponse
  {
    try {
      $loan = Loan::find($request->input('loan_id'));
      $this->authorize('update', $loan);
     
      //cek is loan is repaid
      if ($loan->status == Loan::STATUS_REPAID) {
        return response()->json(["message" => "Loan is repaid"], HttpResponse::HTTP_BAD_REQUEST);
      }

      //cek if amount is less than the total amount
      if ($request->input('amount') < $loan->amount) {
        return response()->json(["message" => "Amount is less than the total amount"], HttpResponse::HTTP_BAD_REQUEST);
      }

      $received_payments = $loanService->repayAllLoan(
        Auth::user()->loans()->find($request->input('loan_id')),
        $request->input('amount'),
        \Carbon\Carbon::now()->toDateTimeString(),
      );
      

      return response()->json(["message" => "Loan updated successfully"], HttpResponse::HTTP_CREATED);
    } catch (\Throwable $th) {
      $response = ["message" => $th->getMessage()];
      return response()->json($response, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
