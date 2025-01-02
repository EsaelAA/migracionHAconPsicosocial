<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleados2024 extends Model
{
    protected $table = 'empleados';

    protected $primaryKey = 'id';

    protected $fillable = [
        'cc',
        'nombres',
        'apellidos',
        'nit',
        'cargo',
        'email',
        'empresa',
        'pruebaintralaboralA',
        'pruebaintralaboralB',
        'pruebaextralaboral',
        'pruebaestres',
        'pruebaclima',
        'notas',
        'regional',
        'oficina',
        'sede',
        'ciclos',
        'sucursales',
        'proceso',
        'subproceso',
        'oficina2',
        'oficina2',
        'ciclos2',
        'consentimiento',
        'tratamientoDatos',
        'fechaTratamiento',
        'fechaConsentimiento',
    ];
}
