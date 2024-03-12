<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Exceptions\BadRequestException;

class Chat extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'chatID';

    /**
     * @var array
     */
    protected $foreignKey = ['user1ID', 'user2ID'];

    protected $casts = [
        'isActive' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $fillable = [
        'user1ID',
        'user2ID',
        'ticketID',
        'isActive',
    ];

    public static array $relatedResources = [
        'user1',
        'user2',
        'ticket',
        'messages',
    ];


    public static function getValidationRules(array $fieldsToValidate): array
    {
        $validationRules =  [
            'user1ID' => ['required', 'integer', 'exists:users,userID'],
            'user2ID' => ['required', 'integer', 'exists:users,userID'],
            'ticketID' => ['required', 'integer', 'exists:tickets,ticketID'],
            'isActive' => 'boolean',
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

    public function verifyNotStartingChatWithSelf(): void
    {
        if ($this->user1ID === $this->user2ID) {
            throw new BadRequestException('Cannot start chat with yourself');
        }
    }

    public function verifyRelatedTicketExists(): void
    {
        $ticket = Ticket::where('ticketID', $this->ticketID)->first();
        if (!$ticket) {
            throw new BadRequestException('No related ticket found');
        }
    }

    public function verifyUser2IsRelatedToTicket(): void
    {
        $ticket = Ticket::where('ticketID', $this->ticketID)->first();
        
        if (!$ticket) {
            throw new BadRequestException('No related ticket found');
        }

        if ($ticket->userID !== $this->user2ID) {
            throw new BadRequestException('Cannot start chat with a user who does not own the ticket');
        }
    }

    public function verifyChatDoesNotAlreadyExist(): void
    {
        $chat = Chat::where('user1ID', $this->user1ID)
            ->where('user2ID', $this->user2ID)
            ->where('ticketID', $this->ticketID)
            ->first();

        if ($chat) {
            throw new BadRequestException('Chat already exists');
        }
    }

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1ID', 'userID')->select('userID', 'firstName', 'lastName', 'profilePicture', 'created_at');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2ID', 'userID')->select('userID', 'firstName', 'lastName', 'profilePicture', 'created_at');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticketID', 'ticketID');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chatID', 'chatID');
    }
}
