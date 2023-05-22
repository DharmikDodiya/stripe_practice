<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('payment-form',[StripeController::class,'form'])->name('make.form');
// Route::post('make/payment',[StripeController::class,'makePayment'])->name('make.payment');


// Route::controller(StripeController::class)->group(function(){
//     Route::get('stripe', 'stripe');
//     Route::post('stripe', 'stripePost')->name('stripe.post');
// });


// use Illuminate\Http\Request;
// use Stripe\Stripe;

// Route::get('/payment', function () {
//     return view('payment');
// });

// Route::post('/payment', function (Request $request) {
//     Stripe::setApiKey(config('services.stripe.secret'));

//     $amount = 1000; // Amount in cents
//     $currency = 'usd';

//     $token = $request->input('stripeToken');

//     try {
//         $charge = \Stripe\Charge::create([
//             'amount' => $amount,
//             'currency' => $currency,
//             'source' => $token,
//         ]);

//         // Payment successful, handle further actions (e.g., update database)

//         return 'Payment successful!';
//     } catch (\Stripe\Exception\CardException $e) {
//         // Payment failed, handle the exception
//         return $e->getMessage();
//     }
// });
