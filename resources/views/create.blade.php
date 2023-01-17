@extends('master')
@section('content')
    <div class="card col-md-4" style="margin: 0 auto;">
            <div class="card-header">Add site information below</div>
            <div class="card-body">
                @if($message = Session::get('error'))
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger pb-0">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="container">
                    <form  method="post" action="{{ route('report.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3 @if(session('email')) d-none @endif">
                            <div class="col-sm-12">
                                <input type="text" value="@if(!session('email')){{'example@example.com'}} @else {{session('email')}} @endif" name="email" class="form-control" placeholder="Please enter your email."/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <input type="text" value="https://agencyanalytics.com" name="url" class="form-control" placeholder="Please enter the URL you would like to crawl" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <input type="text" value="6" name="pages" class="form-control" placeholder="Please enter the number of pages you would like to crawl." />
                            </div>
                        </div>
                        <div class="text-center">
                            <input type="submit" class="btn btn-primary" value="Let's rock and roll!" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection('content')
