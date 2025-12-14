<?php

namespace App\Services;

class SpecialistService
{
    protected $specialistRepository;

    public function __construct($specialistRepository)
    {
        $this->specialistRepository = $specialistRepository;
    }

    // Service methods for Specialist entity
}
