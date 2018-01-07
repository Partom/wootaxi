@extends('admin.layout.base')

@section('title', 'Add Service Type Car Brand ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.service.brand.index',$Service->id) }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

            <h5 style="margin-bottom: 2em;">Add Car Brand</h5>

            <form class="form-horizontal" action="{{route('admin.service.brand.store',$Service->id)}}" method="POST" enctype="multipart/form-data" role="form">
                {{csrf_field()}}
                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">Service Name</label>
                    <div class="col-xs-10">
                        <input class="form-control" readonly=true type="text" value="{{ $Service->name }}" name="name" required id="name" placeholder="Service Name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="service_brand" class="col-xs-12 col-form-label">Car Brand Name</label>
                    <div class="col-xs-10">
                        <select class="form-control" name="service_brand" required>
                            <option value="">Select Car Brand</option>
                            @foreach($CarCategory as $brand)
                            <option value="{{$brand->id}}">{{$brand->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="carbrand" class="col-xs-12 col-form-label"></label>
                    <div class="col-xs-10">
                        <button type="submit" class="btn btn-primary">Add Car Brand</button>
                        <a href="{{route('admin.service.brand.index',$Service->id)}}" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
