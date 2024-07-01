<?php

namespace App\Http\Controllers;

use App\Services\Zoom\ZoomService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    public function __construct(protected ZoomService $zoomService)
    {
    }

    public function generateOAuthUrl(Request $request)
    {
        return redirect()->to($this->zoomService->generateOAuthUrl());
    }

    /**
     * @throws GuzzleException
     * @throws FileNotFoundException
     */
    public function redirectToURL(Request $request)
    {
        return $this->zoomService->getAccessTokenFromCode($request->get('code'));
    }
}
