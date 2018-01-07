@extends('admin.layout.base')

@section('title', 'Send User Push')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.user.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 style="margin-bottom: 2em;">Send User Push</h5>

            <form class="form-horizontal" action="{{route('admin.user.notify')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
            	<div class="form-group row">
                    <label for="ride" class="col-xs-12 col-form-label">Notify At</label>
                    <div class="col-xs-10">
                        <select class="form-control" id="notify" name="notify">
                            <option value="USER">USER</option>
                            <option value="PROVIDER">PROVIDER</option>
                        </select>
                    </div>
                </div>
				<div class="form-group row">
					<label for="first_name" class="col-xs-12 col-form-label">Notify Content</label>
					<div class="col-xs-10">
						<textarea class="form-control" name="notifycontent" placeholder="Notify Content" rows="4" cols="50">
						</textarea>
						
					</div>
				</div>
				<div class="form-group row">
					<label for="zipcode" class="col-xs-12 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Sent Notify</button>
						<a href="{{route('admin.user.index')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection
