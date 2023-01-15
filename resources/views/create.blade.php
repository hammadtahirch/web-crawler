@extends('master')

@section('content')

@if($errors->any())

<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)

        <li>{{ $error }}</li>

        @endforeach
    </ul>
</div>

@endif


        <div class="card col-md-5">
            <div class="card-header">Add Student</div>
            <div class="card-body">
                <form method="post" action="{{ route('report.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-sm-10">
                            <input type="text" name="url" class="form-control" placeholder="Please enter the URL you would like to crawl" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-10">
                            <input type="text" name="pages" class="form-control" placeholder="Please enter the number of pages you would like to crawl." />
                        </div>
                    </div>
                    <div class="text-center">
                        <input type="submit" class="btn btn-primary" value="Add" />
                    </div>
                </form>
            </div>
        </div>

@endsection('content')
