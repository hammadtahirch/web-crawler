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
            <div class="col col-md-6"><b>Crawled Data</b></div>
            <div class="col col-md-6">
                <a href="{{ route('report.create') }}" class="btn btn-success btn-sm float-end">Add</a>
                <a style="margin-right: 10px;" href="{{ route('avg_report.index') }}" class="btn btn-success btn-sm float-end">AVG Report</a>
                <a style="margin-right: 10px;" href="<?=url('')?>/delete_session_data" class="btn btn-success btn-sm float-end">Clear Out</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Link</th>
                <th>Status code</th>
                <th>Images links</th>
                <th>Internal links</th>
                <th>External links</th>
                <th>Page load time</th>
                <th>Word count</th>
                <th>Title length</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
            @if(count($reportData) > 0)

            @foreach($reportData as $row)

            <tr>
                <td>{{ $row->page_link }}</td>
                <td>{{ $row->status_code }}</td>
                <td>{{ $row->images_links }}</td>
                <td>{{ $row->internal_links }}</td>
                <td>{{ $row->external_links }}</td>
                <td>{{ $row->page_load_time }}</td>
                <td>{{ $row->word_count }}</td>
                <td>{{ $row->title_length }}</td>
                <td>{{ date("F d, Y h:i:s", strtotime($row->created_at)) }}</td>
                <td>{{ date("F d, Y h:i:s", strtotime($row->updated_at)) }}</td>
                <td>
                    <form method="post" action="{{ route('report.destroy', $row->id) }}">
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
        {!! $reportData->links("pagination::bootstrap-5") !!}
    </div>
</div>

@endsection
