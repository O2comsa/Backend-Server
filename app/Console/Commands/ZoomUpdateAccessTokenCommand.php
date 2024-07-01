<?php

namespace App\Console\Commands;

use App\Models\ZoomAccountsAccess;
use App\Services\Zoom\ZoomService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ZoomUpdateAccessTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:zoom-update-access-token-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ZoomService $zoomService)
    {
        $accessTokens = ZoomAccountsAccess::query()
            ->latest()
            ->first();
        if ($accessTokens) {
            $zoomService->refreshToken($accessTokens->refresh_token);
        }
    }
}
