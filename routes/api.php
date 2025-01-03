<?php

use App\Http\Controllers\MigrationController;
use Illuminate\Support\Facades\Route;

Route::get('/migrateEmployees', [MigrationController::class, 'migrateEmployees']);
Route::get('/migrateQuestionnaireA', [MigrationController::class, 'migrateQuestionnaireA']);
Route::get('/migrateQuestionnaireB', [MigrationController::class, 'migrateQuestionnaireB']);
Route::get('/migrateQuestionnaireGeneralData', [MigrationController::class, 'migrateQuestionnaireGeneralData']);
Route::get('/migrateQuestionnaireExtrawork', [MigrationController::class, 'migrateQuestionnaireExtrawork']);
Route::get('/migrateQuestionnaireStress', [MigrationController::class, 'migrateQuestionnaireStress']);
Route::get('/migrateQuestionnaireWeather', [MigrationController::class, 'migrateQuestionnaireWeather']);

Route::get('/migrateEmployees2017', [MigrationController::class, 'migrateEmployees2017']);
Route::get('/migrateQuestionnaireA2017', [MigrationController::class, 'migrateQuestionnaireA2017']);
Route::get('/migrateQuestionnaireB2017', [MigrationController::class, 'migrateQuestionnaireB2017']);
Route::get('/migrateQuestionnaireGeneralData2017', [MigrationController::class, 'migrateQuestionnaireGeneralData2017']);
Route::get('/migrateQuestionnaireExtrawork2017', [MigrationController::class, 'migrateQuestionnaireExtrawork2017']);
Route::get('/migrateQuestionnaireStress2017', [MigrationController::class, 'migrateQuestionnaireStress2017']);

Route::get('/migrateEmployees2018', [MigrationController::class, 'migrateEmployees2018']);
Route::get('/migrateQuestionnaireA2018', [MigrationController::class, 'migrateQuestionnaireA2018']);
Route::get('/migrateQuestionnaireB2018', [MigrationController::class, 'migrateQuestionnaireB2018']);
Route::get('/migrateQuestionnaireGeneralData2018', [MigrationController::class, 'migrateQuestionnaireGeneralData2018']);
Route::get('/migrateQuestionnaireExtrawork2018', [MigrationController::class, 'migrateQuestionnaireExtrawork2018']);
Route::get('/migrateQuestionnaireStress2018', [MigrationController::class, 'migrateQuestionnaireStress2018']);

Route::get('/migrateEmployees2019', [MigrationController::class, 'migrateEmployees2019']);
Route::get('/migrateQuestionnaireA2019', [MigrationController::class, 'migrateQuestionnaireA2019']);
Route::get('/migrateQuestionnaireB2019', [MigrationController::class, 'migrateQuestionnaireB2019']);
Route::get('/migrateQuestionnaireGeneralData2019', [MigrationController::class, 'migrateQuestionnaireGeneralData2019']);
Route::get('/migrateQuestionnaireExtrawork2019', [MigrationController::class, 'migrateQuestionnaireExtrawork2019']);
Route::get('/migrateQuestionnaireStress2019', [MigrationController::class, 'migrateQuestionnaireStress2019']);

Route::get('/migrateEmployees2020', [MigrationController::class, 'migrateEmployees2020']);
Route::get('/migrateQuestionnaireA2020', [MigrationController::class, 'migrateQuestionnaireA2020']);
Route::get('/migrateQuestionnaireB2020', [MigrationController::class, 'migrateQuestionnaireB2020']);
Route::get('/migrateQuestionnaireGeneralData2020', [MigrationController::class, 'migrateQuestionnaireGeneralData2020']);
Route::get('/migrateQuestionnaireExtrawork2020', [MigrationController::class, 'migrateQuestionnaireExtrawork2020']);
Route::get('/migrateQuestionnaireStress2020', [MigrationController::class, 'migrateQuestionnaireStress2020']);

Route::get('/migrateEmployees2021', [MigrationController::class, 'migrateEmployees2021']);
Route::get('/migrateQuestionnaireA2021', [MigrationController::class, 'migrateQuestionnaireA2021']);
Route::get('/migrateQuestionnaireB2021', [MigrationController::class, 'migrateQuestionnaireB2021']);
Route::get('/migrateQuestionnaireGeneralData2021', [MigrationController::class, 'migrateQuestionnaireGeneralData2021']);
Route::get('/migrateQuestionnaireExtrawork2021', [MigrationController::class, 'migrateQuestionnaireExtrawork2021']);
Route::get('/migrateQuestionnaireStress2021', [MigrationController::class, 'migrateQuestionnaireStress2021']);

Route::get('/migrateEmployees2022', [MigrationController::class, 'migrateEmployees2022']);
Route::get('/migrateQuestionnaireA2022', [MigrationController::class, 'migrateQuestionnaireA2022']);
Route::get('/migrateQuestionnaireB2022', [MigrationController::class, 'migrateQuestionnaireB2022']);
Route::get('/migrateQuestionnaireGeneralData2022', [MigrationController::class, 'migrateQuestionnaireGeneralData2022']);
Route::get('/migrateQuestionnaireExtrawork2022', [MigrationController::class, 'migrateQuestionnaireExtrawork2022']);
Route::get('/migrateQuestionnaireStress2022', [MigrationController::class, 'migrateQuestionnaireStress2022']);

Route::get('/migrateEmployees2023', [MigrationController::class, 'migrateEmployees2023']);
Route::get('/migrateQuestionnaireA2023', [MigrationController::class, 'migrateQuestionnaireA2023']);
Route::get('/migrateQuestionnaireB2023', [MigrationController::class, 'migrateQuestionnaireB2023']);
Route::get('/migrateQuestionnaireGeneralData2023', [MigrationController::class, 'migrateQuestionnaireGeneralData2023']);
Route::get('/migrateQuestionnaireExtrawork2023', [MigrationController::class, 'migrateQuestionnaireExtrawork2023']);
Route::get('/migrateQuestionnaireStress2023', [MigrationController::class, 'migrateQuestionnaireStress2023']);

Route::get('/migrateEmployees2024', [MigrationController::class, 'migrateEmployees2024']);
Route::get('/migrateQuestionnaireA2024', [MigrationController::class, 'migrateQuestionnaireA2024']);
Route::get('/migrateQuestionnaireB2024', [MigrationController::class, 'migrateQuestionnaireB2024']);
Route::get('/migrateQuestionnaireGeneralData2024', [MigrationController::class, 'migrateQuestionnaireGeneralData2024']);
Route::get('/migrateQuestionnaireExtrawork2024', [MigrationController::class, 'migrateQuestionnaireExtrawork2024']);
Route::get('/migrateQuestionnaireStress2024', [MigrationController::class, 'migrateQuestionnaireStress2024']);
