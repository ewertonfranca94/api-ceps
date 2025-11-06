<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CepController extends Controller
{
    public function show(string $cep)
    {
        return response()->json([
            'message' => 'endpoint funcionando!',
            'cep_recebido' => $cep
        ]);
    }
}
