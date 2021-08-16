@extends('layouts.app')

@section('content')
    <?php
    $months = array();
    $currentMonth = (int)date('m');
    for ($x = $currentMonth; $x < $currentMonth + 12; $x++) {
        $months[] = date('F', mktime(0, 0, 0, $x, 1));
    }

    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Budget</div>

                    <div class="card-body">

                        @if(session('success'))
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <p>{{ session('success') }}</p>
                                        <a class="close" href="#"></a>
                                    </div>
                                </div>
                            </div>
                        @endif

                            @if(session('error'))
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger">
                                            <p>{{ session('error') }}</p>
                                            <a class="close" href="#"></a>
                                        </div>
                                    </div>
                                </div>
                            @endif


                        <form method="POST" action="{{ url('budget') }}">
                            @csrf

                            <div class="form-group">
                                    <label for="email" class="col-form-label">Month</label>
                                <select class="form-control" name="month" required autofocus>
                                    @foreach($months as $k => $month)
                                    <option value="{{$month}}">{{$month}}</option>
                                    @endforeach
                                </select>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div>
                            <div class="form-group">

                                    <label for="email" class="col-form-label">Budget Name</label>
                                    <input id="email" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required >

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div>

                            <div class="form-group ">

                                    <label for="email" class="col-form-label">Budget Amount</label>

                                    <input id="email" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>

                                    @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                            </div>

                            <div class="form-group">
                                    <label for="password" class="col-form-label">Budget Type</label>
                                    <select class="form-control" name="type" required>
                                        @foreach($categories as $cate)
                                            <option value="{{$cate->name}}">{{$cate->name}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group">
                                <label for="message-text" class="col-form-label">Message:</label>
                                <textarea class="form-control" name="description" id="message-text" required></textarea>
                            </div>
                            <input type="hidden" name="user_id" value="{{auth()->user()->id}}">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                        <hr>
                            <h5>Total - &#8358; {{number_format($budgets->sum('amount'))}}</h5>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Type</th>
                                <th scope="col">Month</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                                {{--                                <th scope="col">Type</th>--}}
                                {{--                                <th scope="col">Handle</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 1; ?>
                            @foreach($budgets as $cate)
                                <tr>
                                    <th scope="row">{{$i++}}</th>
                                    <td>{{$cate->name}}</td>
                                    <td>{{$cate->type}}</td>
                                    <td>{{$cate->month}} {{ \Carbon\Carbon::parse($cate->create_at)->format('Y') }}</td>
                                    <td>&#8358; {{number_format($cate->amount)}}</td>
                                    <td>

                                        @if($cate->status == '1')
                                            <i class="badge badge-success badge-pill">Completed on {{ \Carbon\Carbon::parse($cate->updated_at)->format('d M Y') }}</i>
                                        @else
                                            <i class="badge badge-info badge-pill">Not Done</i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cate->status == '1')
                                            <a href="" disabled="" class="btn btn-warning btn-sm">Done</a>

                                            @else
                                            <a href="{{url('mark/'.$cate->id)}}" class="btn btn-primary btn-sm">Mark Done</a>

                                            <a href="{{ url('delete/budget/'.$cate->id) }}" class="btn btn-danger btn-sm">Remove</a>
                                            @endif

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
