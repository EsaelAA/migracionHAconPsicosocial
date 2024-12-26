<?php

use App\Http\Controllers\MigrationController;
use Illuminate\Support\Facades\Route;

Route::get('/migrateEmployees', [MigrationController::class, 'migrateEmployees']);
Route::get('/migrateQuestionnaireA', [MigrationController::class, 'migrateQuestionnaireA']);
Route::get('/migrateQuestionnaireB', [MigrationController::class, 'migrateQuestionnaireB']);
