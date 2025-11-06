<?php

namespace App\Http\Controllers;

use App\Http\Requests\CepLookupRequest;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;


class CepController extends Controller
{
    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function show(CepLookupRequest $request): JsonResponse
    {
    // CEP já validado e normalizado pelo FormRequest
    $cep = $request->validated()['cep'];

    $address = $this->addressService->findByCep($cep);

    if (!$address){
        return response()->json([
            'message' => 'CEP não encontrado no banco de dados'
        ], 404);
    }
    // Formata o CEP com hífen antes de retornar
        $cepFormatado = substr($address->cep, 0, 5) . '-' . substr($address->cep, 5);

        return response()->json([
            'logradouro'     => $address->logradouro,
            'bairro'         => $address->bairro,
            'localidade_uf'  => "{$address->localidade}/{$address->uf}",
            'cep'            => $cepFormatado,
        ]);
    }
}