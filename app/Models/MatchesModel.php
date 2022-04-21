<?php

namespace App\Models;

use CodeIgniter\Model;

class MatchesModel extends Model
{
    protected $table      = 'matches';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $allowedFields = ['id','name','utcDate','status','score','homeTeam','awayTeam','referees'];

}