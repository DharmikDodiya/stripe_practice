<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentIntentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SubscriptionController;
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


Route::controller(AuthController::class)->group(function(){
    Route::post('register','register');
    Route::post('login','login');
});

Route::middleware('auth:sanctum')->group(function(){

    Route::controller(StripeController::class)->prefix('stripe')->group(function(){
        Route::post('single-charge','singleCharge');
        Route::post('plan-checkout/{id}','planCheckout');
        Route::post('create-payment','stripePayment');
    });

    Route::controller(CustomerController::class)->prefix('customer')->group(function(){
        Route::post('create','createCustomer');
        Route::get('get/{id}','getCustomer');
        Route::get('list','listCustomer');
        Route::delete('delete/{id}','deleteCustomer');
    });

    Route::controller(PaymentMethodController::class)->prefix('payment-method')->group(function(){
        Route::post('create','createPaymentMethod');
        Route::get('list/{id}','listPaymentMethod');
        Route::get('get/{id}','getPaymentMethod');
        Route::post('detach/{id}','detachPaymentMethod');
        Route::post('create-token','createToken');
        Route::post('create-payment/{id}','attachPaymentMethod');
    });

    Route::controller(CardController::class)->prefix('card')->group(function(){
        Route::post('create-card/{id}','createCard');
        Route::get('{cus_id}/get/{card_id}','getCard');
        Route::delete('{cus_id}/delete/{card_id}','deleteCard');
        Route::get('list/{id}','listCard');
    });

    Route::controller(PlanController::class)->prefix('plan')->group(function(){
        Route::post('create','storePlan');
        Route::get('get/{id}','getPlan');
        Route::post('update/{id}','updatePlan');
        Route::delete('delete/{id}','deletePlan');
        Route::get('list','listPlan');
    });

    Route::controller(PriceController::class)->prefix('price')->group(function(){
        Route::post('create','createPrice');
        Route::get('get/{id}','getPrice');
        Route::patch('update/{id}','updatePrice');
        Route::get('list','priceList');
    });

    Route::controller(ProductController::class)->prefix('product')->group(function(){
        Route::post('create','createProduct');
        Route::get('get/{id}','getProduct');
        Route::get('list','listProduct');
        Route::patch('update/{id}','updateProduct');
        Route::delete('delete/{id}','deleteProduct');
    });

    Route::controller(SubscriptionController::class)->prefix('subscription')->group(function(){
        Route::post('create','createSubscription');
        Route::get('get/{id}','getSubscription');
        Route::get('list','listSubscription');
        Route::delete('cancle/{id}','cancleSubscription');
        Route::post('resume/{id}','resumeSubscription');
    });

    Route::controller(InvoiceController::class)->prefix('invoice')->group(function(){
        Route::post('create','createInvoice');
        Route::get('get/{id}','getInvoice');
        Route::patch('update/{id}','updateInvoice');
        Route::get('list','listInvoice');
        Route::delete('delete/{id}','deleteInvoice');
    });

    Route::controller(ChargeController::class)->prefix('charge')->group(function(){
        Route::post('create','createCharge');
    });

    Route::controller(CouponController::class)->prefix('coupon')->group(function(){
        Route::post('create','createCoupon');
        Route::get('get/{id}','getCoupon');
        Route::put('update/{id}','updateCoupon');
        Route::get('list','listCoupon');
        Route::delete('delete/{id}','deleteCoupon');
        // Route::post('create-payout','createPayout');
    });

    Route::controller(PaymentIntentController::class)->prefix('payment-intent')->group(function(){
        Route::post('create','createPaymentIntent');
        Route::get('get/{id}','getPaymentIntent');
        Route::get('list','listPaymentIntent');
        Route::put('update/{id}','updatePaymentIntent');
        Route::post('capture/{id}','capturePaymentIntent');
    });
});
