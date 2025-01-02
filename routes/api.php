<?php

use App\Http\Controllers\MigrationController;
use Illuminate\Support\Facades\Route;

Route::get('/migrateEmployees', [MigrationController::class, 'migrateEmployees']);
Route::get('/migrateEmployees2017', [MigrationController::class, 'migrateEmployees2017']);
Route::get('/migrateEmployees2018', [MigrationController::class, 'migrateEmployees2018']);
Route::get('/migrateEmployees2019', [MigrationController::class, 'migrateEmployees2019']);
Route::get('/migrateEmployees2020', [MigrationController::class, 'migrateEmployees2020']);
Route::get('/migrateEmployees2021', [MigrationController::class, 'migrateEmployees2021']);
Route::get('/migrateEmployees2024', [MigrationController::class, 'migrateEmployees2024']);
Route::get('/migrateQuestionnaireA', [MigrationController::class, 'migrateQuestionnaireA']);
Route::get('/migrateQuestionnaireB', [MigrationController::class, 'migrateQuestionnaireB']);
Route::get('/migrateQuestionnaireGeneralData', [MigrationController::class, 'migrateQuestionnaireGeneralData']);
Route::get('/migrateQuestionnaireExtrawork', [MigrationController::class, 'migrateQuestionnaireExtrawork']);
Route::get('/migrateQuestionnaireStress', [MigrationController::class, 'migrateQuestionnaireStress']);
Route::get('/migrateQuestionnaireWeather', [MigrationController::class, 'migrateQuestionnaireWeather']);
