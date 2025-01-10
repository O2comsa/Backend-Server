<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:transactions.manage');
        $this->middleware('permission:transactions.add', ['only' => ['create']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.transaction.index');
    }

    public function getTransactions(Request $request)
    {
        $from = $request->from_date;
        $to = $request->to_date;
        

        // Base query for transactions with initial conditions
        $transaction = Transaction::query()
            ->where(function ($query) {
                $query->where('in', '>', 0)
                    ->orWhere('is_free', 1);
            })
            ->whereHas('user') // Ensure the transaction has an associated user
            ->with('user')
            ->latest();

        // Apply date range filter
        if (isset($from) && $from != 'all' && isset($to) && $to != 'all') {
            $fromStart = Carbon::parse($from)->startOfDay(); // "2024-09-18 00:00:00"
            $toEnd = Carbon::parse($to)->endOfDay();       // "2024-09-18 23:59:59"
            $transaction = $transaction->whereBetween('created_at', [$fromStart, $toEnd]);
        } else {
            if (isset($from) && $from != 'all') {
                $transaction = $transaction->whereDate('created_at', '>=', $from);
            }
            if (isset($to) && $to != 'all') {
                $transaction = $transaction->whereDate('created_at', '<=', $to);
            }
        }

        // Apply search filter specifically on user's email if a search value is provided
        if (!empty($request->search['value'])) {
            $keyword = $request->search['value'];
            $transaction = $transaction->whereHas('user', function ($query) use ($keyword) {
                $query->where('email', 'like', "{$keyword}%")
                ->orWhere('name', 'like', "{$keyword}%")
                ->orWhere('national_id', 'like', "{$keyword}%")
                ->orWhere('mobile', 'like', "{$keyword}%");
            });
        }

        // Return the DataTable instance without global search on non-existent columns
        return DataTables::of($transaction)
            ->addIndexColumn()
            ->editColumn('user', function ($transaction) {
                return $transaction->user->name;
            })
            ->addColumn('national_id', function ($transaction) {
                return $transaction->user->national_id;
            })
            ->addColumn('updated_at', function ($transaction) {
                return Carbon::parse($transaction->updated_at)->format('Y-m-d H:i');
            })
            ->filterColumn('user', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('email', 'like', "{$keyword}%")
                      ->orWhere('name', 'like', "{$keyword}%")
                      ->orWhere('national_id', 'like', "{$keyword}%")
                      ->orWhere('mobile', 'like', "{$keyword}%");
                });
            })
            
            ->rawColumns(['user']) // Allow raw HTML for user column if needed
            ->make(true);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        //
        return view('Admin.transaction.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $validator = \Validator::make($request->all(), [
            'type' => 'required',
            'user_id' => 'required|exists:users,id',
            'value' => 'required|min:1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withInput($request->all())->withErrors($validator->errors());
        }

        if ($request->type == 'in') {

            Transaction::create([
                'user_id' => $request->user_id,
                'in' => $request->value,
                'out' => 0,
                'order_id' => 0,
                'balance' => User::find($request->user_id)->wallet + $request->value,
                'note' => 'ايداع بواسطة المشرف',
            ]);
        } elseif ($request->type == 'out') {

            Transaction::create([
                'user_id' => $request->user_id,
                'in' => 0,
                'order_id' => 0,
                'out' => $request->value,
                'balance' => $request->value - User::find($request->user_id)->wallet,
                'note' => 'سحب بواسطة المشرف',
            ]);
        }
        return redirect()->route('transactions.index')->withSuccess(trans('app.success'));
    }
}
