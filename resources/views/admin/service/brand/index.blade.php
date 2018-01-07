@extends('admin.layout.base')

@section('title', 'Service Type Car Brands ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Service Type Car Brands</h5>
                <a href="{{ route('admin.service.brand.create',$Service->id) }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New Car Brand </a>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Car Brand Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($ServiceCarBrand as $index => $serviceCar)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$serviceCar->service_type?$serviceCar->service_type->name:''}}</td>
                            <td>{{$serviceCar->cars?$serviceCar->cars->name:''}}</td>
                            <td>
                                <form action="{{ route('admin.service.brand.destroy', [$Service->id,$serviceCar->id]) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <!--<a href="{{ route('admin.service.brand.edit', [$Service->id,$serviceCar->id]) }}" class="btn btn-info"><i class="fa fa-pencil"></i> Edit</a>-->
                                    <button class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Car Name</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>
@endsection