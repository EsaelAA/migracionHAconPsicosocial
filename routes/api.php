<?php

use App\Http\Controllers\MigrationController;
use Illuminate\Support\Facades\Route;

Route::get('/migrateEmployees', [MigrationController::class, 'migrateEmployees']);
Route::get('/migrateQuestionnaireA', [MigrationController::class, 'migrateQuestionnaireA']);
Route::get('/migrateQuestionnaireB', [MigrationController::class, 'migrateQuestionnaireB']);
Route::get('/migrateQuestionnaireGeneralData', [MigrationController::class, 'migrateQuestionnaireGeneralData']);
Route::get('/migrateQuestionnaireExtrawork', [MigrationController::class, 'migrateQuestionnaireExtrawork']);
Route::get('/migrateQuestionnaireStress', [MigrationController::class, 'migrateQuestionnaireStress']);
