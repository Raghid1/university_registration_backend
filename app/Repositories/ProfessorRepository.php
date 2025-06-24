<?php

namespace App\Repositories;
use App\Models\Professor;
use App\Repositories\Interfaces\ProfessorRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class ProfessorRepository extends BaseRepository implements ProfessorRepositoryInterface{

    protected $model;
    public function __construct(Professor $model){
        parent::__construct($model);
    }

    public function findByEmail(string $email) : ?Professor{
        $professor= $this->model->where('email',$email);
        return $professor;
    }

    public function getCourses(int $professorId): Collection{
        $professor = $this->find($professorId);
        return $professor ? $professor->courses : new Collection();
    }
}