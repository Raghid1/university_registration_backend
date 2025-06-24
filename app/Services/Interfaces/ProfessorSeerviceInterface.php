<?php

use App\Repositories\ProfessorRepository;
use App\Models\Professor;

interface ProfessorSeerviceInterface{

    function findByEmail(string $email) : ?Professor;
    
} 