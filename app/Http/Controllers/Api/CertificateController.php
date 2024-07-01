<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $certificates = Certificate::query()
            ->where('user_id', $request->get('user_id'))
            ->with('related')
            ->paginate();

            // Modify keys to standardize to 'title' within 'related' field
        $certificates->getCollection()->transform(function ($certificate) {
            if (isset($certificate->related)) {
                if($certificate->related->name) {
                    $certificate->related->title = $certificate->related->name;
                    unset($certificate->related->name);
                }
            }
            return $certificate;
        });
        
        return ApiHelper::output($certificates);
        
    }
}
