<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;

class Advertisement extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'advertisementID';

    protected $casts = [
        'isActive' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $fillable = [
        'advertiser',
        'contentHtml',
        'isActive',
        'views',
        'clicks',
        'redirectUrl',
        'type',
        'location',
    ];

    public static function getValidationRules(array $fieldsToValidate): array
    {
        $validationRules =  [
            'advertiser' => ['required', 'string', 'max:100'],
            'contentHtml' => ['required', 'string', 'max:20000'],
            'isActive' => ['boolean'],
            'views' => 'integer',
            'clicks' => 'integer',
            'redirectUrl' => ['string', 'max:1000'],
            'type' => ['required', 'string', 'in:local,global,toast'],
            'location' => [
                'string',
                'required_if:type,local'
            ],

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
