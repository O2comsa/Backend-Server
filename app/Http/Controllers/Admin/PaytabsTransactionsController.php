<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paytabs;
use Yajra\DataTables\DataTables;

class PaytabsTransactionsController extends Controller
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
        return view('Admin.paytabs_transaction.index');
    }

    public function getPaytabsTransactions($from = null, $to = null)
    {
        $transaction = Paytabs::query()->where('paid', 1);

        if (isset($from) && $from != 'all' && isset($to) && $to != 'all') {
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

        return Datatables::of($transaction)
            ->addIndexColumn()
            ->addColumn('action', function ($lesson) {
                return '<a href="' . route('paytabstransactions.show', $lesson->id) . '" class="btn btn-icon edit" title="' . trans('app.show') . '" data-toggle="tooltip" data-placement="top"> <i class="fas fa-edit"></i></a>';
            })
            ->addColumn('user', function ($row) {
                return (isset($row->user)) ? '<a href="' . route('users.edit', $row->user->id) . '">' . $row->user->name . '</a>' : '';
            })
            ->filterColumn('user', function ($query, $keyword) {
                
                $query->whereHas('user', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->diffForHumans();
            })
            ->addColumn('transaction_note', function ($row) {
                return $row->transaction->note ?? '';
            })
            ->addColumn('course', function ($row) {
                return $row->course->title ?? '';
            })
            ->addColumn('paid', function ($row) {
                return $row->paid ? trans('app.paid') : '';
            })
            ->rawColumns(['action', 'user', 'created_at', 'transaction_note', 'course','paid'])
            ->make(true);
    }


    public function show($id)
    {
        //
        $paytabs = Paytabs::find($id);
        return view('Admin.paytabs_transaction.show', compact('paytabs'));
    }

}
