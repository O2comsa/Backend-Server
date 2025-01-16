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
        // Start with the query for Paytabs transactions
        $transaction = Paytabs::query()->where('paid', 1);

        // Ensure the related model exists (either Course or LiveEvent)
        $transaction = $transaction->whereHas('related');
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
                if ($row->related instanceof \App\Models\Course) {
                    return $row->related->title ?? '';
                } elseif ($row->related instanceof \App\Models\LiveEvent) {
                    return $row->related->name ?? ''; // Assuming LiveEvent has a `name` attribute
                }elseif ($row->related instanceof \App\Models\Plan) {
                    return $row->related->name ?? ''; // Assuming LiveEvent has a `name` attribute
                }
                return '';
            })->filterColumn('course', function ($query, $keyword) {
                $query->whereHas('related', function ($relatedQuery) use ($keyword) {
                    $relatedQuery->where(function ($subQuery) use ($relatedQuery, $keyword) {
                        $subQuery->when($relatedQuery->getModel() instanceof \App\Models\Course, function ($query) use ($keyword) {
                            $query->where('title', 'like', "%{$keyword}%");
                        })
                            ->when($relatedQuery->getModel() instanceof \App\Models\LiveEvent, function ($query) use ($keyword) {
                                $query->where('name', 'like', "%{$keyword}%");
                            });
                    });
                });
            })


            ->addColumn('paid', function ($row) {
                return $row->paid ? trans('app.paid') : '';
            })
            ->rawColumns(['action', 'user', 'created_at', 'transaction_note', 'course', 'paid'])
            ->addColumn('updated_at', function ($row) {
                return $row->updated_at->diffForHumans();
            })
            ->make(true);
    }


    public function show($id)
    {
        //
        $paytabs = Paytabs::find($id);
        return view('Admin.paytabs_transaction.show', compact('paytabs'));
    }
}
