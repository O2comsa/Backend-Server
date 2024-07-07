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
            ->select('transactions.*', 'users.name as user_name', 'users.national_id');

        if ($this->from_date && $this->from_date != 'all') {
            $query->whereDate('transactions.created_at', '>=', $this->from_date);
        }

        if ($this->to_date && $this->to_date != 'all') {
            $query->whereDate('transactions.created_at', '<=', $this->to_date);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('users.name', 'like', '%'.$this->search.'%')
                    ->orWhere('users.national_id', 'like', '%'.$this->search.'%')
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
            'National ID',
            'Amount',
            'Note',
            'Order Date',
        ];
    }

    public function map($transaction): array
    {
        return [
            $this->key++,
            $transaction->user_name,
            $transaction->national_id,
            $transaction->in,
            $transaction->note,
            $transaction->created_at->format('Y-m-d'),
        ];
    }
}
