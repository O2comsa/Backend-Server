<?php

namespace App\Http\Controllers\Admin;

use App\Charts\LastRegistrationAdminChart;
use App\Helpers\UserStatus;
use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * DashboardController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $usersPerMonth = $this->countOfNewUsersPerMonth(
            Carbon::now()->subYear()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );

        $transactionPerMonth = $this->countOfNewTransactionPerMonth(
            Carbon::now()->subYear()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );

        $TransactionStats = [
            'total' => Transaction::all()->count(),
            'new' => Transaction::whereBetween('created_at', [Carbon::now()->firstOfMonth(), Carbon::now()])->count(),
        ];

        $usersStats = [
            'total' => User::all()->count(),
            'new' => User::whereBetween('created_at', [Carbon::now()->firstOfMonth(), Carbon::now()])->count(),
            'active' => User::where('status', UserStatus::ACTIVE)->count(),
            'banned' => User::where('status', UserStatus::BANNED)->count(),
        ];


        $usersChart = new LastRegistrationAdminChart();
        $usersChart->labels(array_keys($usersPerMonth));
        $usersChart->dataset(trans('app.users_registration_history'), 'line', array_values($usersPerMonth));

        $transactionChart = new LastRegistrationAdminChart();
        $transactionChart->labels(array_keys($transactionPerMonth));
        $transactionChart->dataset(trans('app.transaction_history'), 'line', array_values($transactionPerMonth));


        return view('Admin.dashboard', compact( 'usersStats', 'TransactionStats', 'usersChart', 'transactionChart'));
    }

    public function countOfNewAdminsPerMonth(Carbon $from, Carbon $to)
    {
        $result = Admin::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get(['created_at'])
            ->groupBy(function ($user) {
                return $user->created_at->format("Y_n");
            });

        $counts = [];

        while ($from->lt($to)) {
            $key = $from->format("Y_n");

            $counts[$this->parseDate($key)] = count($result->get($key, []));

            $from->addMonth();
        }

        return $counts;
    }

    public function countOfNewTransactionPerMonth(Carbon $from, Carbon $to)
    {
        $result = Transaction::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get(['created_at'])
            ->groupBy(function ($user) {
                return $user->created_at->format("Y_n");
            });

        $counts = [];

        while ($from->lt($to)) {
            $key = $from->format("Y_n");

            $counts[$this->parseDate($key)] = count($result->get($key, []));

            $from->addMonth();
        }

        return $counts;
    }

    public function sumOfNewTransactionPerMonth(Carbon $from, Carbon $to)
    {
        $result = Transaction::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get(['created_at', 'in'])
            ->groupBy(function ($user) {
                return $user->created_at->format("Y_n");
            });


        $counts = [];

        while ($from->lt($to)) {
            $key = $from->format("Y_n");

            $counts[$this->parseDate($key)] = collect($result->get($key, []))->sum('in');

            $from->addMonth();
        }

        return $counts;
    }

    public function countOfNewUsersPerMonth(Carbon $from, Carbon $to)
    {
        $result = User::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get(['created_at'])
            ->groupBy(function ($user) {
                return $user->created_at->format("Y_n");
            });

        $counts = [];

        while ($from->lt($to)) {
            $key = $from->format("Y_n");

            $counts[$this->parseDate($key)] = count($result->get($key, []));

            $from->addMonth();
        }

        return $counts;
    }

    private function parseDate($yearMonth)
    {
        list($year, $month) = explode("_", $yearMonth);

        $month = trans("app.months.{$month}");

        return "{$month} {$year}";
    }
}
