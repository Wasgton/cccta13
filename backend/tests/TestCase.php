<?php

namespace Tests;

use Faker\Provider\pt_BR\Person;
use PHPUnit\Framework\TestCase as baseTestCase;

class TestCase extends baseTestCase
{
    protected $faker;

    protected function setUp(): void
    {
        $this->faker = \Faker\Factory::create();
        $this->faker->addProvider(new Person($this->faker));
        parent::setUp();
    }

}