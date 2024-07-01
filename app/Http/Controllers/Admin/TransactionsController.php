<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UserType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    public function getTransactions(Request $request,$from = null, $to = null)
    {
        
        $from = $request->from_date;
        $to = $request->to_date;
        $transaction = Transaction::query()->where('in', '>', 0);

        if (isset($from) && $request->from != 'all' && isset($to) && $to != 'all') {
            $transaction = $transaction->whereBetween('created_at', [$from, $to]);
        } else {
            if (isset($from) && $from != 'all') {
                $transaction = $transaction->whereDate('created_at', '<=', $from);
            }   
            if (isset($to) && $to != 'all') {
                $transaction = $transaction->whereDate('created_at', '>=', $to);
            }
        }

        $transaction = $transaction->whereHas('user')->with('user')->latest();
        
        return DataTables::of($transaction)
            ->addIndexColumn()
            ->editColumn('user', function ($transaction) {
                return $transaction->user->name;
            })
            ->filterColumn('user', function ($query, $keyword) {
                
                $query->whereHas('user', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
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
