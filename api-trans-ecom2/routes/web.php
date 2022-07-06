<?php

use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/berita', [BeritaController::class, 'index']);
Route::get('/berita/{id}', [BeritaController::class, 'show']);
Route::get('/form/berita', [BeritaController::class, 'callForm']);
Route::post('/berita', [BeritaController::class, 'store']);

Route::get('/produk', [ProdukController::class, 'index']);
Route::post('/produk', [ProdukController::class, 'store']);

Route::get('/search/produk', [ProdukController::class, 'search']);

Route::post('/login', [UserController::class, 'cek_login']);
Route::get('/laporan/transaksi', [TransaksiController::class, 'cekTransaksi']);

// Route::get('/transaksi', [TransaksiController::class, 'index']);
