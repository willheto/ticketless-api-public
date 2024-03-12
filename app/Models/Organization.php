<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Lumen\Auth\Authorizable;

class Organization extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'organizationID';

    protected $fillable = [
        'name',
        'location',
        'license',
    ];

    public static function getValidationRules(array $fieldsToValidate): array
    {
        $validationRules =  [
            'name' => ['string', 'required', 'max:100'],
            'location' => ['string', 'required', 'max:100'],
            'license' => ['string', 'required', 'in:free,basic,pro'],
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
