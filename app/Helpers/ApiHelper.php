<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiHelper
{
    public static function validate($request, $validation_data, $message = null)
    {
        if(isset($message)){
            $validator = Validator::make($request->all(), $validation_data, $message);
        }else{
            $validator = Validator::make($request->all(), $validation_data);
        }

        if ($validator->fails()) {
            $data['errors'] = $validator->errors()->all();
            response()->json($data, 400)->send();
            exit;
        }
    }

    public static function output($data, $success = 1)
    {
        if ($success == 1) {
            return response()->json([
                'data' => (empty($data)) ? [] : $data,
            ], 200);
        } else {
            return response()->json([
                'errors' => (empty($data)) ? [] : [$data],
            ], 400);
        }
    }

    public static function saveBase64Image($path, $photo, $ext)
    {
        if ($photo) {
            $photo_name = md5(uniqid()) . '.' . $ext;
            if (!File::exists(public_path('/upload/image/') . $path)) {
                File::makeDirectory(public_path('/upload/image/') . $path, $mode = 0777, true, true);
            }
            Storage::disk('images')->put($path . $photo_name, base64_decode($photo));
            return $photo_name;
        }
    }

    public static function paginate($items, $perPage = 20)
    {

        $page = request()->get('page');
        $options = [
            'path' => request()->url(),
            'query' => request()->query(),
        ];

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        $paginated = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

        return response()->json(
            ['data' => $paginated],
            200
        );
    }
}
