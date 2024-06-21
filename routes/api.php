<?php

use App\Http\Controllers\DebitCardController;
use App\Http\Controllers\DebitCardTransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['cors', 'json.response']], function () {
    // Public routes
    Route::post('login', [AuthController::class, 'login'])->name('login.api');
  });
  
  Route::middleware('auth:api')
  ->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout.api');
        
        // Debit card endpoints
        Route::get('debit-cards', [DebitCardController::class, 'index']);
        Route::post('debit-cards', [DebitCardController::class, 'store']);
        Route::get('debit-cards/{debitCard}', [DebitCardController::class, 'show']);
        Route::put('debit-cards/{debitCard}', [DebitCardController::class, 'update']);
        Route::delete('debit-cards/{debitCard}', [DebitCardController::class, 'destroy']);

        // Debit card transactions endpoints
        Route::get('debit-card-transactions/{debitCardId}', [DebitCardTransactionController::class, 'index']);
        Route::post('debit-card-transactions', [DebitCardTransactionController::class, 'store']);
        Route::get('debit-card-transactions/{debitCardTransaction}', [DebitCardTransactionController::class, 'show']);

        //loan endpoints
        Route::get('loans', [LoanController::class, 'index']);
        Route::post('loans', [LoanController::class, 'store']);
        Route::post('loans-pay/partial', [LoanController::class, 'updatePartial']);
        Route::post('loans-pay/repaid', [LoanController::class, 'updateRepaid']);
        Route::get('loans/{id}', [LoanController::class, 'show']);
    });
