<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Http\Request;
use App\Managers\UploadManager;


class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'userID';

    /**
     * @var string
     */
    protected $foreignKey = 'organizationID';

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'phoneNumber',
        'city',
        'userType',
        'password',
        'passwordCode',
        'profilePicture',
        'language',
        'organizationID'
    ];

    protected $hidden = ['password', 'passwordCode'];

    public static function getValidationRules(array $fieldsToValidate): array
    {
        $validationRules =  [
            'firstName' => ['string', 'required', 'max:100'],
            'lastName' => ['string', 'required', 'max:100'],
            'email' => ['email', 'required', 'unique:users', 'max:100'],
            'phoneNumber' => ['string', 'nullable', 'max:20'],
            'city' => ['string', 'nullable', 'max:100'],
            'userType' => ['string', 'in:user,admin,superadmin'],
            'organizationID' => ['integer', 'nullable', 'exists:organizations,organizationID'],
            'password' => ['string', 'required', 'min:8', 'max:100'],
            'passwordCode' => ['integer', 'nullable'],
            'profilePicture' => ['string', 'nullable'],
            'language' => ['string', 'in:en,fi'],
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

    public function getFillableUserDataFromRequest(Request $request): array
    {

        if (isset($request->password)) {
            $request['password'] = $this->hashPassword($request['password']);
        }

        if (isset($request->profilePicture) && $request->profilePicture !== null) {
            $this->uploadNewProfilePicture($request->profilePicture, $request);
        }

        if (isset($request->profilePicture) && $request->profilePicture === null) {
            $this->deleteExistingProfilePicture($request);
        }

        return $request->except('userID', 'userType', 'organizationID');
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function uploadNewProfilePicture(string $profilePicture, Request $request): void
    {
        $uploadManager = new UploadManager();
        $uuid = uniqid();
        $fileUrl = $uploadManager->handleUploadFile($profilePicture, $uuid . '.png');
        $request->merge(['profilePicture' => $fileUrl]);
    }

    public function deleteExistingProfilePicture(Request $request): void
    {
        $user = User::where('userID', $request->input('userID'))->first();
        if ($user && $user->profilePicture) {
            $uploadManager = new UploadManager();
            $currentEventProfilePicture = basename($user->profilePicture);
            $uploadManager->deleteFile($currentEventProfilePicture);
        }
    }

    public static function isUserSuperadmin(int $userID): bool
    {
        $user = User::where('userID', $userID)->first();

        if (!$user) {
            return false;
        }

        if ($user->userType === 'superadmin') {
            return true;
        }

        return false;
    }

    public static function isUserAdmin(int $userID): bool
    {

        $user = User::where('userID', $userID)->first();

        if (!$user) {
            return false;
        }

        if ($user->userType === 'admin') {
            return true;
        }

        return false;
    }

    public static function isUserUser(int $userID): bool
    {
        $user = User::where('userID', $userID)->first();
        if (!$user) {
            return false;
        }

        if ($user->userType === 'user') {
            return true;
        }

        return false;
    }
}
