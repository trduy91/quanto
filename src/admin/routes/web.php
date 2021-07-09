<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RandomController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\ReferralInfoController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
Auth::routes();

Route::get('/test', [RandomController::class, 'index']);
Route::get('/', [SurveyController::class,'index'])->name('admin.surveys');

Route::get('/profile', [ProfileController::class,'index'])->name('profile');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');


Route::get('/admin', [SurveyController::class,'index'])->name('admin.surveys');

Route::get('/admin/users', [UserController::class,'index'])->name('admin.users');
Route::get('/admin/user/edit/{id}', [UserController::class,'edit'])->name('admin.user.edit');
Route::get('/admin/user/delete/{id}', [UserController::class,'delete'])->name('admin.user.delete');
Route::post('/admin/user/save', [ClientController::class,'save'])->name('admin.user.save');
Route::post('admin/user/upload', [UserController::class,'upload'])->name('admin.user.upload');

Route::get('/admin/clients', [ClientController::class,'index'])->name('admin.clients');
Route::get('/admin/client/show/{id}', [ClientController::class,'show'])->name('admin.client.show');
Route::post('/admin/client/sendMail', [ClientController::class,'clientSendMail'])->name('admin.client.sendMail');



Route::get('/admin/surveys', [SurveyController::class,'index'])->name('admin.surveys');
Route::get('/admin/survey/add', [SurveyController::class,'add'])->name('admin.survey.add');
Route::get('/admin/survey/delete/{id}', [SurveyController::class,'delete'])->name('admin.survey.delete');
Route::post('/admin/survey/save', [SurveyController::class,'save'])->name('admin.survey.save');
Route::get('/admin/survey/edit/{id}', [SurveyController::class,'edit'])->name('admin.survey.edit');
Route::get('/admin/formularSetting', [SurveyController::class,'formularSetting'])->name('admin.formularSetting');
Route::post('/admin/formularSetting/save', [SurveyController::class,'formularSave'])->name('admin.formularSetting.save');


Route::get('/admin/referralInfo', [ReferralInfoController::class,'index'])->name('admin.referralInfo');
Route::get('/admin/referralInfo/add', [ReferralInfoController::class,'add'])->name('admin.referralInfo.add');
Route::get('/admin/referralInfo/delete/{id}', [ReferralInfoController::class,'delete'])->name('admin.referralInfo.delete');
Route::post('/admin/referralInfo/save', [ReferralInfoController::class,'save'])->name('admin.referralInfo.save');
Route::get('/admin/referralInfo/edit/{id}', [ReferralInfoController::class,'edit'])->name('admin.referralInfo.edit');
