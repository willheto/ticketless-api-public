<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Exceptions\NotFoundException\NotFoundException;
use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Validation\ValidationException;
use App\Exceptions\CustomValidationException\CustomValidationException;

class FilesController extends BaseController
{
    public function __construct()
    {
        $this->CRUD_RESPONSE_ARRAY = 'files';
        $this->CRUD_RESPONSE_OBJECT = 'file';
    }

    public function getSingleFile(int $fileID): JsonResponse
    {
        try {
            $file = File::where('fileID', $fileID)->first();
            if (!$file) {
                throw new NotFoundException('File not found');
            }

            $response = $this->createResponseData($file, 'object');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function getAllFiles(): JsonResponse
    {
        try {
            $files = File::all();

            $response = $this->createResponseData($files, 'array');
            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function createFile(Request $request): JsonResponse
    {
        try {
            $this->validate($request, File::getValidationRules([]));
            $file = new File();
            $file->uploadFile($request);
            $file->fill($request->except('fileBase64'));
            $file->save();

            $response = $this->createResponseData($file, 'object');
            return response()->json($response, 201);
        } catch (ValidationException $e) {
            if ($e->getMessage()) {
                return $this->handleError(new CustomValidationException($e->getMessage()));
            }
            return $this->handleError(new CustomValidationException());
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function updateFile(Request $request): JsonResponse
    {
        try {
            $fileID = $request->input('fileID');
            $file = File::where('fileID', $fileID)->first();
            
            if (!$file) {
                throw new NotFoundException('File not found');
            }
            
            $this->validate($request, File::getValidationRules([]));
            $file->fill($request->except('fileBase64'));
            $file->save();

            $response = $this->createResponseData($file, 'object');
            return response()->json($response);
        } catch (ValidationException $e) {
            if ($e->getMessage()) {
                return $this->handleError(new CustomValidationException($e->getMessage()));
            }
            return $this->handleError(new CustomValidationException());
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }

    public function deleteFile(Request $request): JsonResponse
    {
        try {
            $file = File::where('fileID', $request->input('fileID'))->first();
            if (!$file) {
                throw new NotFoundException('File not found');
            }

            $file->deleteExistingFile();
            $file->delete();

            return response()->json(['success' => 'File deleted']);
        } catch (Exception $e) {
            return $this->handleError($e);
        }
    }
}
