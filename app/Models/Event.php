<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException\UnauthorizedException;
use App\Managers\UploadManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class Event extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * @var string
     */
    protected $primaryKey = 'eventID';

    /**
     * @var string
     */
    protected $foreignKey = 'organizationID';

    protected $fillable = [
        'organizationID',
        'name',
        'location',
        'type',
        'date',
        'image',
        'trendingScore',
        'showEventOnCalendar',
        'ticketMaxPrice',
        'ticketSaleUrl',
        'isPublic',
        'status',
        'activeFrom',
        'activeTo',
        'redirectCustomText',
        'redirectCustomButtonText'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
        'activeFrom' => 'datetime:Y-m-d H:i:s',
        'activeTo' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'isPublic' => 'boolean',
        'showEventOnCalendar' => 'boolean',
    ];

    /**
     * This property will hold the count of tickets dynamically.
     *
     * @var int|null
     */
    public $ticketsCount = null;

    public static function getValidationRules(array $fieldsToValidate): array
    {
        $statusEnumValues = ['active', 'inactive', 'scheduled', 'redirect'];
        $validationRules =  [
            'organizationID' => ['nullable', 'integer', 'exists:organizations,organizationID'],
            'name' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string'],
            'date' => ['required', 'date'],
            'isPublic' => ['boolean'],
            'status' => ['string', 'in:' . implode(',', $statusEnumValues)],
            'activeFrom' => ['required_if:status,scheduled', 'date_format:Y-m-d H:i:s'],
            'activeTo' => ['required_if:status,scheduled', 'date_format:Y-m-d H:i:s', 'after:activeFrom'],
            'ticketSaleUrl' => ['required_if:status,redirect', 'string', 'max:1000'],
            'trendingScore' => ['integer', 'min:0'],
            'showEventOnCalendar' => ['boolean'],
            'ticketMaxPrice' => ['numeric', 'min:0', 'max:100'],
            'redirectCustomText' => ['string', 'max:500'],
            'redirectCustomButtonText' => ['string', 'max:100'],
        ];
        if (
            empty($fieldsToValidate)
        ) {
            return $validationRules;
        }

        $filteredRules = array_intersect_key($validationRules, $fieldsToValidate);

        return $filteredRules;
    }


    protected function uploadNewImage(string $image, Request $request): void
    {
        $uploadManager = new UploadManager();
        $uuid = uniqid();
        $fileUrl = $uploadManager->handleUploadFile($image, $uuid . '.png');
        $request->merge(['image' => $fileUrl]);
    }

    protected function deleteExistingImage(Request $request): void
    {
        $event = Event::where('eventID', $request->input('eventID'))->first();
        if ($event && $event->image) {
            $uploadManager = new UploadManager();
            $currentEventImage = basename($event->image);
            $uploadManager->deleteFile($currentEventImage);
        }
    }

    public function getFillableEventDataFromRequest(Request $request): array
    {
        if (User::isUserSuperadmin($request->userID) || User::isUserAdmin($request->userID)) {
            if (isset($request->image) && $request->image !== null) {
                $this->uploadNewImage($request->image, $request);
            }

            if (isset($request->image) && $request->image === null) {
                $this->deleteExistingImage($request);
            }

            return $request->except('trendingScore');
        }

        if (User::isUserUser($request->userID)) {
            return $request->except(['organizationID', 'trendingScore', 'showEventOnCalendar', 'image', 'activeFrom', 'activeTo', 'ticketSaleUrl', 'status', 'isPublic', 'ticketMaxPrice', 'redirectCustomText', 'redirectCustomButtonText']);
        }

        throw new UnauthorizedException('Unauthorized access');
    }

    public function getTicketsSellingCount(): int
    {
        return $this->hasMany(Ticket::class, 'eventID', 'eventID')->where('isSelling', true)->count();
    }

    public function getTicketsBuyingCount(): int
    {
        return $this->hasMany(Ticket::class, 'eventID', 'eventID')->where('isSelling', false)->count();
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where(function ($query) use ($now) {
            $query->where('status', 'active')
                ->orWhere('status', 'redirect')
                ->orWhere(function ($query) use ($now) {
                    $query->where('status', 'scheduled')
                        ->where('activeFrom', '<=', $now)
                        ->where('activeTo', '>=', $now);
                });
        });
    }

    public static function getActivePublicEvents(): Collection
    {
        return Event::where('isPublic', true)
            ->active()
            ->get();
    }

    public static function isEventEligibleForTicketCreation(int $eventID): bool
    {
        $event = self::where('eventID', $eventID)
            ->active()
            ->where('status', '!=', 'redirect')
            ->first();
        return !is_null($event);
    }
}
