<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Budget;
use App\Models\Categories;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function summary()
    {
        $data['income'] = Transaction::filterCategory(config('app.categories.income.id'))->get()->sum('amount');
        $data['savings'] = Transaction::filterCategory(config('app.categories.savings.id'))->get()->sum('amount');
        $data['expenses'] = Transaction::filterCategory(config('app.categories.expenses.id'))->get()->sum('amount');
        $data['transactions'] = Transaction::myLatest(5)->get();
        $data['categories'] = Categories::pluck('name', 'id')->toArray();
        $data['budgets'] = Budget::where('user_id', auth()->user()->id)->get();
        $data['types'] = Type::pluck('name', 'id')->toArray();
        return view('transactions.summary', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Support\Facades\View
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'to' => 'nullable|date',
            'from' => 'nullable|date',
        ]);
        $data['transactions'] = Transaction::betweenDates($data)->orderBy('created_at', 'DESC')->paginate(100);
        $data['income'] = Transaction::filterCategory(config('app.categories.income.id'))->betweenDates($data)->orderBy('created_at', 'DESC')->get()->sum('amount');
        $data['savings'] = Transaction::filterCategory(config('app.categories.savings.id'))->betweenDates($data)->orderBy('created_at', 'DESC')->get()->sum('amount');
        $data['expenses'] = Transaction::filterCategory(config('app.categories.expenses.id'))->betweenDates($data)->orderBy('created_at', 'DESC')->get()->sum('amount');
        //return $data;
        return view('transactions.transaction_list', $data);
        //return view('transactions', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
            'name' => 'required|string',
            'date' => 'required',
            'category_id' => 'required|exists:categories,id',
            'type_id' => 'required|exists:types,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'description' => 'nullable|string'
        ]);
        $data['date'] = \Carbon\Carbon::parse($request->date)->toDateTimeString();
        Transaction::create($data + ['user_id' => Auth::id()]);
        return back()->with('success', 'Transaction was registered successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function filter(Request $request)
    {
        if(isset($request->from) && isset($request->to)) {
            $to = $request->to;
            $from = $request->from;
            $transactions = Transaction::where('user_id', auth()->user()->id)->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'DESC')->get();
        }else{}

        return view('transactions', ['transactions' => $transactions, 'from' => $from, 'to' => $to]);

    }
}
