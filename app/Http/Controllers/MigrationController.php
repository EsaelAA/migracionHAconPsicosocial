<?php

namespace App\Http\Controllers;

use App\Models\caliextralaborales;
use App\Models\caliriesgopsicosocialparte1;
use App\Models\caliriesgopsicosocialparte2;
use App\Models\Company;
use App\Models\CompanyPlatform;
use App\Models\Empleados2011;
use App\Models\Employees;
use App\Models\Extrawork;
use App\Models\fichadatosgenerales;
use App\Models\GeneralData;
use App\Models\IntraWorkA;
use App\Models\IntraWorkB;
use App\Models\MeasurementCompanies;
use App\Models\Measurements;
use App\Models\Questionnaires;
use App\Models\Results;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrationController extends Controller
{
    public function migrateEmployees()
    {
        set_time_limit(12000000);

        $Empleados2011 = Empleados2011::all();

        foreach ($Empleados2011 as $empleado2011) {
            if ($empleado2011->empresa == "") {
                continue;
            }

            $companyName = Company::where('company_nit', '=', $empleado2011->nit)->first();

            $measurementName = "MEDICIÓN $companyName->company_name";

            $ifExistsMeasurement = Measurements::where('measurement_name', '=', $measurementName)->first();

            $measurementId = "";
            if (!$ifExistsMeasurement) {
                $measurement = Measurements::create([
                    'measurement_name' => mb_strtoupper($measurementName, 'UTF-8'),
                    'measurement_year' => "2011",
                    'state' => 0,
                    'start_date' => '2011-01-01 07:30:00',
                    'end_date' => '2011-12-31 12:35:00'
                ]);

                MeasurementCompanies::create([
                    'measurement_id' => $measurement->measurement_id,
                    'company_id' => $companyName->company_id,
                ]);

                CompanyPlatform::create([
                    'platform_id' => 2,
                    'company_id' => $companyName->company_id
                ]);

                $measurementId = $measurement->measurement_id;
            } else {
                $measurementId = $ifExistsMeasurement->measurement_id;
            }

            $isEmployeeExists = Employees::where('document_employee', '=', $empleado2011->cc)
                ->where('measurement_id', '=', $measurementId)
                ->first();

            if ($isEmployeeExists) {
                continue;
            }

            $positionType = "Profesional, analista, técnico, tecnólogo";
            $fichadatosgenerales = fichadatosgenerales::where('cc', '=', $empleado2011->cc)->first();

            if ($fichadatosgenerales) {
                $positionType = $fichadatosgenerales->tipodecargo;
            }

            $Employees = Employees::create([
                'company_id' => $companyName->company_id,
                'measurement_id' => $measurementId,
                'city' => mb_strtoupper($empleado2011->regional, 'UTF-8'),
                'document_employee' => $empleado2011->cc,
                'first_name' => mb_strtoupper($empleado2011->nombres),
                'last_name' => mb_strtoupper($empleado2011->apellidos),
                'position' => mb_strtoupper($empleado2011->cargo),
                'position_type' => $positionType,
                'email' => $empleado2011->email,
                'username' => "CERRADO-2011",
                'password' => "CERRADO-2011",
                'first_level' => mb_strtoupper($empleado2011->oficina),
                'second_level' => mb_strtoupper($empleado2011->sede),
                'third_level' => mb_strtoupper($empleado2011->ciclos),
                'fourth_level' => "",
                'fifth_level' => "",
                'sixth_level' => "",
                'seventh_level' => "",
                'eighth_level' => "",
                'state' => 0
            ]);

            $intralaboralState = "";
            if ($empleado2011->notas == 'A') {
                $intralaboralState = $empleado2011->pruebaintralaboralA;
            } else {
                $intralaboralState = $empleado2011->pruebaintralaboralB;
            }

            $Questionnaires = Questionnaires::create([
                'measurement_id' => $measurementId,
                'employee_id' => $Employees->employee_id,
                'type_questionarie' => $empleado2011->notas,
                'state_crafft' => "",
                'state_weather' => $empleado2011->pruebaclima,
                'state_copping' => "",
                'state_intrawork' => $intralaboralState,
                'state_extrawork' => $empleado2011->pruebaextralaboral,
                'state_general_data' => $empleado2011->pruebadatos,
                'state_stress' => $empleado2011->pruebaestres
            ]);
        }
    }

    public function migrateQuestionnaireA()
    {
        $Caliriesgo1 = caliriesgopsicosocialparte1::all();

        foreach ($Caliriesgo1 as $answerOld) {
            $Employees = Employees::where('document_employee', '=', $answerOld->cc)
                ->join('psychosocial_questionnaires', 'psychosocial_questionnaires.employee_id', '=', 'psychosocial_employees.employee_id')
                ->first();

            if (!$Employees) {
                continue;
            }

            $fechaOriginal = $answerOld->fechaaplicacion;
            $fechaFormateada = DateTime::createFromFormat('d/m/Y', $fechaOriginal)->format('Y-m-d');

            $data = [
                "questionnaire_id" => $Employees->questionnaire_id,
                "response_date" => $fechaFormateada,
                "answer_1" => $answerOld->r1,
                "answer_2" => $answerOld->r2,
                "answer_3" => $answerOld->r3,
                "answer_4" => $answerOld->r4,
                "answer_5" => $answerOld->r5,
                "answer_6" => $answerOld->r6,
                "answer_7" => $answerOld->r7,
                "answer_8" => $answerOld->r8,
                "answer_9" => $answerOld->r9,
                "answer_10" => $answerOld->r10,
                "answer_11" => $answerOld->r11,
                "answer_12" => $answerOld->r12,
                "answer_13" => $answerOld->r13,
                "answer_14" => $answerOld->r14,
                "answer_15" => $answerOld->r15,
                "answer_16" => $answerOld->r16,
                "answer_17" => $answerOld->r17,
                "answer_18" => $answerOld->r18,
                "answer_19" => $answerOld->r19,
                "answer_20" => $answerOld->r20,
                "answer_21" => $answerOld->r21,
                "answer_22" => $answerOld->r22,
                "answer_23" => $answerOld->r23,
                "answer_24" => $answerOld->r24,
                "answer_25" => $answerOld->r25,
                "answer_26" => $answerOld->r26,
                "answer_27" => $answerOld->r27,
                "answer_28" => $answerOld->r28,
                "answer_29" => $answerOld->r29,
                "answer_30" => $answerOld->r30,
                "answer_31" => $answerOld->r31,
                "answer_32" => $answerOld->r32,
                "answer_33" => $answerOld->r33,
                "answer_34" => $answerOld->r34,
                "answer_35" => $answerOld->r35,
                "answer_36" => $answerOld->r36,
                "answer_37" => $answerOld->r37,
                "answer_38" => $answerOld->r38,
                "answer_39" => $answerOld->r39,
                "answer_40" => $answerOld->r40,
                "answer_41" => $answerOld->r41,
                "answer_42" => $answerOld->r42,
                "answer_43" => $answerOld->r43,
                "answer_44" => $answerOld->r44,
                "answer_45" => $answerOld->r45,
                "answer_46" => $answerOld->r46,
                "answer_47" => $answerOld->r47,
                "answer_48" => $answerOld->r48,
                "answer_49" => $answerOld->r49,
                "answer_50" => $answerOld->r50,
                "answer_51" => $answerOld->r51,
                "answer_52" => $answerOld->r52,
                "answer_53" => $answerOld->r53,
                "answer_54" => $answerOld->r54,
                "answer_55" => $answerOld->r55,
                "answer_56" => $answerOld->r56,
                "answer_57" => $answerOld->r57,
                "answer_58" => $answerOld->r58,
                "answer_59" => $answerOld->r59,
                "answer_60" => $answerOld->r60,
                "answer_61" => $answerOld->r61,
                "answer_62" => $answerOld->r62,
                "answer_63" => $answerOld->r63,
                "answer_64" => $answerOld->r64,
                "answer_65" => $answerOld->r65,
                "answer_66" => $answerOld->r66,
                "answer_67" => $answerOld->r67,
                "answer_68" => $answerOld->r68,
                "answer_69" => $answerOld->r69,
                "answer_70" => $answerOld->r70,
                "answer_71" => $answerOld->r71,
                "answer_72" => $answerOld->r72,
                "answer_73" => $answerOld->r73,
                "answer_74" => $answerOld->r74,
                "answer_75" => $answerOld->r75,
                "answer_76" => $answerOld->r76,
                "answer_77" => $answerOld->r77,
                "answer_78" => $answerOld->r78,
                "answer_79" => $answerOld->r79,
                "answer_80" => $answerOld->r80,
                "answer_81" => $answerOld->r81,
                "answer_82" => $answerOld->r82,
                "answer_83" => $answerOld->r83,
                "answer_84" => $answerOld->r84,
                "answer_85" => $answerOld->r85,
                "answer_86" => $answerOld->r86,
                "answer_87" => $answerOld->r87,
                "answer_88" => $answerOld->r88,
                "answer_89" => $answerOld->r89,
                "answer_90" => $answerOld->r90,
                "answer_91" => $answerOld->r91,
                "answer_92" => $answerOld->r92,
                "answer_93" => $answerOld->r93,
                "answer_94" => $answerOld->r94,
                "answer_95" => $answerOld->r95,
                "answer_96" => $answerOld->r96,
                "answer_97" => $answerOld->r97,
                "answer_98" => $answerOld->r98,
                "answer_99" => $answerOld->r99,
                "answer_100" => $answerOld->r100,
                "answer_101" => $answerOld->r101,
                "answer_102" => $answerOld->r102,
                "answer_103" => $answerOld->r103,
                "answer_104" => $answerOld->r104,
                "answer_105" => $answerOld->r105,
                "answer_106" => $answerOld->r106,
                "answer_107" => $answerOld->r107,
                "answer_108" => $answerOld->r108,
                "answer_109" => $answerOld->r109,
                "answer_110" => $answerOld->r110,
                "answer_111" => $answerOld->r111,
                "answer_112" => $answerOld->r112,
                "answer_113" => $answerOld->r113,
                "answer_114" => $answerOld->r114,
                "answer_115" => $answerOld->r115,
                "answer_116" => $answerOld->r116,
                "answer_117" => $answerOld->r117,
                "answer_118" => $answerOld->r118,
                "answer_119" => $answerOld->r119,
                "answer_120" => $answerOld->r120,
                "answer_121" => $answerOld->r121,
                "answer_122" => $answerOld->r122,
                "answer_123" => $answerOld->r123,
            ];


            $dataIntrawork = new Request();
            $dataIntrawork->replace($data);

            $this->registerIntraWorkAUser($dataIntrawork);
        }
    }

    public function registerIntraWorkAUser(Request $request)
    {
        $QuestionnaireExits = Questionnaires::where('questionnaire_id', $request->questionnaire_id)->first();

        if (!$QuestionnaireExits) {
            return;
        }

        $dimensionCharacteristics = null;
        $dimensionRelations = null;
        $dimensionFeedback = null;
        $dimensionRelationShip = null;
        $dimensionClarity = null;
        $dimensionTraining = null;
        $dimensionStake = null;
        $dimensionOpportunities = null;
        $dimensionControl = null;
        $dimensionEnviromentalDemands = null;
        $dimensionEmotionalDemands = null;
        $dimensionQuantitativeDemands = null;
        $dimensionWorkInfluence = null;
        $dimensionRequirements = null;
        $dimensionMentalLoadDemands = null;
        $dimensionConsistency = null;
        $dimensionDayDemands = null;
        $dimensionRewardsWork = null;
        $dimensionRecognition = null;

        for ($i = 1; $i <= 123; $i++) {

            if (
                $request["answer_$i"] == 'no aplica'
                || $request["answer_$i"] == 'no responde'
                || $request["answer_$i"] == 'no respondio'
            ) {
                $request["answer_$i"] = '';
                continue;
            }

            // DOMINIO DE LIDERAZGO Y RELACIONES CON LOS COLABORADORES
            if (
                $i == 63
                || $i == 64
                || $i == 65
                || $i == 66
                || $i == 67
                || $i == 68
                || $i == 69
                || $i == 70
                || $i == 71
                || $i == 72
                || $i == 73
                || $i == 74
                || $i == 75
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionCharacteristics += $request["answer_$i"];
                }
            }

            if (
                $i == 76
                || $i == 77
                || $i == 78
                || $i == 79
                || $i == 80
                || $i == 81
                || $i == 82
                || $i == 83
                || $i == 84
                || $i == 85
                || $i == 86
                || $i == 87
                || $i == 88
                || $i == 89
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionRelations += $request["answer_$i"];
                }
            }

            if (
                $i == 90
                || $i == 91
                || $i == 92
                || $i == 93
                || $i == 94
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionFeedback += $request["answer_$i"];
                }
            }

            if (
                $i == 115
                || $i == 116
                || $i == 117
                || $i == 118
                || $i == 119
                || $i == 120
                || $i == 121
                || $i == 122
                || $i == 123
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionRelationShip += $request["answer_$i"];
                }
            }

            // DOMINIO DE CONTROL SOBRE EL TRABAJO
            if (
                $i == 53
                || $i == 54
                || $i == 55
                || $i == 56
                || $i == 57
                || $i == 58
                || $i == 59
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionClarity += $request["answer_$i"];
                }
            }

            if (
                $i == 60
                || $i == 61
                || $i == 62
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionTraining += $request["answer_$i"];
                }
            }

            if (
                $i == 48
                || $i == 49
                || $i == 50
                || $i == 51
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionStake += $request["answer_$i"];
                }
            }

            if (
                $i == 39
                || $i == 40
                || $i == 41
                || $i == 42
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionOpportunities += $request["answer_$i"];
                }
            }

            if (
                $i == 44
                || $i == 45
                || $i == 46
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionControl += $request["answer_$i"];
                }
            }

            // DOMINIO DEMANDAS DEL TRABAJO
            if (
                $i == 1
                || $i == 2
                || $i == 3
                || $i == 4
                || $i == 5
                || $i == 6
                || $i == 7
                || $i == 8
                || $i == 9
                || $i == 10
                || $i == 11
                || $i == 12
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionEnviromentalDemands += $request["answer_$i"];
                }
            }

            if (
                $i == 106
                || $i == 107
                || $i == 108
                || $i == 109
                || $i == 110
                || $i == 111
                || $i == 112
                || $i == 113
                || $i == 114
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionEmotionalDemands += $request["answer_$i"];
                }
            }

            if (
                $i == 13
                || $i == 14
                || $i == 15
                || $i == 32
                || $i == 43
                || $i == 47
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionQuantitativeDemands += $request["answer_$i"];
                }
            }

            if (
                $i == 35
                || $i == 36
                || $i == 37
                || $i == 38
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionWorkInfluence += $request["answer_$i"];
                }
            }

            if (
                $i == 19
                || $i == 22
                || $i == 23
                || $i == 24
                || $i == 25
                || $i == 26
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionRequirements += $request["answer_$i"];
                }
            }

            if (
                $i == 16
                || $i == 17
                || $i == 18
                || $i == 20
                || $i == 21
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionMentalLoadDemands += $request["answer_$i"];
                }
            }

            if (
                $i == 27
                || $i == 28
                || $i == 29
                || $i == 30
                || $i == 52
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionConsistency += $request["answer_$i"];
                }
            }

            if (
                $i == 31
                || $i == 33
                || $i == 34
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionDayDemands += $request["answer_$i"];
                }
            }

            // DOMINIO DE RECOMPENSAS
            if (
                $i == 95
                || $i == 102
                || $i == 103
                || $i == 104
                || $i == 105
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionRewardsWork += $request["answer_$i"];
                }
            }

            if (
                $i == 96
                || $i == 97
                || $i == 98
                || $i == 99
                || $i == 100
                || $i == 101
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionRecognition += $request["answer_$i"];
                }
            }
        }

        // CÁLCULOS POR DIMENSIONES
        // LIDERAZGO Y RELACIONES SOCIALES EN EL TRABAJO
        $transformCharacteristics = null;
        $baremoCharacteristics = null;

        if ($dimensionCharacteristics >= 0) {
            $transformCharacteristics = round(($dimensionCharacteristics / 52) * 100, 3);

            if ($transformCharacteristics <= 3.8) {
                $baremoCharacteristics = "Sin riesgo o riesgo despreciable";
            } else if ($transformCharacteristics <= 15.4) {
                $baremoCharacteristics = "Riesgo bajo";
            } else if ($transformCharacteristics <= 30.8) {
                $baremoCharacteristics = "Riesgo medio";
            } else if ($transformCharacteristics <= 46.2) {
                $baremoCharacteristics = "Riesgo alto";
            } else {
                $baremoCharacteristics = "Riesgo muy alto";
            }
        }

        $transformRelations = null;
        $baremoRelations = null;
        if ($dimensionRelations >= 0) {
            $transformRelations = round(($dimensionRelations / 56) * 100, 2);

            if ($transformRelations <= 5.4) {
                $baremoRelations = "Sin riesgo o riesgo despreciable";
            } else if ($transformRelations <= 16.1) {
                $baremoRelations = "Riesgo bajo";
            } else if ($transformRelations <= 25) {
                $baremoRelations = "Riesgo medio";
            } else if ($transformRelations <= 37.5) {
                $baremoRelations = "Riesgo alto";
            } else {
                $baremoRelations = "Riesgo muy alto";
            }
        }

        $transformFeedback = null;
        $baremoFeedback = null;
        if ($dimensionFeedback >= 0) {
            $transformFeedback = round(($dimensionFeedback / 20) * 100, 2);

            if ($transformFeedback <= 10) {
                $baremoFeedback = "Sin riesgo o riesgo despreciable";
            } else if ($transformFeedback <= 25) {
                $baremoFeedback = "Riesgo bajo";
            } else if ($transformFeedback <= 40) {
                $baremoFeedback = "Riesgo medio";
            } else if ($transformFeedback <= 55) {
                $baremoFeedback = "Riesgo alto";
            } else {
                $baremoFeedback = "Riesgo muy alto";
            }
        }

        $transformRelationShip = null;
        $baremoRelationShip = null;
        if ($dimensionRelationShip >= 0) {
            $transformRelationShip = round(($dimensionRelationShip / 36) * 100, 2);

            if ($transformRelationShip <= 13.9) {
                $baremoRelationShip = "Sin riesgo o riesgo despreciable";
            } else if ($transformRelationShip <= 25) {
                $baremoRelationShip = "Riesgo bajo";
            } else if ($transformRelationShip <= 33.3) {
                $baremoRelationShip = "Riesgo medio";
            } else if ($transformRelationShip <= 47.2) {
                $baremoRelationShip = "Riesgo alto";
            } else {
                $baremoRelationShip = "Riesgo muy alto";
            }
        }

        // CONTROL SOBRE EL TRABAJO
        $transformClarity = null;
        $baremoClarity = null;
        if ($dimensionClarity >= 0) {
            $transformClarity = round(($dimensionClarity / 28) * 100, 2);

            if ($transformClarity <= 0.9) {
                $baremoClarity = "Sin riesgo o riesgo despreciable";
            } else if ($transformClarity <= 10.7) {
                $baremoClarity = "Riesgo bajo";
            } else if ($transformClarity <= 21.4) {
                $baremoClarity = "Riesgo medio";
            } else if ($transformClarity <= 39.3) {
                $baremoClarity = "Riesgo alto";
            } else {
                $baremoClarity = "Riesgo muy alto";
            }
        }

        $transformTraining = null;
        $baremoTraining = null;
        if ($dimensionTraining >= 0) {
            $transformTraining = round(($dimensionTraining / 12) * 100, 2);

            if ($transformTraining <= 0.9) {
                $baremoTraining = "Sin riesgo o riesgo despreciable";
            } else if ($transformTraining <= 16.7) {
                $baremoTraining = "Riesgo bajo";
            } else if ($transformTraining <= 33.3) {
                $baremoTraining = "Riesgo medio";
            } else if ($transformTraining <= 50) {
                $baremoTraining = "Riesgo alto";
            } else {
                $baremoTraining = "Riesgo muy alto";
            }
        }

        $transformStake = null;
        $baremoStake = null;
        if ($dimensionStake >= 0) {
            $transformStake = round(($dimensionStake / 16) * 100, 2);

            if ($transformStake <= 12.5) {
                $baremoStake = "Sin riesgo o riesgo despreciable";
            } else if ($transformStake <= 25) {
                $baremoStake = "Riesgo bajo";
            } else if ($transformStake <= 37.5) {
                $baremoStake = "Riesgo medio";
            } else if ($transformStake <= 50) {
                $baremoStake = "Riesgo alto";
            } else {
                $baremoStake = "Riesgo muy alto";
            }
        }

        $transformOpportunities = null;
        $baremoOpportunities = null;
        if ($dimensionOpportunities >= 0) {
            $transformOpportunities = round(($dimensionOpportunities / 16) * 100, 2);

            if ($transformOpportunities <= 0.9) {
                $baremoOpportunities = "Sin riesgo o riesgo despreciable";
            } else if ($transformOpportunities <= 6.3) {
                $baremoOpportunities = "Riesgo bajo";
            } else if ($transformOpportunities <= 18.8) {
                $baremoOpportunities = "Riesgo medio";
            } else if ($transformOpportunities <= 31.3) {
                $baremoOpportunities = "Riesgo alto";
            } else {
                $baremoOpportunities = "Riesgo muy alto";
            }
        }

        $transformControl = null;
        $baremoControl = null;
        if ($dimensionControl >= 0) {
            $transformControl = round(($dimensionControl / 12) * 100, 2);

            if ($transformControl <= 8.3) {
                $baremoControl = "Sin riesgo o riesgo despreciable";
            } else if ($transformControl <= 25) {
                $baremoControl = "Riesgo bajo";
            } else if ($transformControl <= 41.7) {
                $baremoControl = "Riesgo medio";
            } else if ($transformControl <= 58.3) {
                $baremoControl = "Riesgo alto";
            } else {
                $baremoControl = "Riesgo muy alto";
            }
        }

        // DEMANDAS DEL TRABAJO
        $transformEnviromentalDemands = null;
        $baremoEnviromentalDemands = null;
        if ($dimensionEnviromentalDemands >= 0) {
            $transformEnviromentalDemands = round(($dimensionEnviromentalDemands / 48) * 100, 2);

            if ($transformEnviromentalDemands <= 14.6) {
                $baremoEnviromentalDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformEnviromentalDemands <= 22.9) {
                $baremoEnviromentalDemands = "Riesgo bajo";
            } else if ($transformEnviromentalDemands <= 31.3) {
                $baremoEnviromentalDemands = "Riesgo medio";
            } else if ($transformEnviromentalDemands <= 39.6) {
                $baremoEnviromentalDemands = "Riesgo alto";
            } else {
                $baremoEnviromentalDemands = "Riesgo muy alto";
            }
        }

        $transformEmotionalDemands = null;
        $baremoEmotionalDemands = null;
        if ($dimensionEmotionalDemands >= 0) {
            $transformEmotionalDemands = round(($dimensionEmotionalDemands / 36) * 100, 2);

            if ($transformEmotionalDemands <= 16.7) {
                $baremoEmotionalDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformEmotionalDemands <= 25) {
                $baremoEmotionalDemands = "Riesgo bajo";
            } else if ($transformEmotionalDemands <= 33.3) {
                $baremoEmotionalDemands = "Riesgo medio";
            } else if ($transformEmotionalDemands <= 47.2) {
                $baremoEmotionalDemands = "Riesgo alto";
            } else {
                $baremoEmotionalDemands = "Riesgo muy alto";
            }
        }

        $transformQuantitativeDemands = null;
        $baremoQuantitativeDemands = null;
        if ($dimensionQuantitativeDemands >= 0) {
            $transformQuantitativeDemands = round(($dimensionQuantitativeDemands / 24) * 100, 2);

            if ($transformQuantitativeDemands <= 25) {
                $baremoQuantitativeDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformQuantitativeDemands <= 33.3) {
                $baremoQuantitativeDemands = "Riesgo bajo";
            } else if ($transformQuantitativeDemands <= 45.8) {
                $baremoQuantitativeDemands = "Riesgo medio";
            } else if ($transformQuantitativeDemands <= 54.2) {
                $baremoQuantitativeDemands = "Riesgo alto";
            } else {
                $baremoQuantitativeDemands = "Riesgo muy alto";
            }
        }

        $transformWorkInfluence = null;
        $baremoWorkInfluence = null;
        if ($dimensionWorkInfluence >= 0) {
            $transformWorkInfluence = round(($dimensionWorkInfluence / 16) * 100, 2);

            if ($transformWorkInfluence <= 18.8) {
                $baremoWorkInfluence = "Sin riesgo o riesgo despreciable";
            } else if ($transformWorkInfluence <= 31.3) {
                $baremoWorkInfluence = "Riesgo bajo";
            } else if ($transformWorkInfluence <= 43.8) {
                $baremoWorkInfluence = "Riesgo medio";
            } else if ($transformWorkInfluence <= 50) {
                $baremoWorkInfluence = "Riesgo alto";
            } else {
                $baremoWorkInfluence = "Riesgo muy alto";
            }
        }

        $transformRequirements = null;
        $baremoRequirements = null;
        if ($dimensionRequirements >= 0) {
            $transformRequirements = round(($dimensionRequirements / 24) * 100, 2);

            if ($transformRequirements <= 37.5) {
                $baremoRequirements = "Sin riesgo o riesgo despreciable";
            } else if ($transformRequirements <= 54.2) {
                $baremoRequirements = "Riesgo bajo";
            } else if ($transformRequirements <= 66.7) {
                $baremoRequirements = "Riesgo medio";
            } else if ($transformRequirements <= 79.2) {
                $baremoRequirements = "Riesgo alto";
            } else {
                $baremoRequirements = "Riesgo muy alto";
            }
        }

        $transformMentalLoadDemands = null;
        $baremoMentalLoadDemands = null;
        if ($dimensionMentalLoadDemands >= 0) {
            $transformMentalLoadDemands = round(($dimensionMentalLoadDemands / 20) * 100, 2);

            if ($transformMentalLoadDemands <= 60) {
                $baremoMentalLoadDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformMentalLoadDemands <= 70) {
                $baremoMentalLoadDemands = "Riesgo bajo";
            } else if ($transformMentalLoadDemands <= 80) {
                $baremoMentalLoadDemands = "Riesgo medio";
            } else if ($transformMentalLoadDemands <= 90) {
                $baremoMentalLoadDemands = "Riesgo alto";
            } else {
                $baremoMentalLoadDemands = "Riesgo muy alto";
            }
        }

        $transformConsistency = null;
        $baremoConsistency = null;
        if ($dimensionConsistency >= 0) {
            $transformConsistency = round(($dimensionConsistency / 20) * 100, 2);

            if ($transformConsistency <= 15) {
                $baremoConsistency = "Sin riesgo o riesgo despreciable";
            } else if ($transformConsistency <= 25) {
                $baremoConsistency = "Riesgo bajo";
            } else if ($transformConsistency <= 35) {
                $baremoConsistency = "Riesgo medio";
            } else if ($transformConsistency <= 45) {
                $baremoConsistency = "Riesgo alto";
            } else {
                $baremoConsistency = "Riesgo muy alto";
            }
        }

        $transformDayDemands = null;
        $baremoDayDemands = null;
        if ($dimensionDayDemands >= 0) {
            $transformDayDemands = round(($dimensionDayDemands / 12) * 100, 2);

            if ($transformDayDemands <= 8.3) {
                $baremoDayDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformDayDemands <= 25) {
                $baremoDayDemands = "Riesgo bajo";
            } else if ($transformDayDemands <= 33.3) {
                $baremoDayDemands = "Riesgo medio";
            } else if ($transformDayDemands <= 50) {
                $baremoDayDemands = "Riesgo alto";
            } else {
                $baremoDayDemands = "Riesgo muy alto";
            }
        }

        // RECOMPENSAS
        $transformRewardsWork = null;
        $baremoRewardsWork = null;
        if ($dimensionRewardsWork >= 0) {
            $transformRewardsWork = round(($dimensionRewardsWork / 20) * 100, 2);

            if ($transformRewardsWork <= 0.9) {
                $baremoRewardsWork = "Sin riesgo o riesgo despreciable";
            } else if ($transformRewardsWork <= 5) {
                $baremoRewardsWork = "Riesgo bajo";
            } else if ($transformRewardsWork <= 10) {
                $baremoRewardsWork = "Riesgo medio";
            } else if ($transformRewardsWork <= 20) {
                $baremoRewardsWork = "Riesgo alto";
            } else {
                $baremoRewardsWork = "Riesgo muy alto";
            }
        }

        $transformRecognition = null;
        $baremoRecognition = null;
        if ($dimensionRecognition >= 0) {
            $transformRecognition = round(($dimensionRecognition / 24) * 100, 2);

            if ($transformRecognition <= 4.2) {
                $baremoRecognition = "Sin riesgo o riesgo despreciable";
            } else if ($transformRecognition <= 16.7) {
                $baremoRecognition = "Riesgo bajo";
            } else if ($transformRecognition <= 25) {
                $baremoRecognition = "Riesgo medio";
            } else if ($transformRecognition <= 37.5) {
                $baremoRecognition = "Riesgo alto";
            } else {
                $baremoRecognition = "Riesgo muy alto";
            }
        }

        // DOMINIOS
        $domainLeaderShip = $dimensionCharacteristics + $dimensionRelations + $dimensionFeedback + $dimensionRelationShip;
        $transformLeaderShip = null;
        $baremoLeaderShip = null;
        if ($domainLeaderShip >= 0) {
            $transformLeaderShip = round(($domainLeaderShip / 164) * 100, 2);

            if ($transformLeaderShip <= 9.1) {
                $baremoLeaderShip = "Sin riesgo o riesgo despreciable";
            } else if ($transformLeaderShip <= 17.7) {
                $baremoLeaderShip = "Riesgo bajo";
            } else if ($transformLeaderShip <= 25.6) {
                $baremoLeaderShip = "Riesgo medio";
            } else if ($transformLeaderShip <= 34.8) {
                $baremoLeaderShip = "Riesgo alto";
            } else {
                $baremoLeaderShip = "Riesgo muy alto";
            }
        }

        $domainWorkControl = $dimensionClarity + $dimensionTraining + $dimensionStake + $dimensionOpportunities + $dimensionControl;
        $transformWorkControl = null;
        $baremoWorkControl = null;
        if ($domainWorkControl >= 0) {
            $transformWorkControl = round(($domainWorkControl / 84) * 100, 2);

            if ($transformWorkControl <= 10.7) {
                $baremoWorkControl = "Sin riesgo o riesgo despreciable";
            } else if ($transformWorkControl <= 19) {
                $baremoWorkControl = "Riesgo bajo";
            } else if ($transformWorkControl <= 29.8) {
                $baremoWorkControl = "Riesgo medio";
            } else if ($transformWorkControl <= 40.5) {
                $baremoWorkControl = "Riesgo alto";
            } else {
                $baremoWorkControl = "Riesgo muy alto";
            }
        }

        $domainWorkDemands = $dimensionEnviromentalDemands + $dimensionEmotionalDemands + $dimensionQuantitativeDemands + $dimensionWorkInfluence + $dimensionRequirements + $dimensionMentalLoadDemands + $dimensionConsistency + $dimensionDayDemands;
        $transformWorkDemands = null;
        $baremoWorkDemands = null;
        if ($domainWorkDemands >= 0) {
            $transformWorkDemands = round(($domainWorkDemands / 200) * 100, 2);

            if ($transformWorkDemands <= 28.5) {
                $baremoWorkDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformWorkDemands <= 35) {
                $baremoWorkDemands = "Riesgo bajo";
            } else if ($transformWorkDemands <= 41.5) {
                $baremoWorkDemands = "Riesgo medio";
            } else if ($transformWorkDemands <= 47.5) {
                $baremoWorkDemands = "Riesgo alto";
            } else {
                $baremoWorkDemands = "Riesgo muy alto";
            }
        }

        $domainRewards = $dimensionRewardsWork + $dimensionRecognition;
        $transformRewards = null;
        $baremoRewards = null;
        if ($domainRewards >= 0) {
            $transformRewards = round(($domainRewards / 44) * 100, 2);

            if ($transformRewards <= 4.5) {
                $baremoRewards = "Sin riesgo o riesgo despreciable";
            } else if ($transformRewards <= 11.4) {
                $baremoRewards = "Riesgo bajo";
            } else if ($transformRewards <= 20.5) {
                $baremoRewards = "Riesgo medio";
            } else if ($transformRewards <= 29.5) {
                $baremoRewards = "Riesgo alto";
            } else {
                $baremoRewards = "Riesgo muy alto";
            }
        }

        $generalIntralaboral = $domainLeaderShip + $domainWorkControl + $domainWorkDemands + $domainRewards;
        $trasnformGeneralIntralaboral = null;
        $baremoGeneralIntralaboral = null;

        if ($generalIntralaboral >= 0) {
            $trasnformGeneralIntralaboral = round(($generalIntralaboral / 492) * 100, 2);

            if ($trasnformGeneralIntralaboral <= 19.7) {
                $baremoGeneralIntralaboral = "Sin riesgo o riesgo despreciable";
            } else if ($trasnformGeneralIntralaboral <= 25.9) {
                $baremoGeneralIntralaboral = "Riesgo bajo";
            } else if ($trasnformGeneralIntralaboral <= 31.6) {
                $baremoGeneralIntralaboral = "Riesgo medio";
            } else if ($trasnformGeneralIntralaboral <= 38.2) {
                $baremoGeneralIntralaboral = "Riesgo alto";
            } else {
                $baremoGeneralIntralaboral = "Riesgo muy alto";
            }
        }

        $updateData = [
            'response_date' => $request->response_date,
            'answer_1' => $request->answer_1,
            'answer_2' => $request->answer_2,
            'answer_3' => $request->answer_3,
            'answer_4' => $request->answer_4,
            'answer_5' => $request->answer_5,
            'answer_6' => $request->answer_6,
            'answer_7' => $request->answer_7,
            'answer_8' => $request->answer_8,
            'answer_9' => $request->answer_9,
            'answer_10' => $request->answer_10,
            'answer_11' => $request->answer_11,
            'answer_12' => $request->answer_12,
            'answer_13' => $request->answer_13,
            'answer_14' => $request->answer_14,
            'answer_15' => $request->answer_15,
            'answer_16' => $request->answer_16,
            'answer_17' => $request->answer_17,
            'answer_18' => $request->answer_18,
            'answer_19' => $request->answer_19,
            'answer_20' => $request->answer_20,
            'answer_21' => $request->answer_21,
            'answer_22' => $request->answer_22,
            'answer_23' => $request->answer_23,
            'answer_24' => $request->answer_24,
            'answer_25' => $request->answer_25,
            'answer_26' => $request->answer_26,
            'answer_27' => $request->answer_27,
            'answer_28' => $request->answer_28,
            'answer_29' => $request->answer_29,
            'answer_30' => $request->answer_30,
            'answer_31' => $request->answer_31,
            'answer_32' => $request->answer_32,
            'answer_33' => $request->answer_33,
            'answer_34' => $request->answer_34,
            'answer_35' => $request->answer_35,
            'answer_36' => $request->answer_36,
            'answer_37' => $request->answer_37,
            'answer_38' => $request->answer_38,
            'answer_39' => $request->answer_39,
            'answer_40' => $request->answer_40,
            'answer_41' => $request->answer_41,
            'answer_42' => $request->answer_42,
            'answer_43' => $request->answer_43,
            'answer_44' => $request->answer_44,
            'answer_45' => $request->answer_45,
            'answer_46' => $request->answer_46,
            'answer_47' => $request->answer_47,
            'answer_48' => $request->answer_48,
            'answer_49' => $request->answer_49,
            'answer_50' => $request->answer_50,
            'answer_51' => $request->answer_51,
            'answer_52' => $request->answer_52,
            'answer_53' => $request->answer_53,
            'answer_54' => $request->answer_54,
            'answer_55' => $request->answer_55,
            'answer_56' => $request->answer_56,
            'answer_57' => $request->answer_57,
            'answer_58' => $request->answer_58,
            'answer_59' => $request->answer_59,
            'answer_60' => $request->answer_60,
            'answer_61' => $request->answer_61,
            'answer_62' => $request->answer_62,
            'answer_63' => $request->answer_63,
            'answer_64' => $request->answer_64,
            'answer_65' => $request->answer_65,
            'answer_66' => $request->answer_66,
            'answer_67' => $request->answer_67,
            'answer_68' => $request->answer_68,
            'answer_69' => $request->answer_69,
            'answer_70' => $request->answer_70,
            'answer_71' => $request->answer_71,
            'answer_72' => $request->answer_72,
            'answer_73' => $request->answer_73,
            'answer_74' => $request->answer_74,
            'answer_75' => $request->answer_75,
            'answer_76' => $request->answer_76,
            'answer_77' => $request->answer_77,
            'answer_78' => $request->answer_78,
            'answer_79' => $request->answer_79,
            'answer_80' => $request->answer_80,
            'answer_81' => $request->answer_81,
            'answer_82' => $request->answer_82,
            'answer_83' => $request->answer_83,
            'answer_84' => $request->answer_84,
            'answer_85' => $request->answer_85,
            'answer_86' => $request->answer_86,
            'answer_87' => $request->answer_87,
            'answer_88' => $request->answer_88,
            'answer_89' => $request->answer_89,
            'answer_90' => $request->answer_90,
            'answer_91' => $request->answer_91,
            'answer_92' => $request->answer_92,
            'answer_93' => $request->answer_93,
            'answer_94' => $request->answer_94,
            'answer_95' => $request->answer_95,
            'answer_96' => $request->answer_96,
            'answer_97' => $request->answer_97,
            'answer_98' => $request->answer_98,
            'answer_99' => $request->answer_99,
            'answer_100' => $request->answer_100,
            'answer_101' => $request->answer_101,
            'answer_102' => $request->answer_102,
            'answer_103' => $request->answer_103,
            'answer_104' => $request->answer_104,
            'answer_105' => $request->answer_105,
            'answer_106' => $request->answer_106,
            'answer_107' => $request->answer_107,
            'answer_108' => $request->answer_108,
            'answer_109' => $request->answer_109,
            'answer_110' => $request->answer_110,
            'answer_111' => $request->answer_111,
            'answer_112' => $request->answer_112,
            'answer_113' => $request->answer_113,
            'answer_114' => $request->answer_114,
            'answer_115' => $request->answer_115,
            'answer_116' => $request->answer_116,
            'answer_117' => $request->answer_117,
            'answer_118' => $request->answer_118,
            'answer_119' => $request->answer_119,
            'answer_120' => $request->answer_120,
            'answer_121' => $request->answer_121,
            'answer_122' => $request->answer_122,
            'answer_123' => $request->answer_123,
        ];

        $answersIntraWork = IntraWorkA::updateOrCreate(
            ['questionnaire_id' => $request->questionnaire_id],
            $updateData
        );

        $measurement_id = $QuestionnaireExits->measurement_id;
        $questionnaireType = $QuestionnaireExits->type_questionarie;


        $EmployeeLevels = new Employees();
        $resultExistsEmployee = $EmployeeLevels
            ->join('psychosocial_questionnaires', 'psychosocial_employees.employee_id', '=', 'psychosocial_questionnaires.employee_id')
            ->where('psychosocial_questionnaires.questionnaire_id', $request->questionnaire_id)
            ->select('psychosocial_employees.*')
            ->first();

        $Results = new Results();
        $resultExists = $Results
            ->where('psychosocial_results.questionnaire_id', $request->questionnaire_id)
            ->where('psychosocial_results.measurement_id', $measurement_id)
            ->where('psychosocial_results.type_questionnaire', $questionnaireType)
            ->first();

        if ($resultExists != null) {
            // SI YA ESTÁ EL REGISTRO, SE DEBE ACTUALIZAR LOS DATOS
            $insertResults = $resultExists;

            // SI HAY DATOS EN EXTRALABORAL, SE DEBE ACTUALIZAR EL INTRA-EXTRA
            if ($insertResults->extrawork_raw_results_general != null) {
                $generalIntraworkExtrawork = $insertResults->extrawork_raw_results_general + $generalIntralaboral;
                $transformIntraExtraWork = round(($generalIntraworkExtrawork / 616) * 100, 2);

                if ($transformIntraExtraWork <= 18.8) {
                    $baremoGeneralIntraExtrawork = "Sin riesgo o riesgo despreciable";
                } else if ($transformIntraExtraWork <= 24.4) {
                    $baremoGeneralIntraExtrawork = "Riesgo bajo";
                } else if ($transformIntraExtraWork <= 29.5) {
                    $baremoGeneralIntraExtrawork = "Riesgo medio";
                } else if ($transformIntraExtraWork <= 35.4) {
                    $baremoGeneralIntraExtrawork = "Riesgo alto";
                } else {
                    $baremoGeneralIntraExtrawork = "Riesgo muy alto";
                }

                if ($insertResults->intrawork_level_results_general == "Riesgo muy alto" && $insertResults->stress_level_results_general == "Muy alto") {
                    $insertResults->results_cap_intra++;
                } elseif ($insertResults->intrawork_level_results_general == "Riesgo muy alto" && $insertResults->stress_level_results_general == "Alto") {
                    $insertResults->results_cap_intra++;
                } elseif ($insertResults->intrawork_level_results_general == "Riesgo alto" && $insertResults->stress_level_results_general == "Muy alto") {
                    $insertResults->results_cap_intra++;
                } elseif ($insertResults->intrawork_level_results_general == "Riesgo alto" && $insertResults->stress_level_results_general == "Alto") {
                    $insertResults->results_cap_intra++;
                }

                if ($insertResults->results_cap_intra == 1 && $insertResults->results_cap_extra == 1 && $insertResults->results_cap_intra_extra == 0) {
                    $insertResults->results_cap_intra_extra++;
                }

                if ($insertResults->results_stress_no_specific == 0) {
                    if ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" &&  $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } else {
                        $insertResults->results_stress_no_specific = 0;
                    }
                }

                $insertResults->general_intrawork_extrawork = $transformIntraExtraWork;
                $insertResults->general_results_intrawork_extrawork = $baremoGeneralIntraExtrawork;
                $insertResults->general_raw_intrawork_extrawork = $generalIntraworkExtrawork;
            }
        } else {
            $insertResults = $Results;
        }

        $insertResults->questionnaire_id = $request->questionnaire_id;
        $insertResults->measurement_id = $measurement_id;
        $insertResults->type_questionnaire = $questionnaireType;

        $insertResults->intrawork_characteristics = $transformCharacteristics;
        $insertResults->intrawork_level_characteristics = $baremoCharacteristics;
        $insertResults->intrawork_raw_characteristics = $dimensionCharacteristics;

        $insertResults->intrawork_relations = $transformRelations;
        $insertResults->intrawork_level_relations = $baremoRelations;
        $insertResults->intrawork_raw_relations = $dimensionRelations;

        $insertResults->intrawork_feedback = $transformFeedback;
        $insertResults->intrawork_level_feedback = $baremoFeedback;
        $insertResults->intrawork_raw_feedback = $dimensionFeedback;

        $insertResults->intrawork_relationship = $transformRelationShip;
        $insertResults->intrawork_level_relationship = $baremoRelationShip;
        $insertResults->intrawork_raw_relationship = $dimensionRelationShip;

        $insertResults->intrawork_leadership = $transformLeaderShip;
        $insertResults->intrawork_level_leadership = $baremoLeaderShip;
        $insertResults->intrawork_raw_leadership = $domainLeaderShip;

        $insertResults->intrawork_clarity = $transformClarity;
        $insertResults->intrawork_level_clarity = $baremoClarity;
        $insertResults->intrawork_raw_clarity = $dimensionClarity;

        $insertResults->intrawork_training = $transformTraining;
        $insertResults->intrawork_level_training = $baremoTraining;
        $insertResults->intrawork_raw_training = $dimensionTraining;

        $insertResults->intrawork_stake = $transformStake;
        $insertResults->intrawork_level_stake = $baremoStake;
        $insertResults->intrawork_raw_stake = $dimensionStake;

        $insertResults->intrawork_opportunities = $transformOpportunities;
        $insertResults->intrawork_level_opportunities = $baremoOpportunities;
        $insertResults->intrawork_raw_opportunities = $dimensionOpportunities;

        $insertResults->intrawork_control = $transformControl;
        $insertResults->intrawork_level_control = $baremoControl;
        $insertResults->intrawork_raw_control = $dimensionControl;

        $insertResults->intrawork_work_control = $transformWorkControl;
        $insertResults->intrawork_level_work_control = $baremoWorkControl;
        $insertResults->intrawork_raw_work_control = $domainWorkControl;

        $insertResults->intrawork_environmental_demands = $transformEnviromentalDemands;
        $insertResults->intrawork_level_environmental_demands = $baremoEnviromentalDemands;
        $insertResults->intrawork_raw_environmental_demands = $dimensionEnviromentalDemands;

        $insertResults->intrawork_emotional_demands = $transformEmotionalDemands;
        $insertResults->intrawork_level_emotional_demands = $baremoEmotionalDemands;
        $insertResults->intrawork_raw_emotional_demands = $dimensionEmotionalDemands;

        $insertResults->intrawork_quantitative_demands = $transformQuantitativeDemands;
        $insertResults->intrawork_level_quantitative_demands = $baremoQuantitativeDemands;
        $insertResults->intrawork_raw_quantitative_demands = $dimensionQuantitativeDemands;

        $insertResults->intrawork_work_influence = $transformWorkInfluence;
        $insertResults->intrawork_level_work_influence = $baremoWorkInfluence;
        $insertResults->intrawork_raw_work_influence = $dimensionWorkInfluence;

        $insertResults->intrawork_requirements = $transformRequirements;
        $insertResults->intrawork_level_requirements = $baremoRequirements;
        $insertResults->intrawork_raw_requirements = $dimensionRequirements;

        $insertResults->intrawork_mental_load_demands = $transformMentalLoadDemands;
        $insertResults->intrawork_level_mental_load_demands = $baremoMentalLoadDemands;
        $insertResults->intrawork_raw_mental_load_demands = $dimensionMentalLoadDemands;

        $insertResults->intrawork_consistency = $transformConsistency;
        $insertResults->intrawork_level_consistency = $baremoConsistency;
        $insertResults->intrawork_raw_consistency = $dimensionConsistency;

        $insertResults->intrawork_day_demands = $transformDayDemands;
        $insertResults->intrawork_level_day_demands = $baremoDayDemands;
        $insertResults->intrawork_raw_day_demands = $dimensionDayDemands;

        $insertResults->intrawork_work_demands = $transformWorkDemands;
        $insertResults->intrawork_level_work_demands = $baremoWorkDemands;
        $insertResults->intrawork_raw_work_demands = $domainWorkDemands;

        $insertResults->intrawork_rewards_work = $transformRewardsWork;
        $insertResults->intrawork_level_rewards_work = $baremoRewardsWork;
        $insertResults->intrawork_raw_rewards_work = $dimensionRewardsWork;

        $insertResults->intrawork_recognition = $transformRecognition;
        $insertResults->intrawork_level_recognition = $baremoRecognition;
        $insertResults->intrawork_raw_recognition = $dimensionRecognition;

        $insertResults->intrawork_rewards = $transformRewards;
        $insertResults->intrawork_level_rewards = $baremoRewards;
        $insertResults->intrawork_raw_rewards = $domainRewards;

        $insertResults->intrawork_results_general = $trasnformGeneralIntralaboral;
        $insertResults->intrawork_level_results_general = $baremoGeneralIntralaboral;
        $insertResults->intrawork_raw_results_general = $generalIntralaboral;

        if ($insertResults->results_cap_intra == 0) {
            if ($insertResults->intrawork_level_results_general == "Riesgo muy alto" && $insertResults->stress_level_results_general == "Muy alto") {
                $insertResults->results_cap_intra++;
            } elseif ($insertResults->intrawork_level_results_general == "Riesgo muy alto" && $insertResults->stress_level_results_general == "Alto") {
                $insertResults->results_cap_intra++;
            } elseif ($insertResults->intrawork_level_results_general == "Riesgo alto" && $insertResults->stress_level_results_general == "Muy alto") {
                $insertResults->results_cap_intra++;
            } elseif ($insertResults->intrawork_level_results_general == "Riesgo alto" && $insertResults->stress_level_results_general == "Alto") {
                $insertResults->results_cap_intra++;
            }
        }

        if ($insertResults->results_cap_intra == 1 && $insertResults->results_cap_extra == 1 && $insertResults->results_cap_intra_extra == 0) {
            $insertResults->results_cap_intra_extra++;
        }

        if ($insertResults->results_stress_no_specific == 0) {
            if ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" &&  $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } else {
                $insertResults->results_stress_no_specific = 0;
            }
        }

        if (
            $insertResults->company_id == null &&
            $insertResults->city == null &&
            $insertResults->first_level == null &&
            $insertResults->second_level == null &&
            $insertResults->third_level == null &&
            $insertResults->fourth_level == null &&
            $insertResults->fifth_level == null &&
            $insertResults->sixth_level == null &&
            $insertResults->seventh_level == null &&
            $insertResults->eighth_level == null
        ) {
            $insertResults->type_questionnaire = $questionnaireType;
            $insertResults->company_id = $resultExistsEmployee->company_id;
            $insertResults->city = $resultExistsEmployee->city;
            $insertResults->position = $resultExistsEmployee->position;

            $insertResults->first_level = $resultExistsEmployee->first_level;
            $insertResults->second_level = $resultExistsEmployee->second_level;
            $insertResults->third_level = $resultExistsEmployee->third_level;
            $insertResults->fourth_level = $resultExistsEmployee->fourth_level;
            $insertResults->fifth_level = $resultExistsEmployee->fifth_level;
            $insertResults->sixth_level = $resultExistsEmployee->sixth_level;
            $insertResults->seventh_level = $resultExistsEmployee->seventh_level;
            $insertResults->eighth_level = $resultExistsEmployee->eighth_level;
        }

        $QuestionnaireExits->state_intrawork = 'Realizado';

        if ($insertResults->save() && $answersIntraWork && $QuestionnaireExits->save()) {
            return response()->json(['message' => "Se han registrado las respuestas exitosamente."], 200);
        } else {
            return response()->json(['error' => "No se ha podido registrar las respuestas.", 'Questionnaire' => 'Intalaboral'], 500);
        }
    }

    public function migrateQuestionnaireB()
    {
        $Caliriesgo = caliriesgopsicosocialparte2::all();

        foreach ($Caliriesgo as $answerOld) {
            $Employees = Employees::where('document_employee', '=', $answerOld->cc)
                ->join('psychosocial_questionnaires', 'psychosocial_questionnaires.employee_id', '=', 'psychosocial_employees.employee_id')
                ->first();

            if (!$Employees) {
                continue;
            }

            $fechaOriginal = $answerOld->fechaaplicacion;
            $fechaFormateada = DateTime::createFromFormat('d/m/Y', $fechaOriginal)->format('Y-m-d');

            $data = [
                "questionnaire_id" => $Employees->questionnaire_id,
                "response_date" => $fechaFormateada,
                "answer_1" => $answerOld->r1,
                "answer_2" => $answerOld->r2,
                "answer_3" => $answerOld->r3,
                "answer_4" => $answerOld->r4,
                "answer_5" => $answerOld->r5,
                "answer_6" => $answerOld->r6,
                "answer_7" => $answerOld->r7,
                "answer_8" => $answerOld->r8,
                "answer_9" => $answerOld->r9,
                "answer_10" => $answerOld->r10,
                "answer_11" => $answerOld->r11,
                "answer_12" => $answerOld->r12,
                "answer_13" => $answerOld->r13,
                "answer_14" => $answerOld->r14,
                "answer_15" => $answerOld->r15,
                "answer_16" => $answerOld->r16,
                "answer_17" => $answerOld->r17,
                "answer_18" => $answerOld->r18,
                "answer_19" => $answerOld->r19,
                "answer_20" => $answerOld->r20,
                "answer_21" => $answerOld->r21,
                "answer_22" => $answerOld->r22,
                "answer_23" => $answerOld->r23,
                "answer_24" => $answerOld->r24,
                "answer_25" => $answerOld->r25,
                "answer_26" => $answerOld->r26,
                "answer_27" => $answerOld->r27,
                "answer_28" => $answerOld->r28,
                "answer_29" => $answerOld->r29,
                "answer_30" => $answerOld->r30,
                "answer_31" => $answerOld->r31,
                "answer_32" => $answerOld->r32,
                "answer_33" => $answerOld->r33,
                "answer_34" => $answerOld->r34,
                "answer_35" => $answerOld->r35,
                "answer_36" => $answerOld->r36,
                "answer_37" => $answerOld->r37,
                "answer_38" => $answerOld->r38,
                "answer_39" => $answerOld->r39,
                "answer_40" => $answerOld->r40,
                "answer_41" => $answerOld->r41,
                "answer_42" => $answerOld->r42,
                "answer_43" => $answerOld->r43,
                "answer_44" => $answerOld->r44,
                "answer_45" => $answerOld->r45,
                "answer_46" => $answerOld->r46,
                "answer_47" => $answerOld->r47,
                "answer_48" => $answerOld->r48,
                "answer_49" => $answerOld->r49,
                "answer_50" => $answerOld->r50,
                "answer_51" => $answerOld->r51,
                "answer_52" => $answerOld->r52,
                "answer_53" => $answerOld->r53,
                "answer_54" => $answerOld->r54,
                "answer_55" => $answerOld->r55,
                "answer_56" => $answerOld->r56,
                "answer_57" => $answerOld->r57,
                "answer_58" => $answerOld->r58,
                "answer_59" => $answerOld->r59,
                "answer_60" => $answerOld->r60,
                "answer_61" => $answerOld->r61,
                "answer_62" => $answerOld->r62,
                "answer_63" => $answerOld->r63,
                "answer_64" => $answerOld->r64,
                "answer_65" => $answerOld->r65,
                "answer_66" => $answerOld->r66,
                "answer_67" => $answerOld->r67,
                "answer_68" => $answerOld->r68,
                "answer_69" => $answerOld->r69,
                "answer_70" => $answerOld->r70,
                "answer_71" => $answerOld->r71,
                "answer_72" => $answerOld->r72,
                "answer_73" => $answerOld->r73,
                "answer_74" => $answerOld->r74,
                "answer_75" => $answerOld->r75,
                "answer_76" => $answerOld->r76,
                "answer_77" => $answerOld->r77,
                "answer_78" => $answerOld->r78,
                "answer_79" => $answerOld->r79,
                "answer_80" => $answerOld->r80,
                "answer_81" => $answerOld->r81,
                "answer_82" => $answerOld->r82,
                "answer_83" => $answerOld->r83,
                "answer_84" => $answerOld->r84,
                "answer_85" => $answerOld->r85,
                "answer_86" => $answerOld->r86,
                "answer_87" => $answerOld->r87,
                "answer_88" => $answerOld->r88,
                "answer_89" => $answerOld->r89,
                "answer_90" => $answerOld->r90,
                "answer_91" => $answerOld->r91,
                "answer_92" => $answerOld->r92,
                "answer_93" => $answerOld->r93,
                "answer_94" => $answerOld->r94,
                "answer_95" => $answerOld->r95,
                "answer_96" => $answerOld->r96,
                "answer_97" => $answerOld->r97
            ];


            $dataIntrawork = new Request();
            $dataIntrawork->replace($data);

            $this->registerIntraWorkBUser($dataIntrawork);
        }
    }

    public function registerIntraWorkBUser(Request $request)
    {
        $QuestionnaireExits = Questionnaires::where('questionnaire_id', $request->questionnaire_id)->first();

        if (!$QuestionnaireExits) {
            return;
        }

        $dimensionCharacteristics = null;
        $dimensionRelations = null;
        $dimensionFeedback = null;
        $dimensionClarity = null;
        $dimensionTraining = null;
        $dimensionStake = null;
        $dimensionOpportunities = null;
        $dimensionControl = null;
        $dimensionEnviromentalDemands = null;
        $dimensionEmotionalDemands = null;
        $dimensionQuantitativeDemands = null;
        $dimensionWorkInfluence = null;
        $dimensionMentalLoadDemands = null;
        $dimensionDayDemands = null;
        $dimensionRewardsWork = null;
        $dimensionRecognition = null;

        for ($i = 1; $i <= 97; $i++) {
            if (
                $request["answer_$i"] == 'no aplica'
                || $request["answer_$i"] == 'no responde'
                || $request["answer_$i"] == 'no respondio'
            ) {
                $request["answer_$i"] = '';
                continue;
            }

            // Dominio: Relaciones sociales en el trabajo
            // Liderazgo
            if (
                $i == 49
                || $i == 50
                || $i == 51
                || $i == 52
                || $i == 53
                || $i == 54
                || $i == 55
                || $i == 56
                || $i == 57
                || $i == 58
                || $i == 59
                || $i == 60
                || $i == 61

            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionCharacteristics += $request["answer_$i"];
                }
            }
            // Relaciones sociales en el trabajo
            if (
                $i == 62
                || $i == 63
                || $i == 64
                || $i == 65
                || $i == 66
                || $i == 67
                || $i == 68
                || $i == 69
                || $i == 70
                || $i == 71
                || $i == 72
                || $i == 73
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionRelations += $request["answer_$i"];
                }
            }
            // Retroalimentación del desempeño
            if (
                $i == 74
                || $i == 75
                || $i == 76
                || $i == 77
                || $i == 78
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionFeedback += $request["answer_$i"];
                }
            }
            // Dominio: Control sobre el trabajo
            // Claridad de rol
            if (
                $i == 41
                || $i == 42
                || $i == 43
                || $i == 44
                || $i == 45
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionClarity += $request["answer_$i"];
                }
            }
            // Capacitación
            if (
                $i == 46
                || $i == 47
                || $i == 48
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionTraining += $request["answer_$i"];
                }
            }
            // Participación y manejo del cambio
            if (
                $i == 38
                || $i == 39
                || $i == 40
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionStake += $request["answer_$i"];
                }
            }
            // Oportunidades para el uso y desarrollo de habilidades y conocimientos
            if (
                $i == 29
                || $i == 30
                || $i == 31
                || $i == 32
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionOpportunities += $request["answer_$i"];
                }
            }
            // Control y autonomía sobre el trabajo
            if (
                $i == 34
                || $i == 35
                || $i == 36
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionControl += $request["answer_$i"];
                }
            }
            // Dominio: Demandas del trabajo
            // Demandas ambientales y de esfuerzo físico
            if (
                $i == 1
                || $i == 2
                || $i == 3
                || $i == 4
                || $i == 5
                || $i == 6
                || $i == 7
                || $i == 8
                || $i == 9
                || $i == 10
                || $i == 11
                || $i == 12
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionEnviromentalDemands += $request["answer_$i"];
                }
            }
            // Demandas emocionales
            if (
                $i == 89
                || $i == 90
                || $i == 91
                || $i == 92
                || $i == 93
                || $i == 94
                || $i == 95
                || $i == 96
                || $i == 97
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionEmotionalDemands += $request["answer_$i"];
                }
            }
            // Demandas cuantitativas
            if (
                $i == 13
                || $i == 14
                || $i == 15
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionQuantitativeDemands += $request["answer_$i"];
                }
            }
            // Influencia del trabajo en el entorno extralaboral
            if (
                $i == 25
                || $i == 26
                || $i == 27
                || $i == 28
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionWorkInfluence += $request["answer_$i"];
                }
            }
            // Demandas de carga mental
            if (
                $i == 16
                || $i == 17
                || $i == 18
                || $i == 19
                || $i == 20
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionMentalLoadDemands += $request["answer_$i"];
                }
            }
            // Demandas de la jornada de trabajo
            if (
                $i == 21
                || $i == 22
                || $i == 23
                || $i == 24
                || $i == 33
                || $i == 37
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionDayDemands += $request["answer_$i"];
                }
            }
            // Dominio: Recompensas
            // Recompensas derivadas de la pertenencia a la organización y del trabajo que se realiza
            if (
                $i == 85
                || $i == 86
                || $i == 87
                || $i == 88
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionRewardsWork += $request["answer_$i"];
                }
            }
            // Reconocimiento y compensación
            if (
                $i == 79
                || $i == 80
                || $i == 81
                || $i == 82
                || $i == 83
                || $i == 84
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $dimensionRecognition += $request["answer_$i"];
                }
            }
        }

        // CÁLCULOS POR DIMENSIONES
        // LIDERAZGO Y RELACIONES SOCIALES EN EL TRABAJO
        $transformCharacteristics = null;
        $baremoCharacteristics = null;

        if ($dimensionCharacteristics >= 0) {
            $transformCharacteristics = round(($dimensionCharacteristics / 52) * 100, 2);

            if ($transformCharacteristics <= 3.8) {
                $baremoCharacteristics = "Sin riesgo o riesgo despreciable";
            } else if ($transformCharacteristics <= 13.5) {
                $baremoCharacteristics = "Riesgo bajo";
            } else if ($transformCharacteristics <= 25.0) {
                $baremoCharacteristics = "Riesgo medio";
            } else if ($transformCharacteristics <= 38.5) {
                $baremoCharacteristics = "Riesgo alto";
            } else {
                $baremoCharacteristics = "Riesgo muy alto";
            }
        }

        $transformRelations = null;
        $baremoRelations = null;
        if ($dimensionRelations >= 0) {
            $transformRelations = round(($dimensionRelations / 48) * 100, 2);

            if ($transformRelations <= 6.3) {
                $baremoRelations = "Sin riesgo o riesgo despreciable";
            } else if ($transformRelations <= 14.6) {
                $baremoRelations = "Riesgo bajo";
            } else if ($transformRelations <= 27.1) {
                $baremoRelations = "Riesgo medio";
            } else if ($transformRelations <= 37.5) {
                $baremoRelations = "Riesgo alto";
            } else {
                $baremoRelations = "Riesgo muy alto";
            }
        }

        $transformFeedback = null;
        $baremoFeedback = null;
        if ($dimensionFeedback >= 0) {
            $transformFeedback = round(($dimensionFeedback / 20) * 100, 2);

            if ($transformFeedback <= 5) {
                $baremoFeedback = "Sin riesgo o riesgo despreciable";
            } else if ($transformFeedback <= 20) {
                $baremoFeedback = "Riesgo bajo";
            } else if ($transformFeedback <= 30) {
                $baremoFeedback = "Riesgo medio";
            } else if ($transformFeedback <= 50) {
                $baremoFeedback = "Riesgo alto";
            } else {
                $baremoFeedback = "Riesgo muy alto";
            }
        }

        // CONTROL SOBRE EL TRABAJO
        $transformClarity = null;
        $baremoClarity = null;
        if ($dimensionClarity >= 0) {
            $transformClarity = round(($dimensionClarity / 20) * 100, 2);

            if ($transformClarity <= 0.9) {
                $baremoClarity = "Sin riesgo o riesgo despreciable";
            } else if ($transformClarity <= 5) {
                $baremoClarity = "Riesgo bajo";
            } else if ($transformClarity <= 15) {
                $baremoClarity = "Riesgo medio";
            } else if ($transformClarity <= 30) {
                $baremoClarity = "Riesgo alto";
            } else {
                $baremoClarity = "Riesgo muy alto";
            }
        }

        $transformTraining = null;
        $baremoTraining = null;
        if ($dimensionTraining >= 0) {
            $transformTraining = round(($dimensionTraining / 12) * 100, 2);

            if ($transformTraining <= 0.9) {
                $baremoTraining = "Sin riesgo o riesgo despreciable";
            } else if ($transformTraining <= 16.7) {
                $baremoTraining = "Riesgo bajo";
            } else if ($transformTraining <= 25) {
                $baremoTraining = "Riesgo medio";
            } else if ($transformTraining <= 50) {
                $baremoTraining = "Riesgo alto";
            } else {
                $baremoTraining = "Riesgo muy alto";
            }
        }

        $transformStake = null;
        $baremoStake = null;
        if ($dimensionStake >= 0) {
            $transformStake = round(($dimensionStake / 12) * 100, 2);

            if ($transformStake <= 16.7) {
                $baremoStake = "Sin riesgo o riesgo despreciable";
            } else if ($transformStake <= 33.3) {
                $baremoStake = "Riesgo bajo";
            } else if ($transformStake <= 41.7) {
                $baremoStake = "Riesgo medio";
            } else if ($transformStake <= 58.3) {
                $baremoStake = "Riesgo alto";
            } else {
                $baremoStake = "Riesgo muy alto";
            }
        }

        $transformOpportunities = null;
        $baremoOpportunities = null;
        if ($dimensionOpportunities >= 0) {
            $transformOpportunities = round(($dimensionOpportunities / 16) * 100, 2);

            if ($transformOpportunities <= 12.5) {
                $baremoOpportunities = "Sin riesgo o riesgo despreciable";
            } else if ($transformOpportunities <= 25) {
                $baremoOpportunities = "Riesgo bajo";
            } else if ($transformOpportunities <= 37.5) {
                $baremoOpportunities = "Riesgo medio";
            } else if ($transformOpportunities <= 56.3) {
                $baremoOpportunities = "Riesgo alto";
            } else {
                $baremoOpportunities = "Riesgo muy alto";
            }
        }

        $transformControl = null;
        $baremoControl = null;
        if ($dimensionControl >= 0) {
            $transformControl = round(($dimensionControl / 12) * 100, 2);

            if ($transformControl <= 33.3) {
                $baremoControl = "Sin riesgo o riesgo despreciable";
            } else if ($transformControl <= 50) {
                $baremoControl = "Riesgo bajo";
            } else if ($transformControl <= 66.7) {
                $baremoControl = "Riesgo medio";
            } else if ($transformControl <= 75) {
                $baremoControl = "Riesgo alto";
            } else {
                $baremoControl = "Riesgo muy alto";
            }
        }

        // DEMANDAS DEL TRABAJO
        $transformEnviromentalDemands = null;
        $baremoEnviromentalDemands = null;
        if ($dimensionEnviromentalDemands >= 0) {
            $transformEnviromentalDemands = round(($dimensionEnviromentalDemands / 48) * 100, 2);

            if ($transformEnviromentalDemands <= 22.9) {
                $baremoEnviromentalDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformEnviromentalDemands <= 31.3) {
                $baremoEnviromentalDemands = "Riesgo bajo";
            } else if ($transformEnviromentalDemands <= 39.6) {
                $baremoEnviromentalDemands = "Riesgo medio";
            } else if ($transformEnviromentalDemands <= 47.9) {
                $baremoEnviromentalDemands = "Riesgo alto";
            } else {
                $baremoEnviromentalDemands = "Riesgo muy alto";
            }
        }

        $transformEmotionalDemands = null;
        $baremoEmotionalDemands = null;
        if ($dimensionEmotionalDemands >= 0) {
            $transformEmotionalDemands = round(($dimensionEmotionalDemands / 36) * 100, 2);

            if ($transformEmotionalDemands <= 19.4) {
                $baremoEmotionalDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformEmotionalDemands <= 27.8) {
                $baremoEmotionalDemands = "Riesgo bajo";
            } else if ($transformEmotionalDemands <= 38.9) {
                $baremoEmotionalDemands = "Riesgo medio";
            } else if ($transformEmotionalDemands <= 47.2) {
                $baremoEmotionalDemands = "Riesgo alto";
            } else {
                $baremoEmotionalDemands = "Riesgo muy alto";
            }
        }

        $transformQuantitativeDemands = null;
        $baremoQuantitativeDemands = null;
        if ($dimensionQuantitativeDemands >= 0) {
            $transformQuantitativeDemands = round(($dimensionQuantitativeDemands / 12) * 100, 2);

            if ($transformQuantitativeDemands <= 16.7) {
                $baremoQuantitativeDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformQuantitativeDemands <= 33.3) {
                $baremoQuantitativeDemands = "Riesgo bajo";
            } else if ($transformQuantitativeDemands <= 41.7) {
                $baremoQuantitativeDemands = "Riesgo medio";
            } else if ($transformQuantitativeDemands <= 50) {
                $baremoQuantitativeDemands = "Riesgo alto";
            } else {
                $baremoQuantitativeDemands = "Riesgo muy alto";
            }
        }

        $transformWorkInfluence = null;
        $baremoWorkInfluence = null;
        if ($dimensionWorkInfluence >= 0) {
            $transformWorkInfluence = round(($dimensionWorkInfluence / 16) * 100, 2);

            if ($transformWorkInfluence <= 12.5) {
                $baremoWorkInfluence = "Sin riesgo o riesgo despreciable";
            } else if ($transformWorkInfluence <= 25) {
                $baremoWorkInfluence = "Riesgo bajo";
            } else if ($transformWorkInfluence <= 31.3) {
                $baremoWorkInfluence = "Riesgo medio";
            } else if ($transformWorkInfluence <= 50) {
                $baremoWorkInfluence = "Riesgo alto";
            } else {
                $baremoWorkInfluence = "Riesgo muy alto";
            }
        }

        $transformMentalLoadDemands = null;
        $baremoMentalLoadDemands = null;
        if ($dimensionMentalLoadDemands >= 0) {
            $transformMentalLoadDemands = round(($dimensionMentalLoadDemands / 20) * 100, 2);

            if ($transformMentalLoadDemands <= 50) {
                $baremoMentalLoadDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformMentalLoadDemands <= 65) {
                $baremoMentalLoadDemands = "Riesgo bajo";
            } else if ($transformMentalLoadDemands <= 75) {
                $baremoMentalLoadDemands = "Riesgo medio";
            } else if ($transformMentalLoadDemands <= 85) {
                $baremoMentalLoadDemands = "Riesgo alto";
            } else {
                $baremoMentalLoadDemands = "Riesgo muy alto";
            }
        }

        $transformDayDemands = null;
        $baremoDayDemands = null;
        if ($dimensionDayDemands >= 0) {
            $transformDayDemands = round(($dimensionDayDemands / 24) * 100, 2);

            if ($transformDayDemands <= 25) {
                $baremoDayDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformDayDemands <= 37.5) {
                $baremoDayDemands = "Riesgo bajo";
            } else if ($transformDayDemands <= 45.8) {
                $baremoDayDemands = "Riesgo medio";
            } else if ($transformDayDemands <= 58.3) {
                $baremoDayDemands = "Riesgo alto";
            } else {
                $baremoDayDemands = "Riesgo muy alto";
            }
        }

        // RECOMPENSAS
        $transformRewardsWork = null;
        $baremoRewardsWork = null;
        if ($dimensionRewardsWork >= 0) {
            $transformRewardsWork = round(($dimensionRewardsWork / 16) * 100, 2);

            if ($transformRewardsWork <= 0.9) {
                $baremoRewardsWork = "Sin riesgo o riesgo despreciable";
            } else if ($transformRewardsWork <= 6.3) {
                $baremoRewardsWork = "Riesgo bajo";
            } else if ($transformRewardsWork <= 12.5) {
                $baremoRewardsWork = "Riesgo medio";
            } else if ($transformRewardsWork <= 18.8) {
                $baremoRewardsWork = "Riesgo alto";
            } else {
                $baremoRewardsWork = "Riesgo muy alto";
            }
        }

        $transformRecognition = null;
        $baremoRecognition = null;
        if ($dimensionRecognition >= 0) {
            $transformRecognition = round(($dimensionRecognition / 24) * 100, 2);

            if ($transformRecognition <= 0.9) {
                $baremoRecognition = "Sin riesgo o riesgo despreciable";
            } else if ($transformRecognition <= 12.5) {
                $baremoRecognition = "Riesgo bajo";
            } else if ($transformRecognition <= 25) {
                $baremoRecognition = "Riesgo medio";
            } else if ($transformRecognition <= 37.5) {
                $baremoRecognition = "Riesgo alto";
            } else {
                $baremoRecognition = "Riesgo muy alto";
            }
        }

        // Dominios
        $domainLeaderShip = $dimensionCharacteristics + $dimensionRelations + $dimensionFeedback;
        $transformLeaderShip = null;
        $baremoLeaderShip = null;
        if ($domainLeaderShip >= 0) {
            $transformLeaderShip = round(($domainLeaderShip / 120) * 100, 2);

            if ($transformLeaderShip <= 8.3) {
                $baremoLeaderShip = "Sin riesgo o riesgo despreciable";
            } else if ($transformLeaderShip <= 17.5) {
                $baremoLeaderShip = "Riesgo bajo";
            } else if ($transformLeaderShip <= 26.7) {
                $baremoLeaderShip = "Riesgo medio";
            } else if ($transformLeaderShip <= 34.3) {
                $baremoLeaderShip = "Riesgo alto";
            } else {
                $baremoLeaderShip = "Riesgo muy alto";
            }
        }

        $domainWorkControl = $dimensionClarity + $dimensionTraining + $dimensionStake + $dimensionOpportunities + $dimensionControl;
        $transformWorkControl = null;
        $baremoWorkControl = null;
        if ($domainWorkControl >= 0) {
            $transformWorkControl = round(($domainWorkControl / 72) * 100, 2);

            if ($transformWorkControl <= 19.4) {
                $baremoWorkControl = "Sin riesgo o riesgo despreciable";
            } else if ($transformWorkControl <= 26.4) {
                $baremoWorkControl = "Riesgo bajo";
            } else if ($transformWorkControl <= 34.7) {
                $baremoWorkControl = "Riesgo medio";
            } else if ($transformWorkControl <= 43.1) {
                $baremoWorkControl = "Riesgo alto";
            } else {
                $baremoWorkControl = "Riesgo muy alto";
            }
        }

        $domainWorkDemands = $dimensionEnviromentalDemands + $dimensionEmotionalDemands + $dimensionQuantitativeDemands + $dimensionWorkInfluence + $dimensionMentalLoadDemands + $dimensionDayDemands;
        $transformWorkDemands = null;
        $baremoWorkDemands = null;
        if ($domainWorkDemands >= 0) {
            $transformWorkDemands = round(($domainWorkDemands / 156) * 100, 2);

            if ($transformWorkDemands <= 26.9) {
                $baremoWorkDemands = "Sin riesgo o riesgo despreciable";
            } else if ($transformWorkDemands <= 33.3) {
                $baremoWorkDemands = "Riesgo bajo";
            } else if ($transformWorkDemands <= 37.8) {
                $baremoWorkDemands = "Riesgo medio";
            } else if ($transformWorkDemands <= 44.2) {
                $baremoWorkDemands = "Riesgo alto";
            } else {
                $baremoWorkDemands = "Riesgo muy alto";
            }
        }

        $domainRewards = $dimensionRewardsWork + $dimensionRecognition;
        $transformRewards = null;
        $baremoRewards = null;
        if ($domainRewards >= 0) {
            $transformRewards = round(($domainRewards / 40) * 100, 2);

            if ($transformRewards <= 2.5) {
                $baremoRewards = "Sin riesgo o riesgo despreciable";
            } else if ($transformRewards <= 10) {
                $baremoRewards = "Riesgo bajo";
            } else if ($transformRewards <= 17.5) {
                $baremoRewards = "Riesgo medio";
            } else if ($transformRewards <= 27.5) {
                $baremoRewards = "Riesgo alto";
            } else {
                $baremoRewards = "Riesgo muy alto";
            }
        }

        $generalIntralaboral = $domainLeaderShip + $domainWorkControl + $domainWorkDemands + $domainRewards;
        $trasnformGeneralIntralaboral = null;
        $baremoGeneralIntralaboral = null;

        if ($generalIntralaboral >= 0) {
            $trasnformGeneralIntralaboral = round(($generalIntralaboral / 388) * 100, 2);

            if ($trasnformGeneralIntralaboral <= 20.7) {
                $baremoGeneralIntralaboral = "Sin riesgo o riesgo despreciable";
            } else if ($trasnformGeneralIntralaboral <= 26.1) {
                $baremoGeneralIntralaboral = "Riesgo bajo";
            } else if ($trasnformGeneralIntralaboral <= 31.3) {
                $baremoGeneralIntralaboral = "Riesgo medio";
            } else if ($trasnformGeneralIntralaboral <= 38.8) {
                $baremoGeneralIntralaboral = "Riesgo alto";
            } else {
                $baremoGeneralIntralaboral = "Riesgo muy alto";
            }
        }

        $updateData = [
            'response_date' => $request->response_date,
            'answer_1' => $request->answer_1,
            'answer_2' => $request->answer_2,
            'answer_3' => $request->answer_3,
            'answer_4' => $request->answer_4,
            'answer_5' => $request->answer_5,
            'answer_6' => $request->answer_6,
            'answer_7' => $request->answer_7,
            'answer_8' => $request->answer_8,
            'answer_9' => $request->answer_9,
            'answer_10' => $request->answer_10,
            'answer_11' => $request->answer_11,
            'answer_12' => $request->answer_12,
            'answer_13' => $request->answer_13,
            'answer_14' => $request->answer_14,
            'answer_15' => $request->answer_15,
            'answer_16' => $request->answer_16,
            'answer_17' => $request->answer_17,
            'answer_18' => $request->answer_18,
            'answer_19' => $request->answer_19,
            'answer_20' => $request->answer_20,
            'answer_21' => $request->answer_21,
            'answer_22' => $request->answer_22,
            'answer_23' => $request->answer_23,
            'answer_24' => $request->answer_24,
            'answer_25' => $request->answer_25,
            'answer_26' => $request->answer_26,
            'answer_27' => $request->answer_27,
            'answer_28' => $request->answer_28,
            'answer_29' => $request->answer_29,
            'answer_30' => $request->answer_30,
            'answer_31' => $request->answer_31,
            'answer_32' => $request->answer_32,
            'answer_33' => $request->answer_33,
            'answer_34' => $request->answer_34,
            'answer_35' => $request->answer_35,
            'answer_36' => $request->answer_36,
            'answer_37' => $request->answer_37,
            'answer_38' => $request->answer_38,
            'answer_39' => $request->answer_39,
            'answer_40' => $request->answer_40,
            'answer_41' => $request->answer_41,
            'answer_42' => $request->answer_42,
            'answer_43' => $request->answer_43,
            'answer_44' => $request->answer_44,
            'answer_45' => $request->answer_45,
            'answer_46' => $request->answer_46,
            'answer_47' => $request->answer_47,
            'answer_48' => $request->answer_48,
            'answer_49' => $request->answer_49,
            'answer_50' => $request->answer_50,
            'answer_51' => $request->answer_51,
            'answer_52' => $request->answer_52,
            'answer_53' => $request->answer_53,
            'answer_54' => $request->answer_54,
            'answer_55' => $request->answer_55,
            'answer_56' => $request->answer_56,
            'answer_57' => $request->answer_57,
            'answer_58' => $request->answer_58,
            'answer_59' => $request->answer_59,
            'answer_60' => $request->answer_60,
            'answer_61' => $request->answer_61,
            'answer_62' => $request->answer_62,
            'answer_63' => $request->answer_63,
            'answer_64' => $request->answer_64,
            'answer_65' => $request->answer_65,
            'answer_66' => $request->answer_66,
            'answer_67' => $request->answer_67,
            'answer_68' => $request->answer_68,
            'answer_69' => $request->answer_69,
            'answer_70' => $request->answer_70,
            'answer_71' => $request->answer_71,
            'answer_72' => $request->answer_72,
            'answer_73' => $request->answer_73,
            'answer_74' => $request->answer_74,
            'answer_75' => $request->answer_75,
            'answer_76' => $request->answer_76,
            'answer_77' => $request->answer_77,
            'answer_78' => $request->answer_78,
            'answer_79' => $request->answer_79,
            'answer_80' => $request->answer_80,
            'answer_81' => $request->answer_81,
            'answer_82' => $request->answer_82,
            'answer_83' => $request->answer_83,
            'answer_84' => $request->answer_84,
            'answer_85' => $request->answer_85,
            'answer_86' => $request->answer_86,
            'answer_87' => $request->answer_87,
            'answer_88' => $request->answer_88,
            'answer_89' => $request->answer_89,
            'answer_90' => $request->answer_90,
            'answer_91' => $request->answer_91,
            'answer_92' => $request->answer_92,
            'answer_93' => $request->answer_93,
            'answer_94' => $request->answer_94,
            'answer_95' => $request->answer_95,
            'answer_96' => $request->answer_96,
            'answer_97' => $request->answer_97,
        ];

        $answersIntraWork = IntraWorkB::updateOrCreate(
            ['questionnaire_id' => $request->questionnaire_id],
            $updateData
        );

        $measurement_id = $QuestionnaireExits->measurement_id;
        $questionnaireType = $QuestionnaireExits->type_questionarie;

        $EmployeeLevels = new Employees();
        $resultExistsEmployee = $EmployeeLevels
            ->join('psychosocial_questionnaires', 'psychosocial_employees.employee_id', '=', 'psychosocial_questionnaires.employee_id')
            ->where('psychosocial_questionnaires.questionnaire_id', $request->questionnaire_id)
            ->select('psychosocial_employees.*')
            ->first();

        $Results = new Results();
        $resultExists = $Results->where('questionnaire_id', $request->questionnaire_id)
            ->where('measurement_id', $measurement_id)
            ->where('type_questionnaire', $questionnaireType)
            ->first();

        if ($resultExists != null) {
            $insertResults = $resultExists;

            if ($insertResults->extrawork_raw_results_general != null) {
                $generalIntraworkExtrawork = $insertResults->extrawork_raw_results_general + $generalIntralaboral;
                $transformIntraExtraWork = ($generalIntraworkExtrawork / 512) * 100;

                if ($transformIntraExtraWork <= 19.9) {
                    $baremoGeneralIntraExtrawork = "Sin riesgo o riesgo despreciable";
                } else if ($transformIntraExtraWork <= 24.8) {
                    $baremoGeneralIntraExtrawork = "Riesgo bajo";
                } else if ($transformIntraExtraWork <= 29.5) {
                    $baremoGeneralIntraExtrawork = "Riesgo medio";
                } else if ($transformIntraExtraWork <= 35.4) {
                    $baremoGeneralIntraExtrawork = "Riesgo alto";
                } else {
                    $baremoGeneralIntraExtrawork = "Riesgo muy alto";
                }

                if ($insertResults->intrawork_level_results_general == "Riesgo muy alto" && $insertResults->stress_level_results_general == "Muy alto") {
                    $insertResults->results_cap_intra++;
                } elseif ($insertResults->intrawork_level_results_general == "Riesgo muy alto" && $insertResults->stress_level_results_general == "Alto") {
                    $insertResults->results_cap_intra++;
                } elseif ($insertResults->intrawork_level_results_general == "Riesgo alto" && $insertResults->stress_level_results_general == "Muy alto") {
                    $insertResults->results_cap_intra++;
                } elseif ($insertResults->intrawork_level_results_general == "Riesgo alto" && $insertResults->stress_level_results_general == "Alto") {
                    $insertResults->results_cap_intra++;
                }

                if ($insertResults->results_cap_intra == 1 && $insertResults->results_cap_extra == 1 && $insertResults->results_cap_intra_extra == 0) {
                    $insertResults->results_cap_intra_extra++;
                }

                if ($insertResults->results_stress_no_specific == 0) {
                    if ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } elseif ($insertResults->stress_level_results_general == "Alto" &&  $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                        $insertResults->results_stress_no_specific++;
                    } else {
                        $insertResults->results_stress_no_specific = 0;
                    }
                }

                $insertResults->general_intrawork_extrawork = $transformIntraExtraWork;
                $insertResults->general_results_intrawork_extrawork = $baremoGeneralIntraExtrawork;
                $insertResults->general_raw_intrawork_extrawork = $generalIntraworkExtrawork;
            }
        } else {
            $insertResults = $Results;
        }

        $insertResults->questionnaire_id = $request->questionnaire_id;
        $insertResults->measurement_id = $measurement_id;
        $insertResults->type_questionnaire = $questionnaireType;

        $insertResults->intrawork_characteristics = $transformCharacteristics;
        $insertResults->intrawork_level_characteristics = $baremoCharacteristics;
        $insertResults->intrawork_raw_characteristics = $dimensionCharacteristics;

        $insertResults->intrawork_relations = $transformRelations;
        $insertResults->intrawork_level_relations = $baremoRelations;
        $insertResults->intrawork_raw_relations = $dimensionRelations;

        $insertResults->intrawork_feedback = $transformFeedback;
        $insertResults->intrawork_level_feedback = $baremoFeedback;
        $insertResults->intrawork_raw_feedback = $dimensionFeedback;

        $insertResults->intrawork_leadership = $transformLeaderShip;
        $insertResults->intrawork_level_leadership = $baremoLeaderShip;
        $insertResults->intrawork_raw_leadership = $domainLeaderShip;

        $insertResults->intrawork_clarity = $transformClarity;
        $insertResults->intrawork_level_clarity = $baremoClarity;
        $insertResults->intrawork_raw_clarity = $dimensionClarity;

        $insertResults->intrawork_training = $transformTraining;
        $insertResults->intrawork_level_training = $baremoTraining;
        $insertResults->intrawork_raw_training = $dimensionTraining;

        $insertResults->intrawork_stake = $transformStake;
        $insertResults->intrawork_level_stake = $baremoStake;
        $insertResults->intrawork_raw_stake = $dimensionStake;

        $insertResults->intrawork_opportunities = $transformOpportunities;
        $insertResults->intrawork_level_opportunities = $baremoOpportunities;
        $insertResults->intrawork_raw_opportunities = $dimensionOpportunities;

        $insertResults->intrawork_control = $transformControl;
        $insertResults->intrawork_level_control = $baremoControl;
        $insertResults->intrawork_raw_control = $dimensionControl;

        $insertResults->intrawork_work_control = $transformWorkControl;
        $insertResults->intrawork_level_work_control = $baremoWorkControl;
        $insertResults->intrawork_raw_work_control = $domainWorkControl;

        $insertResults->intrawork_environmental_demands = $transformEnviromentalDemands;
        $insertResults->intrawork_level_environmental_demands = $baremoEnviromentalDemands;
        $insertResults->intrawork_raw_environmental_demands = $dimensionEnviromentalDemands;

        $insertResults->intrawork_emotional_demands = $transformEmotionalDemands;
        $insertResults->intrawork_level_emotional_demands = $baremoEmotionalDemands;
        $insertResults->intrawork_raw_emotional_demands = $dimensionEmotionalDemands;

        $insertResults->intrawork_quantitative_demands = $transformQuantitativeDemands;
        $insertResults->intrawork_level_quantitative_demands = $baremoQuantitativeDemands;
        $insertResults->intrawork_raw_quantitative_demands = $dimensionQuantitativeDemands;

        $insertResults->intrawork_work_influence = $transformWorkInfluence;
        $insertResults->intrawork_level_work_influence = $baremoWorkInfluence;
        $insertResults->intrawork_raw_work_influence = $dimensionWorkInfluence;

        $insertResults->intrawork_mental_load_demands = $transformMentalLoadDemands;
        $insertResults->intrawork_level_mental_load_demands = $baremoMentalLoadDemands;
        $insertResults->intrawork_raw_mental_load_demands = $dimensionMentalLoadDemands;

        $insertResults->intrawork_day_demands = $transformDayDemands;
        $insertResults->intrawork_level_day_demands = $baremoDayDemands;
        $insertResults->intrawork_raw_day_demands = $dimensionDayDemands;

        $insertResults->intrawork_work_demands = $transformWorkDemands;
        $insertResults->intrawork_level_work_demands = $baremoWorkDemands;
        $insertResults->intrawork_raw_work_demands = $domainWorkDemands;

        $insertResults->intrawork_rewards_work = $transformRewardsWork;
        $insertResults->intrawork_level_rewards_work = $baremoRewardsWork;
        $insertResults->intrawork_raw_rewards_work = $dimensionRewardsWork;

        $insertResults->intrawork_recognition = $transformRecognition;
        $insertResults->intrawork_level_recognition = $baremoRecognition;
        $insertResults->intrawork_raw_recognition = $dimensionRecognition;

        $insertResults->intrawork_rewards = $transformRewards;
        $insertResults->intrawork_level_rewards = $baremoRewards;
        $insertResults->intrawork_raw_rewards = $domainRewards;

        $insertResults->intrawork_results_general = $trasnformGeneralIntralaboral;
        $insertResults->intrawork_level_results_general = $baremoGeneralIntralaboral;
        $insertResults->intrawork_raw_results_general = $generalIntralaboral;

        $QuestionnaireExits->state_intrawork = 'Realizado';

        if ($insertResults->results_cap_intra == 0) {
            if ($insertResults->intrawork_level_results_general == "Riesgo muy alto" && $insertResults->stress_level_results_general == "Muy alto") {
                $insertResults->results_cap_intra++;
            } elseif ($insertResults->intrawork_level_results_general == "Riesgo muy alto" && $insertResults->stress_level_results_general == "Alto") {
                $insertResults->results_cap_intra++;
            } elseif ($insertResults->intrawork_level_results_general == "Riesgo alto" && $insertResults->stress_level_results_general == "Muy alto") {
                $insertResults->results_cap_intra++;
            } elseif ($insertResults->intrawork_level_results_general == "Riesgo alto" && $insertResults->stress_level_results_general == "Alto") {
                $insertResults->results_cap_intra++;
            }
        }

        if ($insertResults->results_cap_intra == 1 && $insertResults->results_cap_extra == 1 && $insertResults->results_cap_intra_extra == 0) {
            $insertResults->results_cap_intra_extra++;
        }

        if ($insertResults->results_stress_no_specific == 0) {
            if ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" &&  $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } else {
                $insertResults->results_stress_no_specific = 0;
            }
        }

        if (
            $insertResults->company_id == null &&
            $insertResults->city == null &&
            $insertResults->first_level == null &&
            $insertResults->second_level == null &&
            $insertResults->third_level == null &&
            $insertResults->fourth_level == null &&
            $insertResults->fifth_level == null &&
            $insertResults->sixth_level == null &&
            $insertResults->seventh_level == null &&
            $insertResults->eighth_level == null
        ) {
            $insertResults->type_questionnaire = $questionnaireType;
            $insertResults->company_id = $resultExistsEmployee->company_id;
            $insertResults->city = $resultExistsEmployee->city;
            $insertResults->position = $resultExistsEmployee->position;

            $insertResults->first_level = $resultExistsEmployee->first_level;
            $insertResults->second_level = $resultExistsEmployee->second_level;
            $insertResults->third_level = $resultExistsEmployee->third_level;
            $insertResults->fourth_level = $resultExistsEmployee->fourth_level;
            $insertResults->fifth_level = $resultExistsEmployee->fifth_level;
            $insertResults->sixth_level = $resultExistsEmployee->sixth_level;
            $insertResults->seventh_level = $resultExistsEmployee->seventh_level;
            $insertResults->eighth_level = $resultExistsEmployee->eighth_level;
        }

        if ($insertResults->save() && $QuestionnaireExits->save() && $answersIntraWork->save()) {
            return response()->json(['message' => "Se han registrado las respuestas exitosamente."], 200);
        } else {
            return response()->json(['error' => "No se ha podido registrar las respuestas.", 'Questionnaire' => 'Intalaboral'], 500);
        }
    }

    public function migrateQuestionnaireGeneralData()
    {
        $Caliriesgo = fichadatosgenerales::all();

        foreach ($Caliriesgo as $answerOld) {
            $Employees = Employees::where('document_employee', '=', $answerOld->cc)
                ->join('psychosocial_questionnaires', 'psychosocial_questionnaires.employee_id', '=', 'psychosocial_employees.employee_id')
                ->first();

            if (!$Employees) {
                continue;
            }

            $fechaOriginal = $answerOld->fechaaplicacion;
            $fechaFormateada = DateTime::createFromFormat('d/m/Y', $fechaOriginal)->format('Y-m-d');

            $data = [
                "questionnaire_id" => $Employees->questionnaire_id,
                "response_date" => $fechaFormateada,
                "gender" => $answerOld->sexo,
                "birth_date" => $answerOld->anodenacimiento,
                "civil_status" => $answerOld->estadocivil,
                "level_study" => $answerOld->niveldeestudio,
                "occupation" => $answerOld->ocupacion,
                "municipality" => $answerOld->muniresidencia,
                "stratum" => $answerOld->estrato,
                "type_housing" => $answerOld->vivienda,
                "dependents" => $answerOld->personasacargo,
                "municipality_work" => $answerOld->munitrabajo,
                "years_work" => $answerOld->anostrabajo2,
                "position" => $answerOld->cargo,
                "position_type" => $answerOld->tipodecargo,
                "position_years" => $answerOld->añoscargo2,
                "area" => $answerOld->area,
                "type_contract" => $answerOld->contrato,
                "hours_work" => $answerOld->añoscargo2,
                "salary_type" => $answerOld->salario,
            ];

            $dataGeneral = new Request();
            $dataGeneral->replace($data);

            $this->createGeneralDataUser($dataGeneral);
        }
    }

    public function createGeneralDataUser(Request $request)
    {
        $Questionnaire = Questionnaires::where('questionnaire_id', $request->questionnaire_id)->first();

        if (!$Questionnaire) {
            return;
        }

        $birthDate = $request->birth_date;

        if ($birthDate != '') {
            if (preg_match('/^\d{4}$/', $birthDate)) {
                $birthDate .= '-01-01';
            }
        } else {
            $birthDate = null;
        }

        $civil_status = $request->civil_status;

        if ($civil_status == 'Union libre') {
            $civil_status = 'Unión libre';
        }

        $level_study = $request->level_study;

        if ($level_study == 'Tecnico / tecnologico incompleto') {
            $level_study = 'Técnico / tecnológico incompleto';
        } else if ($level_study == 'Tecnico / tecnologico completo') {
            $level_study = 'Técnico / tecnológico completo';
        } else if ($level_study == 'Carrera militar / policia') {
            $level_study = 'Carrera militar / policía';
        } else if ($level_study == 'ninguno') {
            $level_study = 'Ninguno';
        }

        $stratum = $request->stratum;

        if ($stratum == 'No se') {
            $stratum = 'No sé';
        }

        $position_type = $request->position_type;

        if ($position_type == 'Auxiliar, asistente administrativo, asistente tecnico') {
            $position_type = 'Auxiliar, asistente administrativo, asistente técnico';
        } else if ($position_type == 'Profesional, analista, tecnico, tecnologo') {
            $position_type = 'Profesional, analista, técnico, tecnólogo';
        }

        $type_contract = $request->type_contract;

        if ($type_contract == 'Temporal de menos de 1 ano') {
            $type_contract = 'Temporal de menos de 1 año';
        } else if ($type_contract == 'Temporal de 1 ano o mas' || $type_contract == 'Temporal de 1 año o mas' || $type_contract == 'Temporal de 1 ano o más') {
            $type_contract = 'Temporal de 1 año o más';
        } else if ($type_contract == 'Termino indefinido') {
            $type_contract = 'Término indefinido';
        } else if ($type_contract == 'Prestacion de servicios') {
            $type_contract = 'Prestación de servicios';
        } else if ($type_contract == 'No se' || $type_contract == 'no se') {
            $type_contract = 'No sé';
        }

        $salary_type = $request->salary_type;

        if ($salary_type == 'Todo vaiable (a destajo, por producción, por comision)' || $salary_type == 'Temporal de 1 año o mas' || $salary_type == 'Todo vaiable (a destajo, por produccion, por comision)' || $salary_type == 'Todo vaiable (a destajo, por produccion, por comisión)') {
            $salary_type = 'Todo vaiable (a destajo, por producción, por comisión)';
        }

        $age = Carbon::parse($birthDate)->age;

        if ($age == '') {
            $rangeAge = '';
        } else if ($age <= 20) {
            $rangeAge = '16 a 20';
        } else if ($age <= 30) {
            $rangeAge = '21 a 30';
        } else if ($age <= 40) {
            $rangeAge = '31 a 40';
        } else if ($age <= 50) {
            $rangeAge = '41 a 50';
        } else if ($age <= 60) {
            $rangeAge = '51 a 60';
        } else if ($age <= 70) {
            $rangeAge = '61 a 70';
        } else {
            $rangeAge = 'Mayor de 70';
        }

        if ($request->years_work == '') {
            $rangeYearsWork = '';
        } else if ($request->years_work < 1) {
            $rangeYearsWork = '0 a 0.9';
        } else if ($request->years_work <= 5) {
            $rangeYearsWork = '1 a 5';
        } else if ($request->years_work <= 10) {
            $rangeYearsWork = '6 a 10';
        } else if ($request->years_work <= 15) {
            $rangeYearsWork = '11 a 15';
        } else if ($request->years_work <= 20) {
            $rangeYearsWork = '16 a 20';
        } else if ($request->years_work <= 25) {
            $rangeYearsWork = '21 a 25';
        } else if ($request->years_work <= 30) {
            $rangeYearsWork = '26 a 30';
        } else if ($request->years_work <= 40) {
            $rangeYearsWork = '31 a 40';
        } else {
            $rangeYearsWork = 'Más de 40';
        }

        if ($request->position_years == '') {
            $rangePositionYears = '';
        } else if ($request->position_years < 1) {
            $rangePositionYears = '0 a 0.9';
        } else if ($request->position_years <= 5) {
            $rangePositionYears = '1 a 5';
        } else if ($request->position_years <= 10) {
            $rangePositionYears = '6 a 10';
        } else if ($request->position_years <= 15) {
            $rangePositionYears = '11 a 15';
        } else if ($request->position_years <= 20) {
            $rangePositionYears = '16 a 20';
        } else if ($request->position_years <= 25) {
            $rangePositionYears = '21 a 25';
        } else if ($request->position_years <= 30) {
            $rangePositionYears = '26 a 30';
        } else if ($request->position_years <= 40) {
            $rangePositionYears = '31 a 40';
        } else if ($request->position_years >= 41) {
            $rangePositionYears = 'Más de 40';
        } else {
            $rangePositionYears = '';
        }

        $positionYears = $request->position_years;
        if (!is_numeric($positionYears)) {
            $positionYears = 0;
        }

        $hoursWork = $request->hours_work;
        if (!is_numeric($hoursWork)) {
            $hoursWork = 0;
        }

        $QuestionnaireGeneralData = GeneralData::updateOrCreate(
            ['questionnaire_id' => $request->questionnaire_id],
            [
                'response_date' => $request->response_date,
                'type_questionarie' => $Questionnaire->type_questionarie,
                'gender' => ucfirst(strtolower($request->gender)),
                'birth_date' => $birthDate,
                'user_years' => $rangeAge,
                'civil_status' => $civil_status,
                'level_study' => $level_study,
                'occupation' => $request->occupation,
                'municipality' => $request->municipality,
                'stratum' => $stratum,
                'type_housing' => $request->type_housing,
                'dependents' => $request->dependents,
                'municipality_work' => $request->municipality_work,
                'years_work' => $request->years_work,
                'range_years_work' => $rangeYearsWork,
                'position' => $request->position,
                'position_type' => $position_type,
                'position_years' => $positionYears,
                'range_position_years' => $rangePositionYears,
                'area' => $request->area,
                'type_contract' => $type_contract,
                'hours_work' => $hoursWork,
                'salary_type' => $salary_type,
            ]
        );

        $Results = new Results();
        $insertResults = $Results->where('psychosocial_results.questionnaire_id', $request->questionnaire_id)
            ->first();

        if ($insertResults) {
            $insertResults->age = $rangeAge;
            $insertResults->gender = $request->gender;
            $insertResults->position_years = $rangeYearsWork;
            $insertResults->work_years = $rangePositionYears;
            $insertResults->save();
        }

        $Questionnaire->state_general_data = 'Realizado';

        if ($QuestionnaireGeneralData->save() && $Questionnaire->save()) {
            return response()->json(['message' => "Se ha creado un cuestionario de ficha de datos generales correctamente"], 200);
        } else {
            return response()->json(['error' => "No hemos podido crear el cuestionario de ficha de datos generales, revisa tu petición", 'Questionnaire' => 'Ficha de datos generales'], 500);
        }
    }

    public function migrateQuestionnaireExtrawork()
    {
        $Caliriesgo = caliextralaborales::all();

        foreach ($Caliriesgo as $answerOld) {
            $Employees = Employees::where('document_employee', '=', $answerOld->cc)
                ->join('psychosocial_questionnaires', 'psychosocial_questionnaires.employee_id', '=', 'psychosocial_employees.employee_id')
                ->first();

            if (!$Employees) {
                continue;
            }

            $fechaOriginal = $answerOld->fechaaplicacion;
            $fechaFormateada = DateTime::createFromFormat('d/m/Y', $fechaOriginal)->format('Y-m-d');

            $data = [
                "questionnaire_id" => $Employees->questionnaire_id,
                "response_date" => $fechaFormateada,
                "answer_1" => $answerOld->r1,
                "answer_2" => $answerOld->r2,
                "answer_3" => $answerOld->r3,
                "answer_4" => $answerOld->r4,
                "answer_5" => $answerOld->r5,
                "answer_6" => $answerOld->r6,
                "answer_7" => $answerOld->r7,
                "answer_8" => $answerOld->r8,
                "answer_9" => $answerOld->r9,
                "answer_10" => $answerOld->r10,
                "answer_11" => $answerOld->r11,
                "answer_12" => $answerOld->r12,
                "answer_13" => $answerOld->r13,
                "answer_14" => $answerOld->r14,
                "answer_15" => $answerOld->r15,
                "answer_16" => $answerOld->r16,
                "answer_17" => $answerOld->r17,
                "answer_18" => $answerOld->r18,
                "answer_19" => $answerOld->r19,
                "answer_20" => $answerOld->r20,
                "answer_21" => $answerOld->r21,
                "answer_22" => $answerOld->r22,
                "answer_23" => $answerOld->r23,
                "answer_24" => $answerOld->r24,
                "answer_25" => $answerOld->r25,
                "answer_26" => $answerOld->r26,
                "answer_27" => $answerOld->r27,
                "answer_28" => $answerOld->r28,
                "answer_29" => $answerOld->r29,
                "answer_30" => $answerOld->r30,
                "answer_31" => $answerOld->r31
            ];

            $dataIntrawork = new Request();
            $dataIntrawork->replace($data);

            $this->registerExtrawork($dataIntrawork);
        }
    }

    public function registerExtrawork(Request $request)
    {
        $QuestionnaireExits = Questionnaires::where('questionnaire_id', $request->questionnaire_id)->first();

        if (!$QuestionnaireExits) {
            return;
        }

        $Employee = DB::table('psychosocial_questionnaires')
            ->select(
                'psychosocial_employees.position_type',
                'psychosocial_questionnaires.type_questionarie',
            )
            ->join('psychosocial_employees', 'psychosocial_questionnaires.employee_id', '=', 'psychosocial_employees.employee_id')
            ->get();

        $rawTime = null;
        $rawFamilyRelationships = null;
        $rawCommunication = null;
        $rawSituation = null;
        $rawHousingFeatures = null;
        $rawInfluence = null;
        $rawDisplacement = null;

        for ($i = 1; $i <= 31; $i++) {
            if (
                $request["answer_$i"] == 'no aplica'
                || $request["answer_$i"] == 'no responde'
                || $request["answer_$i"] == 'no respondio'
            ) {
                $request["answer_$i"] = '';
                continue;
            }

            if (
                $i == 14
                || $i == 15
                || $i == 16
                || $i == 17
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $rawTime += $request["answer_$i"];
                }
            }

            if (
                $i == 22
                || $i == 25
                || $i == 27
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $rawFamilyRelationships += $request["answer_$i"];
                }
            }

            if (
                $i == 18
                || $i == 19
                || $i == 20
                || $i == 21
                || $i == 23
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $rawCommunication += $request["answer_$i"];
                }
            }

            if (
                $i == 29
                || $i == 30
                || $i == 31
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $rawSituation += $request["answer_$i"];
                }
            }

            if (
                $i == 5
                || $i == 6
                || $i == 7
                || $i == 8
                || $i == 9
                || $i == 10
                || $i == 11
                || $i == 12
                || $i == 13
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $rawHousingFeatures += $request["answer_$i"];
                }
            }

            if (
                $i == 24
                || $i == 26
                || $i == 28
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $rawInfluence += $request["answer_$i"];
                }
            }

            if (
                $i == 1
                || $i == 2
                || $i == 3
                || $i == 4
            ) {
                if (is_numeric($request["answer_$i"])) {
                    $rawDisplacement += $request["answer_$i"];
                }
            }
        }

        $transformTime = null;
        $baremoTime = null;

        if ($rawTime >= 0) {
            $transformTime = round(($rawTime / 16) * 100, 2);

            if ($Employee[0]->position_type == 'Profesional, analista, técnico, tecnólogo' || $Employee[0]->position_type == 'Jefatura - tiene personal a cargo') {
                if ($transformTime <= 6.3) {
                    $baremoTime = "Sin riesgo o riesgo despreciable";
                } else if ($transformTime <= 25) {
                    $baremoTime = "Riesgo bajo";
                } else if ($transformTime <= 37.5) {
                    $baremoTime = "Riesgo medio";
                } else if ($transformTime <= 50) {
                    $baremoTime = "Riesgo alto";
                } else {
                    $baremoTime = "Riesgo muy alto";
                }
            } else {
                if ($transformTime <= 6.3) {
                    $baremoTime = "Sin riesgo o riesgo despreciable";
                } else if ($transformTime <= 25) {
                    $baremoTime = "Riesgo bajo";
                } else if ($transformTime <= 37.5) {
                    $baremoTime = "Riesgo medio";
                } else if ($transformTime <= 50) {
                    $baremoTime = "Riesgo alto";
                } else {
                    $baremoTime = "Riesgo muy alto";
                }
            }
        }

        $transformFamilyRelationships = null;
        $baremoFamilyRelationships = null;

        if ($rawFamilyRelationships >= 0) {
            $transformFamilyRelationships = round(($rawFamilyRelationships / 12) * 100, 2);

            if ($Employee[0]->position_type == 'Profesional, analista, técnico, tecnólogo' || $Employee[0]->position_type == 'Jefatura - tiene personal a cargo') {
                if ($transformFamilyRelationships <= 0.9) {
                    $baremoFamilyRelationships = "Sin riesgo o riesgo despreciable";
                } else if ($transformFamilyRelationships <= 8.3) {
                    $baremoFamilyRelationships = "Riesgo bajo";
                } else if ($transformFamilyRelationships <= 16.7) {
                    $baremoFamilyRelationships = "Riesgo medio";
                } else if ($transformFamilyRelationships <= 25) {
                    $baremoFamilyRelationships = "Riesgo alto";
                } else {
                    $baremoFamilyRelationships = "Riesgo muy alto";
                }
            } else {
                if ($transformFamilyRelationships <= 0.9) {
                    $baremoFamilyRelationships = "Sin riesgo o riesgo despreciable";
                } else if ($transformFamilyRelationships <= 8.3) {
                    $baremoFamilyRelationships = "Riesgo bajo";
                } else if ($transformFamilyRelationships <= 25) {
                    $baremoFamilyRelationships = "Riesgo medio";
                } else if ($transformFamilyRelationships <= 33.3) {
                    $baremoFamilyRelationships = "Riesgo alto";
                } else {
                    $baremoFamilyRelationships = "Riesgo muy alto";
                }
            }
        }

        $transformCommunication = null;
        $baremoCommunication = null;

        if ($rawCommunication >= 0) {
            $transformCommunication = round(($rawCommunication / 20) * 100, 2);

            if ($Employee[0]->position_type == 'Profesional, analista, técnico, tecnólogo' || $Employee[0]->position_type == 'Jefatura - tiene personal a cargo') {
                if ($transformCommunication <= 0.9) {
                    $baremoCommunication = "Sin riesgo o riesgo despreciable";
                } else if ($transformCommunication <= 10) {
                    $baremoCommunication = "Riesgo bajo";
                } else if ($transformCommunication <= 20) {
                    $baremoCommunication = "Riesgo medio";
                } else if ($transformCommunication <= 30) {
                    $baremoCommunication = "Riesgo alto";
                } else {
                    $baremoCommunication = "Riesgo muy alto";
                }
            } else {
                if ($transformCommunication <= 5) {
                    $baremoCommunication = "Sin riesgo o riesgo despreciable";
                } else if ($transformCommunication <= 15) {
                    $baremoCommunication = "Riesgo bajo";
                } else if ($transformCommunication <= 25) {
                    $baremoCommunication = "Riesgo medio";
                } else if ($transformCommunication <= 35) {
                    $baremoCommunication = "Riesgo alto";
                } else {
                    $baremoCommunication = "Riesgo muy alto";
                }
            }
        }

        $transformSituation = null;
        $baremoSituation = null;

        if ($rawSituation >= 0) {
            $transformSituation = round(($rawSituation / 12) * 100, 2);

            if ($Employee[0]->position_type == 'Profesional, analista, técnico, tecnólogo' || $Employee[0]->position_type == 'Jefatura - tiene personal a cargo') {
                if ($transformSituation <= 8.3) {
                    $baremoSituation = "Sin riesgo o riesgo despreciable";
                } else if ($transformSituation <= 25) {
                    $baremoSituation = "Riesgo bajo";
                } else if ($transformSituation <= 33.3) {
                    $baremoSituation = "Riesgo medio";
                } else if ($transformSituation <= 50) {
                    $baremoSituation = "Riesgo alto";
                } else {
                    $baremoSituation = "Riesgo muy alto";
                }
            } else {
                if ($transformSituation <= 16.7) {
                    $baremoSituation = "Sin riesgo o riesgo despreciable";
                } else if ($transformSituation <= 25) {
                    $baremoSituation = "Riesgo bajo";
                } else if ($transformSituation <= 41.7) {
                    $baremoSituation = "Riesgo medio";
                } else if ($transformSituation <= 50) {
                    $baremoSituation = "Riesgo alto";
                } else {
                    $baremoSituation = "Riesgo muy alto";
                }
            }
        }

        $transformHousingFeatures = null;
        $baremoHousingFeatures = null;

        if ($rawHousingFeatures >= 0) {
            $transformHousingFeatures = round(($rawHousingFeatures / 36) * 100, 2);

            if ($Employee[0]->position_type == 'Profesional, analista, técnico, tecnólogo' || $Employee[0]->position_type == 'Jefatura - tiene personal a cargo') {
                if ($transformHousingFeatures <= 5.6) {
                    $baremoHousingFeatures = "Sin riesgo o riesgo despreciable";
                } else if ($transformHousingFeatures <= 11.1) {
                    $baremoHousingFeatures = "Riesgo bajo";
                } else if ($transformHousingFeatures <= 13.9) {
                    $baremoHousingFeatures = "Riesgo medio";
                } else if ($transformHousingFeatures <= 22.2) {
                    $baremoHousingFeatures = "Riesgo alto";
                } else {
                    $baremoHousingFeatures = "Riesgo muy alto";
                }
            } else {
                if ($transformHousingFeatures <= 5.6) {
                    $baremoHousingFeatures = "Sin riesgo o riesgo despreciable";
                } else if ($transformHousingFeatures <= 11.1) {
                    $baremoHousingFeatures = "Riesgo bajo";
                } else if ($transformHousingFeatures <= 16.7) {
                    $baremoHousingFeatures = "Riesgo medio";
                } else if ($transformHousingFeatures <= 27.8) {
                    $baremoHousingFeatures = "Riesgo alto";
                } else {
                    $baremoHousingFeatures = "Riesgo muy alto";
                }
            }
        }

        $transformInfluence = null;
        $baremoInfluence = null;

        if ($rawInfluence >= 0) {
            $transformInfluence = round(($rawInfluence / 12) * 100, 2);

            if ($Employee[0]->position_type == 'Profesional, analista, técnico, tecnólogo' || $Employee[0]->position_type == 'Jefatura - tiene personal a cargo') {
                if ($transformInfluence <= 8.3) {
                    $baremoInfluence = "Sin riesgo o riesgo despreciable";
                } else if ($transformInfluence <= 16.7) {
                    $baremoInfluence = "Riesgo bajo";
                } else if ($transformInfluence <= 25) {
                    $baremoInfluence = "Riesgo medio";
                } else if ($transformInfluence <= 41.7) {
                    $baremoInfluence = "Riesgo alto";
                } else {
                    $baremoInfluence = "Riesgo muy alto";
                }
            } else {
                if ($transformInfluence <= 0.9) {
                    $baremoInfluence = "Sin riesgo o riesgo despreciable";
                } else if ($transformInfluence <= 16.7) {
                    $baremoInfluence = "Riesgo bajo";
                } else if ($transformInfluence <= 25) {
                    $baremoInfluence = "Riesgo medio";
                } else if ($transformInfluence <= 41.7) {
                    $baremoInfluence = "Riesgo alto";
                } else {
                    $baremoInfluence = "Riesgo muy alto";
                }
            }
        }

        $transformDisplacement = null;
        $baremoDisplacement = null;

        if ($rawDisplacement >= 0) {
            $transformDisplacement = round(($rawDisplacement / 16) * 100, 2);

            if ($Employee[0]->position_type == 'Profesional, analista, técnico, tecnólogo' || $Employee[0]->position_type == 'Jefatura - tiene personal a cargo') {
                if ($transformDisplacement <= 0.9) {
                    $baremoDisplacement = "Sin riesgo o riesgo despreciable";
                } else if ($transformDisplacement <= 12.5) {
                    $baremoDisplacement = "Riesgo bajo";
                } else if ($transformDisplacement <= 25) {
                    $baremoDisplacement = "Riesgo medio";
                } else if ($transformDisplacement <= 43.8) {
                    $baremoDisplacement = "Riesgo alto";
                } else {
                    $baremoDisplacement = "Riesgo muy alto";
                }
            } else {
                if ($transformDisplacement <= 0.9) {
                    $baremoDisplacement = "Sin riesgo o riesgo despreciable";
                } else if ($transformDisplacement <= 12.5) {
                    $baremoDisplacement = "Riesgo bajo";
                } else if ($transformDisplacement <= 25) {
                    $baremoDisplacement = "Riesgo medio";
                } else if ($transformDisplacement <= 43.8) {
                    $baremoDisplacement = "Riesgo alto";
                } else {
                    $baremoDisplacement = "Riesgo muy alto";
                }
            }
        }

        $rawExtrawork = $rawTime + $rawFamilyRelationships + $rawCommunication + $rawSituation + $rawHousingFeatures + $rawInfluence + $rawDisplacement;

        $transformExtrawork = null;
        $baremoExtrawork = null;

        if ($rawExtrawork >= 0) {
            $transformExtrawork = round(($rawExtrawork / 124) * 100, 2);

            if ($Employee[0]->position_type == 'Profesional, analista, técnico, tecnólogo' || $Employee[0]->position_type == 'Jefatura - tiene personal a cargo') {
                if ($transformExtrawork <= 11.3) {
                    $baremoExtrawork = "Sin riesgo o riesgo despreciable";
                } else if ($transformExtrawork <= 16.9) {
                    $baremoExtrawork = "Riesgo bajo";
                } else if ($transformExtrawork <= 22.6) {
                    $baremoExtrawork = "Riesgo medio";
                } else if ($transformExtrawork <= 29) {
                    $baremoExtrawork = "Riesgo alto";
                } else {
                    $baremoExtrawork = "Riesgo muy alto";
                }
            } else {
                if ($transformExtrawork <= 12.9) {
                    $baremoExtrawork = "Sin riesgo o riesgo despreciable";
                } else if ($transformExtrawork <= 17.7) {
                    $baremoExtrawork = "Riesgo bajo";
                } else if ($transformExtrawork <= 24.2) {
                    $baremoExtrawork = "Riesgo medio";
                } else if ($transformExtrawork <= 32.3) {
                    $baremoExtrawork = "Riesgo alto";
                } else {
                    $baremoExtrawork = "Riesgo muy alto";
                }
            }
        }

        $updateData = [
            'response_date' => $request->response_date,
            'answer_1' => $request->answer_1,
            'answer_2' => $request->answer_2,
            'answer_3' => $request->answer_3,
            'answer_4' => $request->answer_4,
            'answer_5' => $request->answer_5,
            'answer_6' => $request->answer_6,
            'answer_7' => $request->answer_7,
            'answer_8' => $request->answer_8,
            'answer_9' => $request->answer_9,
            'answer_10' => $request->answer_10,
            'answer_11' => $request->answer_11,
            'answer_12' => $request->answer_12,
            'answer_13' => $request->answer_13,
            'answer_14' => $request->answer_14,
            'answer_15' => $request->answer_15,
            'answer_16' => $request->answer_16,
            'answer_17' => $request->answer_17,
            'answer_18' => $request->answer_18,
            'answer_19' => $request->answer_19,
            'answer_20' => $request->answer_20,
            'answer_21' => $request->answer_21,
            'answer_22' => $request->answer_22,
            'answer_23' => $request->answer_23,
            'answer_24' => $request->answer_24,
            'answer_25' => $request->answer_25,
            'answer_26' => $request->answer_26,
            'answer_27' => $request->answer_27,
            'answer_28' => $request->answer_28,
            'answer_29' => $request->answer_29,
            'answer_30' => $request->answer_30,
            'answer_31' => $request->answer_31,
        ];

        $answersExtrawork = Extrawork::updateOrCreate(
            ['questionnaire_id' => $request->questionnaire_id],
            $updateData
        );

        $EmployeeLevels = new Employees();
        $resultExistsEmployee = $EmployeeLevels
            ->join('psychosocial_questionnaires', 'psychosocial_employees.employee_id', '=', 'psychosocial_questionnaires.employee_id')
            ->where('psychosocial_questionnaires.questionnaire_id', $request->questionnaire_id)
            ->select('psychosocial_employees.*')
            ->first();

        $measurement_id = $QuestionnaireExits->measurement_id;
        $questionnaireType = $QuestionnaireExits->type_questionarie;

        $Results = new Results();
        $resultExists = $Results->where('questionnaire_id', $request->questionnaire_id)
            ->where('measurement_id', $measurement_id)
            ->where('type_questionnaire', $questionnaireType)
            ->first();

        if ($resultExists != null) {
            // SI YA ESTÁ EL REGISTRO, SE DEBE ACTUALIZAR LOS DATOS
            $insertResults = $resultExists;

            // SI HAY DATOS EN EXTRALABORAL, SE DEBE ACTUALIZAR EL INTRA-EXTRA
            if ($insertResults->intrawork_raw_results_general != null) {
                $generalIntraworkExtrawork = $insertResults->intrawork_raw_results_general + $rawExtrawork;

                if ($Employee[0]->type_questionarie == 'A') {
                    $transformIntraExtraWork = round(($generalIntraworkExtrawork / 616) * 100, 2);

                    if ($transformIntraExtraWork <= 18.8) {
                        $baremoGeneralIntraExtrawork = "Sin riesgo o riesgo despreciable";
                    } else if ($transformIntraExtraWork <= 24.4) {
                        $baremoGeneralIntraExtrawork = "Riesgo bajo";
                    } else if ($transformIntraExtraWork <= 29.5) {
                        $baremoGeneralIntraExtrawork = "Riesgo medio";
                    } else if ($transformIntraExtraWork <= 35.4) {
                        $baremoGeneralIntraExtrawork = "Riesgo alto";
                    } else {
                        $baremoGeneralIntraExtrawork = "Riesgo muy alto";
                    }

                    $insertResults->general_intrawork_extrawork = $transformIntraExtraWork;
                    $insertResults->general_results_intrawork_extrawork = $baremoGeneralIntraExtrawork;
                    $insertResults->general_raw_intrawork_extrawork = $generalIntraworkExtrawork;
                } else {
                    $transformIntraExtraWork = ($generalIntraworkExtrawork / 512) * 100;

                    if ($transformIntraExtraWork <= 19.9) {
                        $baremoGeneralIntraExtrawork = "Sin riesgo o riesgo despreciable";
                    } else if ($transformIntraExtraWork <= 24.4) {
                        $baremoGeneralIntraExtrawork = "Riesgo bajo";
                    } else if ($transformIntraExtraWork <= 29.5) {
                        $baremoGeneralIntraExtrawork = "Riesgo medio";
                    } else if ($transformIntraExtraWork <= 35.4) {
                        $baremoGeneralIntraExtrawork = "Riesgo alto";
                    } else {
                        $baremoGeneralIntraExtrawork = "Riesgo muy alto";
                    }

                    if ($insertResults->results_cap_extra == 0) {
                        if ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->extrawork_level_results_general == "Riesgo muy alto") {
                            $insertResults->results_cap_extra++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->extrawork_level_results_general == "Riesgo muy alto") {
                            $insertResults->results_cap_extra++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->extrawork_level_results_general == "Riesgo alto") {
                            $insertResults->results_cap_extra++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->extrawork_level_results_general == "Riesgo alto") {
                            $insertResults->results_cap_extra++;
                        }
                    }

                    if ($insertResults->results_cap_intra == 1 && $insertResults->results_cap_extra == 1 && $insertResults->results_cap_intra_extra == 0) {
                        $insertResults->results_cap_intra_extra++;
                    }

                    if ($insertResults->results_stress_no_specific == 0) {
                        if ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                            $insertResults->results_stress_no_specific++;
                        } elseif ($insertResults->stress_level_results_general == "Alto" &&  $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                            $insertResults->results_stress_no_specific++;
                        } else {
                            $insertResults->results_stress_no_specific = 0;
                        }
                    }

                    $insertResults->general_intrawork_extrawork = $transformIntraExtraWork;
                    $insertResults->general_results_intrawork_extrawork = $baremoGeneralIntraExtrawork;
                    $insertResults->general_raw_intrawork_extrawork = $generalIntraworkExtrawork;
                }
            }
        } else {
            $insertResults = $Results;
        }

        $insertResults->questionnaire_id = $request->questionnaire_id;
        $insertResults->measurement_id = $measurement_id;
        $insertResults->type_questionnaire = $questionnaireType;

        $insertResults->extrawork_time = $transformTime;
        $insertResults->extrawork_level_time = $baremoTime;
        $insertResults->extrawork_raw_time = $rawTime;

        $insertResults->extrawork_family_relationships = $transformFamilyRelationships;
        $insertResults->extrawork_level_family_relationships = $baremoFamilyRelationships;
        $insertResults->extrawork_raw_family_relationships = $rawFamilyRelationships;

        $insertResults->extrawork_communication = $transformCommunication;
        $insertResults->extrawork_level_communication = $baremoCommunication;
        $insertResults->extrawork_raw_communication = $rawCommunication;

        $insertResults->extrawork_situation = $transformSituation;
        $insertResults->extrawork_level_situation = $baremoSituation;
        $insertResults->extrawork_raw_situation = $rawSituation;

        $insertResults->extrawork_housing_features = $transformHousingFeatures;
        $insertResults->extrawork_level_housing_features = $baremoHousingFeatures;
        $insertResults->extrawork_raw_housing_features = $rawHousingFeatures;

        $insertResults->extrawork_influence = $transformInfluence;
        $insertResults->extrawork_level_influence = $baremoInfluence;
        $insertResults->extrawork_raw_influence = $rawInfluence;

        $insertResults->extrawork_displacement = $transformDisplacement;
        $insertResults->extrawork_level_displacement = $baremoDisplacement;
        $insertResults->extrawork_raw_displacement = $rawDisplacement;

        $insertResults->extrawork_results_general = $transformExtrawork;
        $insertResults->extrawork_level_results_general = $baremoExtrawork;
        $insertResults->extrawork_raw_results_general = $rawExtrawork;

        if ($insertResults->results_cap_extra == 0) {
            if ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->extrawork_level_results_general == "Riesgo muy alto") {
                $insertResults->results_cap_extra++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->extrawork_level_results_general == "Riesgo muy alto") {
                $insertResults->results_cap_extra++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->extrawork_level_results_general == "Riesgo alto") {
                $insertResults->results_cap_extra++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->extrawork_level_results_general == "Riesgo alto") {
                $insertResults->results_cap_extra++;
            }
        }

        if ($insertResults->results_cap_intra == 1 && $insertResults->results_cap_extra == 1 && $insertResults->results_cap_intra_extra == 0) {
            $insertResults->results_cap_intra_extra++;
        }

        if ($insertResults->results_stress_no_specific == 0) {
            if ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo bajo" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo bajo") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Sin riesgo o riesgo despreciable" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Sin riesgo o riesgo despreciable") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Muy alto" && $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } elseif ($insertResults->stress_level_results_general == "Alto" &&  $insertResults->intrawork_level_results_general == "Riesgo medio" && $insertResults->extrawork_level_results_general == "Riesgo medio") {
                $insertResults->results_stress_no_specific++;
            } else {
                $insertResults->results_stress_no_specific = 0;
            }
        }

        if (
            $insertResults->company_id == null &&
            $insertResults->city == null &&
            $insertResults->first_level == null &&
            $insertResults->second_level == null &&
            $insertResults->third_level == null &&
            $insertResults->fourth_level == null &&
            $insertResults->fifth_level == null &&
            $insertResults->sixth_level == null &&
            $insertResults->seventh_level == null &&
            $insertResults->eighth_level == null
        ) {
            $insertResults->type_questionnaire = $questionnaireType;
            $insertResults->company_id = $resultExistsEmployee->company_id;
            $insertResults->city = $resultExistsEmployee->city;
            $insertResults->position = $resultExistsEmployee->position;

            $insertResults->first_level = $resultExistsEmployee->first_level;
            $insertResults->second_level = $resultExistsEmployee->second_level;
            $insertResults->third_level = $resultExistsEmployee->third_level;
            $insertResults->fourth_level = $resultExistsEmployee->fourth_level;
            $insertResults->fifth_level = $resultExistsEmployee->fifth_level;
            $insertResults->sixth_level = $resultExistsEmployee->sixth_level;
            $insertResults->seventh_level = $resultExistsEmployee->seventh_level;
            $insertResults->eighth_level = $resultExistsEmployee->eighth_level;
        }

        $QuestionnaireExits->state_extrawork = 'Realizado';

        if ($insertResults->save() && $answersExtrawork && $QuestionnaireExits->save()) {
            return response()->json(['message' => "Se han registrado las respuestas exitosamente."], 200);
        } else {
            return response()->json(['error' => "No se ha podido registrar las respuestas.", 'Questionnaire' => 'Extralaboral'], 500);
        }
    }
}
