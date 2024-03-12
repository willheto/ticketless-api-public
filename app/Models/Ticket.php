<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;


class Ticket extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'ticketID';

    /**
     * @var array
     */
    protected $foreignKey = ['userID', 'eventID'];

    protected $casts = [
        'requiresMembership' => 'boolean',
        'isSelling' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $fillable = [
        'userID',
        'eventID',
        'header',
        'description',
        'price',
        'quantity',
        'requiresMembership',
        'association',
        'isSelling',
    ];


    public static function getValidationRules(array $fieldsToValidate): array
    {
        $validationRules =  [
            'userID' => ['required', 'integer', 'exists:users,userID'],
            'eventID' => ['required', 'integer', 'exists:events,eventID'],
            'header' => ['required', 'string', 'max:100'],
            'description' => ['string', 'max: 200'],
            'price' => ['required_if:isSelling, true', 'numeric', 'min:0', 'max:1000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:5'],
            'requiresMembership' => 'boolean',
            'association' => 'string',
            'isSelling' => 'boolean',
        ];
        if (
            empty($fieldsToValidate)
        ) {
            return $validationRules;
        }

        // Filter the rules based on the posted fields
        $filteredRules = array_intersect_key($validationRules, $fieldsToValidate);

        return $filteredRules;
    }
}
