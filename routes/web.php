<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\General\HomeController;
use App\Http\Controllers\General\AboutController;
use App\Http\Controllers\SpaController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
 *
 * General Routes
 *
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/about', [AboutController::class, 'index']);


/*
 *
 * Redirect all requests to Vue Router
 *
*/
Route::get('/{vue_capture?}', [SpaController::class, 'index'])
  ->where('vue_capture', '[\/\w\.-]*');
