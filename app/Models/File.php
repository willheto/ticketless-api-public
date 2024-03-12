<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Http\Request;
use App\Managers\UploadManager;

class File extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $primaryKey = 'fileID';

    protected $fillable = [
        'fileName',
        'filePath',
    ];

    public static function getValidationRules(array $fieldsToValidate): array
    {
        $validationRules =  [
            'fileName' => ['required', 'string'],
            'fileBase64' => ['required', 'string'], // This field is not in the database, but it's used to upload the file
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

    public function uploadFile(Request $request): void
    {
        $uploadManager = new UploadManager();
        $uuid = uniqid();
        $fileBase64 = $request->input('fileBase64');
        $fileUrl = $uploadManager->handleUploadFile($fileBase64, $uuid . '.png');
        $request->merge(['filePath' => $fileUrl]);
    }

    public function deleteExistingFile(): void
    {
        $uploadManager = new UploadManager();
        $file = basename($this->filePath);
        $uploadManager->deleteFile($file);
    }
}
