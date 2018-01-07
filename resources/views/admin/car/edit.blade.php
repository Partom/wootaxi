@extends('admin.layout.base')

@section('title', 'Update Car ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
    	    <a href="{{ route('admin.car.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 style="margin-bottom: 2em;">Update Car</h5>

            <form class="form-horizontal" action="{{route('admin.car.update', $car->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
            	<input type="hidden" name="_method" value="PATCH">
				<div class="form-group row">
					<label for="name" class="col-xs-2 col-form-label">Car Name</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ $car->name }}" name="name" required id="name" placeholder="Car Name">
					</div>
				</div>

				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Update Car</button>
						<a href="{{route('admin.car.index')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection
