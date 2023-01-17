@extends('master')

@section('content')

@if($message = Session::get('success'))

<div class="alert alert-success">
    {{ $message }}
</div>

@endif

@if($message = Session::get('error'))

    <div class="alert alert-danger">
        {{ $message }}
    </div>

@endif

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col col-md-6">
                <a href="{{ route('report.index') }}" class="btn btn-success btn-sm float-start">Back</a>
            </div>
            <div class="col col-md-6"><b>Avg Report</b></div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Site Link</th>
                <th>Avg page load time</th>
                <th>Avg title length</th>
                <th>Average word count</th>
                <th>Crawled pages</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            @if(count($avgReportData) > 0)

            @foreach($avgReportData as $row)

            <tr>
                <td>{{ $row->site_link }}</td>
                <td>{{ $row->avg_page_load_time }}</td>
                <td>{{ $row->avg_title_length }}</td>
                <td>{{ $row->avg_world_count }}</td>
                <td>{{ $row->crawled_pages }}</td>
                <td>{{ date("F d, Y h:i:s", strtotime($row->created_at)) }}</td>
                <td>{{ date("F d, Y h:i:s", strtotime($row->updated_at)) }}</td>
                <td>
                    <form method="post" action="{{ route('avg_report.destroy', $row->id) }}">
                        @csrf
                        @method('DELETE')
                        <input type="submit" class="btn btn-danger btn-sm" value="Delete" />
                    </form>

                </td>
            </tr>

            @endforeach

            @else
            <tr>
                <td colspan="11" class="text-center">No Data Found</td>
            </tr>
            @endif
        </table>
        {!! $avgReportData->links("pagination::bootstrap-5") !!}

    </div>
</div>

@endsection
