<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;

class Announcement extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'announcementID';

    protected $fillable = [
        'title',
        'content',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public static function getValidationRules(array $fieldsToValidate): array
    {
        $validationRules =  [
            'title' => ['required', 'string', 'max:100'],
            'content' => ['required', 'string', 'max:200'],
            'isActive' => ['boolean'],
        ];

        if (
            empty($fieldsToValidate)
        ) {
            return $validationRules;
        }

        $filteredRules = array_intersect_key($validationRules, $fieldsToValidate);

        return $filteredRules;
    }
}
