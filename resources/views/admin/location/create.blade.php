@extends('admin.layout.base')

@section('title', 'Add One Way Location ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.location.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

            <h5 style="margin-bottom: 2em;">Add One Way Location</h5>

            <form class="form-horizontal" action="{{route('admin.location.store')}}" method="POST" role="form">
                {{ csrf_field() }}
                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">From Location </label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" onFocus="geolocate()" id="autocomplete" value="{{ old('from_address') }}" name="from_address" required placeholder="One Way From Location">
						            <input class="form-control" type="text"  readonly  value="{{ old('from_city') }}" name="from_city" required id="locality">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">To Location </label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" onFocus="geolocate()" id="autocomplete2" value="{{ old('address') }}" name="to_address" required placeholder="One Way To Location">
                        <input class="form-control" type="text"  readonly  value="{{ old('to_city') }}" name="to_city" required id="locality2">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <a href="{{ route('admin.location.index') }}" class="btn btn-danger btn-block">Cancel</a>
                            </div>
                            <div class="col-xs-12 col-sm-6 offset-md-6 col-md-3">
                                <button type="submit" class="btn btn-primary btn-block">Add One Way City</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
  // This example displays an address form, using the autocomplete feature
// of the Google Places API to help users fill in the information.

var placeSearch, autocomplete, autocomplete2;
var componentForm = {
  locality: 'long_name'
};

function initAutocomplete() {
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  autocomplete = new google.maps.places.Autocomplete(
    /** @type {!HTMLInputElement} */
    (document.getElementById('autocomplete')), {
      types: ['geocode']
    });

  // When the user selects an address from the dropdown, populate the address
  // fields in the form.
  autocomplete.addListener('place_changed', function() {
    fillInAddress(autocomplete, "");
  });

  autocomplete2 = new google.maps.places.Autocomplete(
    /** @type {!HTMLInputElement} */
    (document.getElementById('autocomplete2')), {
      types: ['geocode']
    });
  autocomplete2.addListener('place_changed', function() {
    fillInAddress(autocomplete2, "2");
  });

}

function fillInAddress(autocomplete, unique) {
  // Get the place details from the autocomplete object.
  var place = autocomplete.getPlace();

  for (var component in componentForm) {
    if (!!document.getElementById(component + unique)) {
      document.getElementById(component + unique).value = '';
      document.getElementById(component + unique).disabled = false;
    }
  }

  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType] && document.getElementById(addressType + unique)) {
      var val = place.address_components[i][componentForm[addressType]];
      document.getElementById(addressType + unique).value = val;
    }
  }
}
//google.maps.event.addDomListener(window, "load", initAutocomplete);

function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      autocomplete.setBounds(circle.getBounds());
    });
  }
}
</script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}&libraries=places&callback=initAutocomplete"
        async defer></script>

@endsection

