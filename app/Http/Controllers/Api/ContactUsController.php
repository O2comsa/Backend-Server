<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Models\Admin;
use App\Models\ContactUs;
use App\Notifications\ContactUsAdminNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ContactUsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if ($request->has('user_id')) {
            $request->request->add([
                'name' => auth('api')->user()->name,
                'email' => auth('api')->user()->email,
            ]);
        }

        ApiHelper::validate($request, [
            'message' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'files.*' => 'nullable|file|mimes:doc,pdf,docx,jpg,jpeg,png,bmp,gif,svg,webp',
        ]);

        $data = $request->all();

        $filesPath = [];

        foreach ($request->file('files', []) as $file) {
            $destinationPath = 'upload/files/contactus'; // upload path

            $fileName = Storage::disk('upload_driver')->put($destinationPath, $file);
            $filesPath[] = $fileName;
        }

        $data['files'] = $filesPath;

        ContactUs::create($data);

        try {
            // notify by user
            $admin = new Admin();
            $admin->email = env('MAIL_USERNAME');//env('MAIL_FROM_ADDRESS');/
            $admin->notify(new ContactUsAdminNotification($request->name, $request->email, $request->message, $request->mobile));
            //end notify
        } catch (\Exception $exception) {
        }

        return ApiHelper::output(trans('app.contact_us_success_api'));
    }

}
