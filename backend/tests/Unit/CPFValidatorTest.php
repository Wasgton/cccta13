<?php

namespace Tests\Unit;

use App\Application\Exceptions\InvalidCPFException;
use App\Domain\CPFValidator;
use Tests\TestCase;

class CPFValidatorTest extends TestCase
{
    /**
     * @throws InvalidCPFException
     */
    public function test_should_validate_a_correct_cpf()
    {
        $validator = new CPFValidator($this->faker->cpf());
        $result = $validator->validate();
        $this->assertTrue($result);
    }

    public function test_should_not_validate_a_incorrect_cpf()
    {
        $validator = new CPFValidator('12345678900');
        $result = $validator->validate();
        $this->assertFalse($result);
    }

    /**
     * @throws InvalidCPFException
     */
    public function test_should_create_and_validator_instance_with_valid_cpf()
    {
        $this->assertInstanceOf(CPFValidator::class, new CPFValidator($this->faker->cpf()));
    }

    public function test_should_trigger_an_exception_if_cpf_is_empty()
    {
        $this->expectException(InvalidCPFException::class);
        $validator = new CPFValidator('');
    }

    public function test_should_trigger_an_exception_if_cpf_has_less_than_11_digits()
    {
        $this->expectException(InvalidCPFException::class);
        $validator = new CPFValidator($this->faker->numerify('##########'));
    }

    public function test_should_trigger_an_exception_if_cpf_has_repeated_digits()
    {
        $this->expectException(InvalidCPFException::class);
        $validator1 = new CPFValidator($this->faker->numerify('000.000.000-00'));
        $validator2 = new CPFValidator($this->faker->numerify('111.111.111.11'));
        $validator3 = new CPFValidator($this->faker->numerify('999.999.999.99'));
    }

}