<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Database\QueryException;

class AddressService
{
     // Busca um endereço por CEP (aceita com ou sem hífen).
     // Retorna null se não encontrar.
    public function findByCep(string $cep): ?Address
    {
        $cep8 = $this->normalizeCep($cep);
        if ($cep8 === null) {
            // regra defensiva: se chegar um CEP inválido aqui, apenas não encontra
            return null;
        }

        return Address::where('cep', $cep8)->first();
    }

    
     // Cadastra um endereço.
     // Espera um array com: cep, localidade, uf (obrigatórios) e logradouro/bairro (opcionais).
     // Lança \DomainException em caso de CEP duplicado.

    public function create(array $data): Address
    {
        $cep8 = $this->normalizeCep($data['cep'] ?? '');

        if ($cep8 === null) {
            throw new \InvalidArgumentException('CEP inválido (esperado 8 dígitos, com ou sem hífen).');
        }

        // normaliza dados mínimos
        $payload = [
            'cep'        => $cep8,
            'logradouro' => $data['logradouro'] ?? null,
            'bairro'     => $data['bairro'] ?? null,
            'localidade' => $data['localidade'] ?? '',
            'uf'         => strtoupper($data['uf'] ?? ''),
        ];

        try {
            return Address::create($payload);
        } catch (QueryException $e) {
            // código 23000 costuma indicar violação de chave única (CEP duplicado)
            if ($this->isUniqueConstraintViolation($e)) {
                throw new \DomainException('CEP já cadastrado.');
            }
            throw $e; 
        }
    }

    
    // Converte "12345-678" -> "12345678".
    private function normalizeCep(string $raw): ?string
    {
        $onlyDigits = preg_replace('/\D/', '', (string) $raw);
        return (strlen($onlyDigits) === 8) ? $onlyDigits : null;
    }

    
    // Detecta violação de unique constraint de forma portátil.
     
    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;  
        $driverCode = $e->errorInfo[1] ?? null; 
        return $sqlState === '23000' || $driverCode === 1062;
    }
}
