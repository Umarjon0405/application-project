<?php

use App\Http\Controllers\ApplicationCategoryController;
use App\Http\Controllers\ApplicationTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get("/user", [UserController::class, 'auth']);
    Route::get("/users", [UserController::class, 'index']);
    Route::post("/users", [UserController::class, 'store']);
    Route::put("/users/{id}", [UserController::class, 'update']);

    Route::apiResource('/application-categories', ApplicationCategoryController::class);

    Route::apiResource('/application-types', ApplicationTypeController::class);
});

