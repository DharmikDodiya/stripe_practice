<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\StripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('stripe',[StripeController::class,'stripePayment']);


Route::middleware('auth:sanctum')->group(function(){

    Route::controller(AuthController::class)->group(function(){
        Route::post('register','register');
        Route::post('login','login');
    });

    Route::controller(StripeController::class)->prefix('stripe')->group(function(){
        Route::post('single-charge','singleCharge');
        Route::post('plan-checkout/{id}','planCheckout');
    });

    Route::controller(PlanController::class)->prefix('plan')->group(function(){
        Route::post('create-plan','storePlan');
        Route::get('get-plan','getPlan');
        Route::post('update-plan','updatePlan');
        Route::delete('delete-plan/{id}','deletePlan');
    });
});
