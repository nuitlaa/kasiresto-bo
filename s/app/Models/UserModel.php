<?php

namespace App\Models;

class UserModel extends BaseModel{
    protected $table      = 'account';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name', 'foto', 'type'
    ];
}
