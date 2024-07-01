<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $search;
    protected $key = 1;

    public function __construct($from_date, $to_date, $search)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->search = $search;
    }

    public function query()
    {
        $query = Transaction::query()->latest()->where('in', '>', 0)
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select('transactions.*', 'users.name as user_name');

        if ($this->from_date && $this->from_date != 'all') {
            $query->whereDate('transactions.created_at', '>=', $this->from_date);
        }

        if ($this->to_date && $this->to_date != 'all') {
            $query->whereDate('transactions.created_at', '<=', $this->to_date);
        }

        // Add the search condition
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('users.name', 'like', '%'.$this->search.'%')
                    ->orWhere('transactions.in', 'like', '%'.$this->search.'%')
                    ->orWhere('transactions.note', 'like', '%'.$this->search.'%');
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Amount',
            'Note',
            'Order Date',
        ];
    }

    public function map($transaction): array
    {
        $key = 1;
        return [
            $this->key++,
            $transaction->user_name,
            $transaction->in,
            $transaction->note,
            $transaction->created_at->format('Y-m-d'),
        ];
    }
}
