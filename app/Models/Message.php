<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;

class Message extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'messageID';

    /**
     * @var array
     */
    protected $foreignKey = ['senderID'];

    protected $casts = [
        'isRead' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $fillable = [
        'chatID',
        'senderID',
        'receiverID',
        'content',
        'isRead',
    ];

    public static function getValidationRules(array $fieldsToValidate): array
    {
        $validationRules =  [
            'chatID' => ['required', 'integer', 'exists:chats,chatID'],
            'senderID' => ['integer', 'exists:users,userID'],
            'receiverID' => ['required', 'integer', 'exists:users,userID'],
            'content' => ['required', 'string'],
            'isRead' => 'boolean',
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
