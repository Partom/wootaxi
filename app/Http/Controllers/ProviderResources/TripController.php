<?php

namespace App\Http\Controllers\ProviderResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Log;
use Setting;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Http\Controllers\SendPushNotification;
use Mail;
use App\Mail\RequestUserInvoiceReminder;
use App\Mail\RequestUserBookingReminder;

use App\User;
use App\Provider;
use App\Admin;
use App\Chat;
use App\Promocode;
use App\UserRequests;
use App\RequestFilter;
use App\PromocodeUsage;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;
use Location\Coordinate;
use Location\Distance\Vincenty;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            if($request->ajax()) {
                $Provider = Auth::user();
            } else {
                $Provider = Auth::guard('provider')->user();
            }

            $provider = $Provider->id;

            $AfterAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider) {
                        $query->where('status','<>', 'CANCELLED');
                        $query->where('status','<>', 'SCHEDULED');
                        $query->where('provider_id', $provider );
                        $query->where('current_provider_id', $provider);
                    });

            $BroadCastAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider){
                        $query->where('status','<>', 'CANCELLED');
                        $query->where('status','<>', 'SCHEDULED');
                        $query->where('broad_cast', 'YES');
                        $query->whereNull('current_provider_id');
                    });

            $BeforeAssignProvider = RequestFilter::with(['request.user', 'request.payment', 'request'])
                ->where('provider_id', $provider)
                ->whereHas('request', function($query) use ($provider){
                        $query->where('status','<>', 'CANCELLED');
                        $query->where('status','<>', 'SCHEDULED');
                        $query->where('broad_cast', 'NO');
                        $query->where('current_provider_id',$provider);
                    });
            

            $IncomingRequests =$BeforeAssignProvider->union($BroadCastAssignProvider)->union($AfterAssignProvider)->orderBy('created_at','desc')->get();

            if(!empty($request->latitude)) {
                $Provider->update([
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                ]);
            }

            $Timeout = Setting::get('provider_select_timeout', 180);
                if(!empty($IncomingRequests)){
                    for ($i=0; $i < sizeof($IncomingRequests); $i++) {
                        $IncomingRequests[$i]->time_left_to_respond = $Timeout - (time() - strtotime($IncomingRequests[$i]->request->assigned_at));
                        if($IncomingRequests[$i]->request->status == 'SEARCHING' && $IncomingRequests[$i]->time_left_to_respond < 0 && $IncomingRequests[$i]->request->broad_cast == "NO" ) {
                            $this->assign_next_provider($IncomingRequests[$i]->request->id);
                        }else if($IncomingRequests[$i]->request->status == 'SEARCHING' && $IncomingRequests[$i]->time_left_to_respond < 0 && $IncomingRequests[$i]->request->broad_cast == "YES" ) {

                            $User = User::find($IncomingRequests[$i]->request->user_id);

                            if($User){
                                $TextMsg = "Dear ".$User->first_name." ".$User->last_name.", Thanks for Booking, We are Ready to Provide the Taxi Service. Your Booking ID : ".$IncomingRequests[$i]->request->booking_id." We will reach out to you with cab details in next 30-60 min.";
                                
                                $MessageUrl =  "https://control.msg91.com/api/sendhttp.php?authkey=".env('MSG91KEY')."&mobiles=".$User->mobile."&message=".$TextMsg."&sender=".env('MSGSENDERID')."&route=4&country=91";

                                $json = curl($MessageUrl);

                                $MessageUrl = json_decode($json, TRUE);
                                
                                Mail::to($User)->send(new RequestUserBookingReminder($IncomingRequests[$i]->request->id));
                            }  

                            //UserRequests::where('id', $IncomingRequests[$i]->request->id)->update(['status' => 'CANCELLED']);
                            // No longer need request specific rows from RequestMeta
                            //RequestFilter::where('request_id', $IncomingRequests[$i]->request->id)->delete();
                            // request push to user provider not available
                            //(new SendPushNotification)->ProviderNotAvailable($IncomingRequests[$i]->request->user_id);
                        }
                    }
                }


            $Response = [
                    'account_status' => $Provider->status,
                    'service_status' => $Provider->service ? Auth::user()->service->status : 'offline',
                    'requests' => $IncomingRequests,
                ];

            return $Response;
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
    }

    /**
     * Calculate distance between two coordinates.
     * 
     * @return \Illuminate\Http\Response
     */

    public function calculate_distance(Request $request, $id){
        $this->validate($request, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]);
        try{

            if($request->ajax()) {
                $Provider = Auth::user();
            } else {
                $Provider = Auth::guard('provider')->user();
            }

            $UserRequest = UserRequests::where('status','PICKEDUP')
                            ->where('provider_id',$Provider->id)
                            ->find($id);

            if($UserRequest && ($request->latitude && $request->longitude)){

                //Log::info("REQUEST ID:".$UserRequest->id."==SOURCE LATITUDE:".$UserRequest->travel_latitude."==SOURCE LONGITUDE:".$UserRequest->travel_longitude);
            
                if($UserRequest->travel_latitude !=0.00000000 && $UserRequest->travel_longitude !=0.00000000){
                    
                    $coordinate1 = new Coordinate($UserRequest->travel_latitude, $UserRequest->travel_longitude); /** Set Distance Calculation Source Coordinates ****/
                    $coordinate2 = new Coordinate($request->latitude, $request->longitude); /** Set Distance calculation Destination Coordinates ****/

                    $calculator = new Vincenty();

                    /***Distance between two coordinates using spherical algorithm (library as mjaschen/phpgeo) ***/ 

                    $mydistance = $calculator->getDistance($coordinate1, $coordinate2); 

                    $meters = round($mydistance);

                    //Log::info("REQUEST ID:".$UserRequest->id."==BETWEEN TWO COORDINATES DISTANCE:".$meters." (m)");

                    if($meters >= 100){
                        /*** If traveled distance riched houndred meters means to be the source coordinates ***/
                        $traveldistance = round(($meters/1609),8);

                        $calulatedistance = $UserRequest->travel_distance + $traveldistance;

                        $UserRequest->travel_distance = $calulatedistance;
                        $UserRequest->distance        = $calulatedistance;
                        $UserRequest->travel_latitude = $request->latitude;
                        $UserRequest->travel_longitude= $request->longitude;
                        //$UserRequest->d_latitude      = $request->latitude;
                        //$UserRequest->d_longitude     = $request->longitude;
                        $UserRequest->save();
                    }
                }else if(($UserRequest->travel_latitude ==0.00000000 && $UserRequest->travel_longitude ==0.00000000) && ($request->latitude && $request->longitude)) {
                    $UserRequest->distance             = 0;
                    $UserRequest->travel_latitude      = $request->latitude;
                    $UserRequest->travel_longitude     = $request->longitude;
                    $UserRequest->save();
                }
            }
            return $UserRequest;
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }

    }

    /**
     * Cancel given request.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        $this->validate($request, [
            'cancel_reason'=> 'max:255',
        ]);
        try{

            $UserRequest = UserRequests::findOrFail($request->id);
            $Cancellable = ['SEARCHING', 'ACCEPTED', 'ARRIVED', 'STARTED', 'CREATED','SCHEDULED'];

            if(!in_array($UserRequest->status, $Cancellable)) {
                return back()->with(['flash_error' => 'Cannot cancel request at this stage!']);
            }

            $UserRequest->status = "CANCELLED";
            $UserRequest->cancel_reason = $request->cancel_reason;
            $UserRequest->cancelled_by = "PROVIDER";
            $UserRequest->save();

             RequestFilter::where('request_id', $UserRequest->id)->delete();

             ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);

             // Send Push Notification to User
            (new SendPushNotification)->ProviderCancellRide($UserRequest);

            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Something went wrong']);
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rate(Request $request, $id)
    {

        $this->validate($request, [
                'rating' => 'required|integer|in:1,2,3,4,5',
                'comment' => 'max:255',
            ]);
    
        try {

            $UserRequest = UserRequests::where('id', $id)
                ->where('status', 'COMPLETED')
                ->firstOrFail();

            if($UserRequest->rating == null) {
                UserRequestRating::create([
                        'provider_id' => $UserRequest->provider_id,
                        'user_id' => $UserRequest->user_id,
                        'request_id' => $UserRequest->id,
                        'provider_rating' => $request->rating,
                        'provider_comment' => $request->comment,
                    ]);
            } else {
                $UserRequest->rating->update([
                        'provider_rating' => $request->rating,
                        'provider_comment' => $request->comment,
                    ]);
            }

            $UserRequest->update(['provider_rated' => 1]);

            // Delete from filter so that it doesn't show up in status checks.
            RequestFilter::where('request_id', $id)->delete();

            ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);

            // Send Push Notification to Provider 
            $average = UserRequestRating::where('provider_id', $UserRequest->provider_id)->avg('provider_rating');

            $UserRequest->user->update(['rating' => $average]);

            return response()->json(['message' => 'Request Completed!']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Request not yet completed!'], 500);
        }
    }

    /**
     * Get the trip history of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function scheduled(Request $request)
    {
        
        try{

            $Jobs = UserRequests::where('provider_id', Auth::user()->id)
                    ->where('status', 'SCHEDULED')
                    ->with('service_type')
                    ->get();

            if(!empty($Jobs)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x000000|weight:3|enc:".$value->route_key.
                            "&key=".env('GOOGLE_MAP_KEY');
                }
            }

            return $Jobs;
            
        } catch(Exception $e) {
            return response()->json(['error' => "Something Went Wrong"]);
        }
    }

    /**
     * Get the trip history of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
        if($request->ajax()) {

            $Jobs = UserRequests::where('provider_id', Auth::user()->id)
                    ->where('status', 'COMPLETED')
                    ->orderBy('created_at','desc')
                    ->with('payment')
                    ->get();

            if(!empty($Jobs)){
                $map_icon = asset('asset/marker.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x000000|weight:3|enc:".$value->route_key.
                            "&key=".env('GOOGLE_MAP_KEY');
                }
            }
            return $Jobs;
        }
        $Jobs = UserRequests::where('provider_id', Auth::guard('provider')->user()->id)->with('user', 'service_type', 'payment', 'rating')->get();
        return view('provider.trip.index', compact('Jobs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $id)
    {
        try {

            $UserRequest = UserRequests::findOrFail($id);

            if($UserRequest->status != "SEARCHING") {
                return response()->json(['error' => 'Request already under progress!']);
            }
            
            $UserRequest->provider_id = Auth::user()->id;
            $UserRequest->current_provider_id = Auth::user()->id;

            if($UserRequest->schedule_at != ""){

                $beforeschedule_time = strtotime($UserRequest->schedule_at."- 1 hour");
                $afterschedule_time = strtotime($UserRequest->schedule_at."+ 1 hour");

                $CheckScheduling = UserRequests::where('status','SCHEDULED')
                            ->where('provider_id', Auth::user()->id)
                            ->whereBetween('schedule_at',[$beforeschedule_time,$afterschedule_time])
                            ->count();

                if($CheckScheduling > 0 ){
                    if($request->ajax()) {
                        return response()->json(['error' => trans('api.ride.request_already_scheduled')]);
                    }else{
                        return redirect('dashboard')->with('flash_error', 'If the ride is already scheduled then we cannot schedule/request another ride for the after 1 hour or before 1 hour');
                    }
                }

                RequestFilter::where('request_id',$UserRequest->id)->where('provider_id',Auth::user()->id)->update(['status' => 2]);

                $UserRequest->status = "SCHEDULED";
                $UserRequest->save();

            }else{


                $UserRequest->status = "STARTED";
                $UserRequest->save();


                ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'riding']);

                $Filters = RequestFilter::where('request_id', $UserRequest->id)->where('provider_id', '!=', Auth::user()->id)->get();
                // dd($Filters->toArray());
                foreach ($Filters as $Filter) {
                    $Filter->delete();
                }
            }

            $UnwantedRequest = RequestFilter::where('request_id','!=' ,$UserRequest->id)
                                ->where('provider_id',Auth::user()->id )
                                ->whereHas('request', function($query){
                                    $query->where('status','<>','SCHEDULED');
                                });

            if($UnwantedRequest->count() > 0){
                $UnwantedRequest->delete();
            }  

            // Send Push Notification to User
            (new SendPushNotification)->RideAccepted($UserRequest);

            $User = User::find($UserRequest->user_id);

            $Provider = Provider::find($UserRequest->provider_id);

            if($User){
                $TextMsg = "Dear ".$User->first_name." ".$User->last_name.", Thanks for Booking, We are Ready to Provide the Taxi Service. Your Booking ID : ".$UserRequest->booking_id." Driver Name : ".$Provider->first_name." ".$Provider->last_name." Contact At : ".$Provider->mobile;
                $MessageUrl =  "https://control.msg91.com/api/sendhttp.php?authkey=".env('MSG91KEY')."&mobiles=".$User->mobile."&message=".$TextMsg."&sender=".env('MSGSENDERID')."&route=4&country=91";

                $json = curl($MessageUrl);

                $MessageUrl = json_decode($json, TRUE);
                
                Mail::to($User)->send(new RequestUserBookingReminder($UserRequest->id));
            }  
            

            return $UserRequest->with('user')->get();

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to accept, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
              'status' => 'required|in:ACCEPTED,STARTED,ARRIVED,PICKEDUP,DROPPED,PAYMENT,COMPLETED',
           ]);

        try{

            $UserRequest = UserRequests::findOrFail($id);

            if($request->status == 'DROPPED' && $UserRequest->payment_mode != 'CASH') {
                $UserRequest->status = 'COMPLETED';
    
            } else if ($request->status == 'COMPLETED' && $UserRequest->payment_mode == 'CASH') {
                $UserRequest->status = $request->status;
                $UserRequest->paid = 1;
                ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);
            } else {
                $UserRequest->status = $request->status;
                if($request->status == 'ARRIVED'){
                    $UserRequest->arrived_at=Carbon::now();
                    (new SendPushNotification)->Arrived($UserRequest);
                }
            }
            if($request->status == 'PICKEDUP'){
                $UserRequest->distance  = 0;
                $UserRequest->started_at=Carbon::now();
            }

            $UserRequest->save();

            if($request->status == 'DROPPED') {
                $User = User::where('id',$UserRequest->user->id)->first();
                ProviderService::where('provider_id',$UserRequest->provider_id)->update(['status' =>'active']);

                $UserRequest->finished_at = Carbon::now();
                $UserRequest->d_latitude =  $request->latitude?:$UserRequest->d_latitude;
                $UserRequest->d_longitude =  $request->longitude?:$UserRequest->d_longitude;
                $UserRequest->d_address =  $request->address?:$UserRequest->d_address;
                $UserRequest->save();
                $UserRequest->with('user')->findOrFail($id);
                $UserRequest->invoice = $this->invoice($id);
                if($User !=""){
                   //Mail::to($User)->send(new RequestUserInvoiceReminder($UserRequest->id));
                }  
            }
            // Send Push Notification to User
       
            return $UserRequest;

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to update, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $UserRequest = UserRequests::find($id);

        try {
            if($UserRequest->broad_cast == "NO"){
               $this->assign_next_provider($UserRequest->id); 
            }
            return $UserRequest->with('user')->get();

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Unable to reject, Please try again later']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Connection Error']);
        }
    }

    public function assign_next_provider($request_id) {

        try {
            $UserRequest = UserRequests::findOrFail($request_id);
        } catch (ModelNotFoundException $e) {
            // Cancelled between update.
            return false;
        }

        $RequestFilter = RequestFilter::where('provider_id', $UserRequest->current_provider_id)
            ->where('request_id', $UserRequest->id)
            ->delete();

        try {

            $next_provider = RequestFilter::where('request_id', $UserRequest->id)
                            ->orderBy('id')
                            ->firstOrFail();

            $UserRequest->current_provider_id = $next_provider->provider_id;
            $UserRequest->assigned_at = Carbon::now();
            $UserRequest->save();

            // incoming request push to provider
            (new SendPushNotification)->IncomingRequest($next_provider->provider_id);
            
        } catch (ModelNotFoundException $e) {

            UserRequests::where('id', $UserRequest->id)->update(['status' => 'CANCELLED']);

            // No longer need request specific rows from RequestMeta
            RequestFilter::where('request_id', $UserRequest->id)->delete();

            //  request push to user provider not available
            (new SendPushNotification)->ProviderNotAvailable($UserRequest->user_id);
        }
    }

     /**
     * Get the trip invocie
     *
     * @return \Illuminate\Http\Response
     */

    public function invoice($request_id)
    {
        try {
            $UserRequest = UserRequests::findOrFail($request_id);
            
            $Fixed  = 0;
            $Distance = 0;
            $Discount = 0; // Promo Code discounts should be added here.
            $Minutes = 0; // Promo Code discounts should be added here.
            $DateFees = 0;
            $TravelDay = 0;
            $Connection = $UserRequest->service_type->connection ? : 0;

            $StartedDate  = date_create($UserRequest->started_at);
            $FinisedDate  = date_create($UserRequest->finished_at);
            $TraveledTime = round((strtotime($UserRequest->finished_at) - strtotime($UserRequest->started_at))/3600, 1);
            //$TimeInterval = date_diff($StartedDate,$FinisedDate);
           // $TraveledTime = $TimeInterval->i;
            if($UserRequest->ride_in  == 'INTER'){
               $Fixed = $UserRequest->service_type->fixed ? : 0;
               $Distance = (round($UserRequest->distance,2)) * $UserRequest->service_type->price;
               $DateFees = 0;
               $TravelDay = $DateFees;
               $DateFees  = $DateFees  * $UserRequest->service_type->day;
            }else if($UserRequest->ride_in  == 'OUTER' && $UserRequest->ride_way == 'ONEROUND'){
               $Fixed = $UserRequest->service_type->outer_fixed? : 0;
               $TravelDistance = (round($UserRequest->distance,2));
               if($TravelDistance <= 250){
                  $TravelDistance = 250;
               }
               $TravelDistance = $TravelDistance * 2;
               $Distance  = $TravelDistance * $UserRequest->service_type->outer_price;
               //$DateFees  = $TimeInterval->format('%h');
               if($TraveledTime>0){
                  $DateFees  = ceil($TraveledTime/24);
               }else{
                  $DateFees = 0;
               }
               $TravelDay = $DateFees;
               $DateFees  = $DateFees  * $UserRequest->service_type->day;
               $Fixed     = $Fixed;
               
            }else if($UserRequest->ride_in  == 'OUTER' && ($UserRequest->ride_way == 'ONE' || $UserRequest->ride_way == 'ROUND') ){
               $Fixed = $UserRequest->service_type->outer_fixed? : 0;
               $TravelDistance = (round($UserRequest->distance,2));
               if($TravelDistance <= 250){
                  $TravelDistance = 250;
               }
               $Distance  = $TravelDistance * $UserRequest->service_type->outer_price;
               //$DateFees  = $TimeInterval->format('%h');
               if($TraveledTime>0){
                  $DateFees  = ceil($TraveledTime/24);
               }else{
                  $DateFees = 0;
               }
               $TravelDay = $DateFees;
               $DateFees  = $DateFees  * $UserRequest->service_type->day;
            }

            $UserRequest->traveled_time = $TraveledTime;
            $UserRequest->travel_day = $TravelDay;
            $UserRequest->save();

            if($PromocodeUsage = PromocodeUsage::where('user_id',$UserRequest->user_id)->where('status','ADDED')->first()){
                if($Promocode = Promocode::find($PromocodeUsage->promocode_id)){
                    $Discount = $Promocode->discount;
                    $PromocodeUsage->status ='USED';
                    $PromocodeUsage->save();
                }
            }
            $Wallet = 0;
            $Surge = 0;
            $Paid = 0;

            $Commision = ( $DateFees  + $Fixed + $Distance+$Minutes+$Connection ) * (Setting::get('commission_percentage', 0) / 100);

            $GrossTotal = ($DateFees  + $Minutes + $Fixed + $Distance + $Commision+$Connection)-$Discount ;

            $Tax = ($GrossTotal ) * (Setting::get('tax_percentage', 0) / 100);
            $Total = $GrossTotal  + $Tax ;

            if($UserRequest->surge){
                $Surge = (Setting::get('surge_percentage')/100) * $Total;
                $Total += $Surge;
            }

            if($Total < 0){
                $Total = 0.00; // prevent from negative value
            }
            $Paid = $Total;

            $Payment = new UserRequestPayment;
            $Payment->request_id = $UserRequest->id;
            $Payment->fixed = $Fixed;
            $Payment->distance = $Distance;
            $Payment->minutes = $Minutes;
            $Payment->commision = $Commision;
            $Payment->gross_total = $GrossTotal;
            $Payment->connection = $Connection;
            $Payment->day = $DateFees;  
            $Payment->total = abs($Total);
            $Payment->surge = $Surge;
            if($Discount != 0 && $PromocodeUsage){
                $Payment->promocode_id = $PromocodeUsage->promocode_id;
            }
            $Payment->discount = $Discount;

            if($UserRequest->use_wallet == 1 && $Paid > 0){

                $User = User::find($UserRequest->user_id);

                $Wallet = $User->wallet_balance;

                if($Wallet != 0){

                    if($Paid > $Wallet) {

                        $Payment->wallet = $Wallet;
                        $Payable = $Paid - $Wallet;
                        User::where('id',$UserRequest->user_id)->update(['wallet_balance' => 0 ]);
                        $Payment->paid = abs($Payable);

                        // charged wallet money push 
                        (new SendPushNotification)->ChargedWalletMoney($UserRequest->user_id,currency($Wallet));

                    } else {

                        $Payment->paid = 0;
                        $WalletBalance = $Wallet - $Paid;
                        User::where('id',$UserRequest->user_id)->update(['wallet_balance' => $WalletBalance]);
                        $Payment->wallet = $Paid;
                        
                        $Payment->payment_id = 'WALLET';
                        $Payment->payment_mode = $UserRequest->payment_mode;
                        $Payment->paid = 1;

                        $UserRequest->paid = 1;
                        $UserRequest->status = 'COMPLETED';
                        $UserRequest->save();

                        // charged wallet money push 
                        (new SendPushNotification)->ChargedWalletMoney($UserRequest->user_id,currency($Paid));
                    }

                }

            } else {
                $Payment->paid = abs($Paid);
            }

            $Payment->tax = $Tax;
            $Payment->save();

            return $Payment;

        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
    /**
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function history_details(Request $request)
    {
        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);

        if($request->ajax()) {
            
            $Jobs = UserRequests::where('id',$request->request_id)
                                ->where('provider_id', Auth::user()->id)
                                ->with('payment','service_type','user','rating')
                                ->get();
            if(!empty($Jobs)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x000000|weight:3|enc:".$value->route_key.
                            "&key=".env('GOOGLE_MAP_KEY');
                }
            }

            return $Jobs;
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function upcoming_trips() {
    
        try{
            $UserRequests = UserRequests::ProviderUpcomingRequest(Auth::user()->id)->get();
            if(!empty($UserRequests)){
                $map_icon = asset('asset/marker.png');
                foreach ($UserRequests as $key => $value) {
                    $UserRequests[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                                    "autoscale=1".
                                    "&size=320x130".
                                    "&maptype=terrian".
                                    "&format=png".
                                    "&visual_refresh=true".
                                    "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                                    "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                                    "&path=color:0x000000|weight:3|enc:".$value->route_key.
                                    "&key=".env('GOOGLE_MAP_KEY');
                }
            }
            return $UserRequests;
        }

        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function upcoming_details(Request $request)
    {
        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);

        if($request->ajax()) {
            
            $Jobs = UserRequests::where('id',$request->request_id)
                                ->where('provider_id', Auth::user()->id)
                                ->with('service_type','user')
                                ->get();
            if(!empty($Jobs)){
                $map_icon = asset('asset/img/marker-start.png');
                foreach ($Jobs as $key => $value) {
                    $Jobs[$key]->static_map = "https://maps.googleapis.com/maps/api/staticmap?".
                            "autoscale=1".
                            "&size=320x130".
                            "&maptype=terrian".
                            "&format=png".
                            "&visual_refresh=true".
                            "&markers=icon:".$map_icon."%7C".$value->s_latitude.",".$value->s_longitude.
                            "&markers=icon:".$map_icon."%7C".$value->d_latitude.",".$value->d_longitude.
                            "&path=color:0x000000|weight:3|enc:".$value->route_key.
                            "&key=".env('GOOGLE_MAP_KEY');
                }
            }

            return $Jobs;
        }

    }

    /**
     * Get the trip history details of the provider
     *
     * @return \Illuminate\Http\Response
     */
    public function summary(Request $request)
    {
        try{
            if($request->ajax()) {
                $rides = UserRequests::where('provider_id', Auth::user()->id)->count();
                $revenue = UserRequestPayment::whereHas('request', function($query) use ($request) {
                                $query->where('provider_id', Auth::user()->id);
                            })
                        ->sum('total');
                $cancel_rides = UserRequests::where('status','CANCELLED')->where('provider_id', Auth::user()->id)->count();
                $scheduled_rides = UserRequests::where('status','SCHEDULED')->where('provider_id', Auth::user()->id)->count();

                return response()->json([
                    'rides' => $rides, 
                    'revenue' => $revenue,
                    'cancel_rides' => $cancel_rides,
                    'scheduled_rides' => $scheduled_rides,
                ]);
            }

        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')]);
        }

    }


    /**
     * help Details.
     *
     * @return \Illuminate\Http\Response
     */

    public function help_details(Request $request){

        try{

            if($request->ajax()) {
                return response()->json([
                    'contact_number' => Setting::get('contact_number',''), 
                    'contact_email' => Setting::get('contact_email','')
                     ]);
            }

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['error' => trans('api.something_went_wrong')]);
            }
        }
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function chat_histroy(Request $request)
    {
        $this->validate($request, [
                'request_id' => 'required|integer|exists:user_requests,id',
            ]);
        try{
            $Chat = Chat::where('request_id',$request->request_id)
                        ->where('provider_id', Auth::user()->id)
                        ->get();
            return response()->json(["status"=>true,"messages"=>$Chat]);
        }catch (Exception $e) {
            return response()->json(["status"=>false,'error' => trans('api.something_went_wrong')], 500);
        }
    }

}
