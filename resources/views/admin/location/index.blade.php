@extends('admin.layout.base')

@section('title', 'One Way Location ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <h5 class="mb-1">One Way Location</h5>
            <a href="{{ route('admin.location.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add One Way Location</a>
            <table class="table table-striped table-bordered dataTable" id="table-2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>From Address</th>
                        <th>From City</th>
                        <th>To Address</th>
                        <th>To City</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($location as $index => $points)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $points->from_address }}</td>
                        <td>{{ $points->from_city }}</td>
                        <td>{{ $points->to_address }}</td>
                        <td>{{ $points->to_city }}</td>
                        <td>
                            <form action="{{ route('admin.location.destroy', $points->id) }}" method="POST">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <a href="{{ route('admin.location.edit',$points->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
                                <button class="btn btn-danger btn-block" onclick="return confirm('Are you sure?')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>From Address</th>
                        <th>From City</th>
                        <th>To Address</th>
                        <th>To City</th>
                        <th>Action</th>  
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection