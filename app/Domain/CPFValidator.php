<?php

namespace App\Domain;

use App\Application\Exceptions\InvalidCPFException;

class CPFValidator
{
    public function __construct(private $cpf)
    {
        $this->sanitize();
        if (empty($this->cpf) || $this->isValidLength() || $this->isRepeated()) {
            throw new InvalidCPFException();
        }
    }

    public function validate()
    {
        $digit = $this->calculateDigit(10);
        $digit2 = $this->calculateDigit(11);
        $extractedDigit = $this->extractDigit();
        $calculatedDigit = "$digit$digit2";
        return $extractedDigit === $calculatedDigit;
    }

    private function sanitize(): void
    {
        $this->cpf = preg_replace('/\D/', '', $this->cpf);
    }

    private function isRepeated() : bool
    {
        $repeated = true;
        for ($i = 0; $i < 10; $i++) {
            if ($this->cpf[$i] !== $this->cpf[$i + 1]) {
                $repeated = false;
            }
        }
        return $repeated;
    }

    private function calculateDigit( $factor) : string
    {
        $total = 0;
        $splitedCpf = str_split($this->cpf);
        foreach ($splitedCpf as $digit) {
            if($factor>1){
                $total += ((int)$digit) * $factor--;
            }
        }
        $rest = $total%11;
        return $rest < 2 ? 0 : 11 - $rest;
    }

    public function extractDigit(): string
    {
        return substr($this->cpf, 9, 2);
    }

    public function isValidLength(): bool
    {
        return strlen($this->cpf) !== 11;
    }

}
