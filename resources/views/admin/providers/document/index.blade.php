@extends('admin.layout.base')

@section('title', 'Provider Documents ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <h5 class="mb-1">Provider Service Type Woocation</h5>
            <div class="row">
                <div class="col-xs-12">
                    @if($ProviderService->count() > 0)
                    <hr><h6>Woocated Services :  </h6>
                    <table class="table table-striped table-bordered dataTable">
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Service Number</th>
                                <th>Service Model</th>
                                <th>Service Color</th>
                                <th>Property Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ProviderService as $service)
                            <tr>
                                <td>{{ $service->service_type->name }}</td>
                                <td>{{ $service->service_number }}</td>
                                <td>{{ $service->cars?$service->cars->name:'' }}</td>
                                <td>{{ $service->service_color }}</td>
                                <td>{{ $service->property }}</td>
                                <td>
                                    <form action="{{ route('admin.provider.document.service', [$Provider->id, $service->id]) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button class="btn btn-danger btn-large btn-block">Delete</a>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Service Name</th>
                                <th>Service Number</th>
                                <th>Service Model</th>
                                <th>Service Color</th>
                                <th>Property Type</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                    @endif
                    <hr>
                </div>
                <form action="{{ route('admin.provider.document.store', $Provider->id) }}" method="POST">
                    {{ csrf_field() }}
                    <div class="col-xs-2">
                        <select class="form-control input" name="service_type" required>
                            @forelse($ServiceTypes as $Type)
                            <option value="{{ $Type->id }}">{{ $Type->name }}</option>
                            @empty
                            <option>- Please Create a Service Type -</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-xs-2">
                        <input type="text" required name="service_number" class="form-control" placeholder="Number (CY 98769)">
                    </div>
                    <div class="col-xs-2">
                        <select class="form-control input" name="service_model" required>
                            @forelse($CarCategorys as $Car)
                            <option value="{{ $Car->id }}">{{ $Car->name }}</option>
                            @empty
                            <option>- Please Create a Car Type -</option>
                            @endforelse
                        </select>
                        <!--<input type="text" required name="service_model" class="form-control" placeholder="Model (Audi R8)">-->
                    </div>
                    <div class="col-xs-2">
                        <input id="service_color" type="text" class="form-control" name="service_color" placeholder="Car Color[Black]">
                    </div>
                    <div class="col-xs-2">
                        <select class="form-control" name="property" id="property">
                            <option value="">Accessories Type</option>
                            <option value="own">Own Board</option>
                            <option value="rental">Rental Board</option>
                        </select>
                    </div>
                    <div class="col-xs-2">
                        <button class="btn btn-primary btn-block" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-block bg-white">
            <h5 class="mb-1">Provider Documents</h5>
            <table class="table table-striped table-bordered dataTable" id="table-2">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Document Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Provider->documents as $Index => $Document)
                    <tr>
                        <td>{{ $Index + 1 }}</td>
                        <td>{{ $Document->document->name }}</td>
                        <td>{{ $Document->status }}</td>
                        <td>
                            <div class="input-group-btn">
                                <a href="{{ route('admin.provider.document.edit', [$Provider->id, $Document->id]) }}"><span class="btn btn-success btn-large">View</span></a>
                                <button class="btn btn-danger btn-large" form="form-delete">Delete</button>
                                <form action="{{ route('admin.provider.document.destroy', [$Provider->id, $Document->id]) }}" method="POST" id="form-delete">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Document Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection