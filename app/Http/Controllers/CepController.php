<?php

namespace App\Http\Controllers;
use App\Http\Requests\CepLookupRequest;
use Illuminate\Http\Request;


class CepController extends Controller
{
    public function show(CepLookupRequest $request)
    {
        // CEP Validado
        $cep = $request->validated()['cep'];

        return response()->json([
            'message' => 'CEP validado com sucesso',
            'cep' => $cep,
        ]);
    }
}
