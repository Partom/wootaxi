<?php

namespace App\Http\Controllers\ProviderAuth;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;

use Tymon\JWTAuth\Exceptions\JWTException;
use App\Notifications\ResetPasswordOTP;

use Auth;
use Config;
use JWTAuth;
use Setting;
use Notification;
use Validator;
use Socialite;

use App\Provider;
use App\ProviderDevice;
use App\ProviderService;
use App\ProviderDocument;
use App\ServiceCarBrand;
use App\RequestFilter;
use App\ServiceType;

class TokenController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
    {
        $this->validate($request, [
                'device_id' => 'required',
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                //'email' => 'required|email|max:255|unique:providers',
                'mobile' => 'required|digits_between:6,13',
                'password' => 'required|min:6|confirmed',
                'photo' => 'required|mimes:jpeg,bmp,png',
                'ride'=>'required|in:INTER,OUTER,BOTH',
                /*'insurance_document' => 'required|mimes:jpg,jpeg,png,pdf',
                'license_document' => 'required|mimes:jpg,jpeg,png,pdf',
                'photo' => 'required|mimes:jpeg,bmp,png',
                'service_type' => 'required|numeric|exists:service_types,id',
                'service_model' => 'required|numeric|exists:car_categories,id',
                'service_number' => 'required',
                'service_color' => 'required'*/
            ]);

        try{
           
            $isExists = Provider::where('email', $request->email)->first();
            if($isExists){
            	return ['status'=>false,'data' => []];
            } 
            
            $Provider = $request->all();
            $Provider['password'] = bcrypt($request->password);
            
            if ($request->hasFile('photo')) {
                $Provider['avatar'] = $request->photo->store('provider/profile');
            }

            $Provider = Provider::create($Provider);
            /*ProviderService::create([
                'provider_id' => $Provider->id,
                'service_type_id' => $request->service_type,
                'status' => 'offline',
                'service_number' => $request->service_number,
                'car_categories_id' => $request->service_model,
                'property' => $request->property,
                'service_color' => $request->service_color
            ]);

            if ($request->hasFile('license_document')) {
             ProviderDocument::create([
                    'url' => $request->license_document->store('provider/documents'),
                    'provider_id' => $Provider->id,
                    'document_id' => 1,
                    'status' => 'ASSESSING',
                ]);
             }
            if ($request->hasFile('insurance_document')) {
              ProviderDocument::create([
                    'url' => $request->insurance_document->store('provider/documents'),
                    'provider_id' => $Provider->id,
                    'document_id' => 6,
                    'status' => 'ASSESSING',
                ]);
             }*/

            ProviderDevice::create([
                    'provider_id' => $Provider->id,
                    'udid' => $request->device_id,
                    'token' => $request->device_token,
                    'type' => $request->device_type,
                ]);
           
            return ['status'=> true,'data' => $Provider];
        } catch (QueryException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
            }
            return abort(500);
        }
        
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function insurance_document(Request $request)
    {
        $this->validate($request, [
                'id' => 'required',
                'insurance_document' => 'required|mimes:jpg,jpeg,png,pdf',
                'license_document' => 'required|mimes:jpg,jpeg,png,pdf',
                'service_type' => 'required|numeric|exists:service_types,id',
                'service_model' => 'required|numeric|exists:car_categories,id',
                'service_number' => 'required',
                'service_color' => 'required'
            ]);

        try{ 
            $Provider = Provider::find($request->id);
	    if($Provider){
            ProviderService::create([
                'provider_id' => $Provider->id,
                'service_type_id' => $request->service_type,
                'status' => 'offline',
                'service_number' => $request->service_number,
                'car_categories_id' => $request->service_model,
                'property' => 'own',
                'service_color' => $request->service_color
            ]);

            if ($request->hasFile('license_document')) {
             ProviderDocument::create([
                    'url' => $request->license_document->store('provider/documents'),
                    'provider_id' => $Provider->id,
                    'document_id' => 1,
                    'status' => 'ASSESSING',
                ]);
             }
            if ($request->hasFile('insurance_document')) {
              ProviderDocument::create([
                    'url' => $request->insurance_document->store('provider/documents'),
                    'provider_id' => $Provider->id,
                    'document_id' => 6,
                    'status' => 'ASSESSING',
                ]);
             }
             	
            	return $Provider;
            }else{
            	return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
            }
        } catch (QueryException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Something went wrong, Please try again later!'.$e], 500);
            }
            return abort(500);
        }
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function license_document(Request $request)
    {
        $this->validate($request, [
                'id' => 'required',
                //'insurance_document' => 'required|mimes:jpg,jpeg,png,pdf',
                'license_document' => 'required|mimes:jpg,jpeg,png,pdf',
                'service_type' => 'required|numeric|exists:service_types,id',
                'service_model' => 'required|numeric|exists:car_categories,id',
                'service_number' => 'required',
                'service_color' => 'required'
            ]);

        try{
            	$Provider = Provider::find($request->id);
	    	if($Provider){
	            ProviderService::create([
	                'provider_id' => $Provider->id,
	                'service_type_id' => $request->service_type,
	                'status' => 'offline',
	                'service_number' => $request->service_number,
	                'car_categories_id' => $request->service_model,
	                'property' => $request->property,
	                'service_color' => $request->service_color
	            ]);
	
	            if ($request->hasFile('license_document')) {
	             ProviderDocument::create([
	                    'url' => $request->license_document->store('provider/documents'),
	                    'provider_id' => $Provider->id,
	                    'document_id' => 1,
	                    'status' => 'ASSESSING',
	                ]);
	             }
	            /*if ($request->hasFile('insurance_document')) {
	              ProviderDocument::create([
	                    'url' => $request->insurance_document->store('provider/documents'),
	                    'provider_id' => $Provider->id,
	                    'document_id' => 6,
	                    'status' => 'ASSESSING',
	                ]);
	             }*/
            	return $Provider;
            }else{
            	return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
            }
        } catch (QueryException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
            }
            return abort(500);
        }
        
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function vehicle_document(Request $request)
    {
        $this->validate($request, [
                'id' => 'required',
                'vehicle_permit' => 'required|mimes:jpg,jpeg,png,pdf',
                'vehicle_register' => 'required|mimes:jpg,jpeg,png,pdf',
                'police_certificate' => 'required|mimes:jpg,jpeg,png,pdf'
            ]);

        try{
            	$Provider = Provider::find($request->id);
	    	if($Provider){
	           
	            if ($request->hasFile('vehicle_permit')) {
	             ProviderDocument::create([
	                    'url' => $request->vehicle_permit->store('provider/documents'),
	                    'provider_id' => $Provider->id,
	                    'document_id' => 4,
	                    'status' => 'ASSESSING',
	                ]);
	             }
	            if ($request->hasFile('vehicle_register')) {
	              ProviderDocument::create([
	                    'url' => $request->vehicle_register->store('provider/documents'),
	                    'provider_id' => $Provider->id,
	                    'document_id' => 5,
	                    'status' => 'ASSESSING',
	                ]);
	             }
	             if ($request->hasFile('police_certificate')) {
	              ProviderDocument::create([
	                    'url' => $request->police_certificate->store('provider/documents'),
	                    'provider_id' => $Provider->id,
	                    'document_id' => 7,
	                    'status' => 'ASSESSING',
	                ]);
	             }
            	return $Provider;
            }else{
            	return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
            }
        } catch (QueryException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
            }
            return abort(500);
        }
        
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function authenticate(Request $request)
    {
        $this->validate($request, [
                'device_id' => 'required',
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

        Config::set('auth.providers.users.model', 'App\Provider');

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'The email address or password you entered is incorrect.'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Something went wrong, Please try again later!'], 500);
        }

        $User = Provider::with('service', 'device')->find(Auth::user()->id);

        $User->access_token = $token;
        $User->currency = Setting::get('currency', '$');
        $User->sos = Setting::get('sos_number', '911');

        if($User->device) {
            
            ProviderDevice::where('id',$User->device->id)->update([
                'udid' => $request->device_id,
                'token' => $request->device_token,
                'type' => $request->device_type,
            ]);
            
        } else {
            ProviderDevice::create([
                    'provider_id' => $User->id,
                    'udid' => $request->device_id,
                    'token' => $request->device_token,
                    'type' => $request->device_type,
                ]);
        }

        return response()->json($User);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function logout(Request $request)
    {
        try {
            ProviderDevice::where('provider_id', $request->id)->update(['udid'=> '', 'token' => '']);
            ProviderService::where('provider_id',$request->id)->update(['status' =>'offline']);
            $UnwantedRequest = RequestFilter::where('provider_id',$request->id )
                                ->whereHas('request', function($query){
                                    $query->where('status','SEARCHING');
                                });

            if($UnwantedRequest->count() > 0){
                $UnwantedRequest->delete();
            }  
            return response()->json(['message' => trans('api.logout_success')]);
        } catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }

 /**
     * Forgot Password.
     *
     * @return \Illuminate\Http\Response
     */


    public function forgot_password(Request $request){

        $this->validate($request, [
                'email' => 'required|email|exists:providers,email',
            ]);

        try{  
            
            $provider = Provider::where('email' , $request->email)->first();

            $otp = mt_rand(100000, 999999);

            $provider->otp = $otp;
            $provider->save();

            Notification::send($provider, new ResetPasswordOTP($otp));

            return response()->json([
                'message' => 'OTP sent to your email!',
                'provider' => $provider
            ]);

        }catch(Exception $e){
                return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }


    /**
     * Reset Password.
     *
     * @return \Illuminate\Http\Response
     */

    public function reset_password(Request $request){

        $this->validate($request, [
                'password' => 'required|confirmed|min:6',
                'id' => 'required|numeric|exists:providers,id'
            ]);

        try{

            $Provider = Provider::findOrFail($request->id);
            $Provider->password = bcrypt($request->password);
            $Provider->save();

            if($request->ajax()) {
                return response()->json(['message' => 'Password Updated']);
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
    public function facebookViaAPI(Request $request) { 

        $validator = Validator::make(
            $request->all(),
            [
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'accessToken'=>'required',
                'device_id' => 'required',
                'login_by' => 'required|in:manual,facebook,google'
            ]
        );
        
        if($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->all()]);
        }
        $user = Socialite::driver('facebook')->stateless();
        $FacebookDrive = $user->userFromToken( $request->accessToken);
       
        try{
            $FacebookSql = Provider::where('social_unique_id',$FacebookDrive->id);
            if($FacebookDrive->email !=""){
                $FacebookSql->orWhere('email',$FacebookDrive->email);
            }
            $AuthUser = $FacebookSql->first();
            if($AuthUser){ 
                $AuthUser->social_unique_id=$FacebookDrive->id;
                $AuthUser->login_by="facebook";
                $AuthUser->save();  
            }else{   
                $AuthUser["email"]=$FacebookDrive->email;
                $name = explode(' ', $FacebookDrive->name, 2);
                $AuthUser["first_name"]=$name[0];
                $AuthUser["last_name"]=isset($name[1]) ? $name[1] : '';
                $AuthUser["password"]=bcrypt($FacebookDrive->id);
                $AuthUser["social_unique_id"]=$FacebookDrive->id;
                $AuthUser["avatar"]=$FacebookDrive->avatar;
                $AuthUser["login_by"]="facebook";
                $AuthUser = Provider::create($AuthUser);

                if(Setting::get('demo_mode', 0) == 1) {
                    $AuthUser->update(['status' => 'approved']);
                    ProviderService::create([
                        'provider_id' => $AuthUser->id,
                        'service_type_id' => '1',
                        'status' => 'active',
                        'service_number' => '4pp03ets',
                        'service_model' => 'Audi R8',
                    ]);
                }
            }    
            if($AuthUser){ 
                $userToken = JWTAuth::fromUser($AuthUser);
                $User = Provider::with('service', 'device')->find($AuthUser->id);
                if($User->device) {
              
                    ProviderDevice::where('id',$User->device->id)->update([
                            'udid' => $request->device_id,
                            'token' => $request->device_token,
                            'type' => $request->device_type,
                    ]);
                    
                } else {
                    ProviderDevice::create([
                        'provider_id' => $User->id,
                        'udid' => $request->device_id,
                        'token' => $request->device_token,
                        'type' => $request->device_type,
                    ]);
                }
                return response()->json([
                            "status" => true,
                            "token_type" => "Bearer",
                            "access_token" => $userToken,
                            'currency' => Setting::get('currency', '$'),
                            'sos' => Setting::get('sos_number', '911')
                        ]);
            }else{
                return response()->json(['status'=>false,'message' => "Invalid credentials!"]);
            }  
        } catch (Exception $e) {
            return response()->json(['status'=>false,'message' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function googleViaAPI(Request $request) { 

        $validator = Validator::make(
            $request->all(),
            [
                'device_type' => 'required|in:android,ios',
                'device_token' => 'required',
                'accessToken'=>'required',
                'device_id' => 'required',
                'login_by' => 'required|in:manual,facebook,google'
            ]
        );
        
        if($validator->fails()) {
            return response()->json(['status'=>false,'message' => $validator->messages()->all()]);
        }
        $user = Socialite::driver('google')->stateless();
        $GoogleDrive = $user->userFromToken( $request->accessToken);
       
        try{
            $GoogleSql = Provider::where('social_unique_id',$GoogleDrive->id);
            if($GoogleDrive->email !=""){
                $GoogleSql->orWhere('email',$GoogleDrive->email);
            }
            $AuthUser = $GoogleSql->first();
            if($AuthUser){
                $AuthUser->social_unique_id=$GoogleDrive->id;  
                $AuthUser->login_by="google";
                $AuthUser->save();
            }else{   
                $AuthUser["email"]=$GoogleDrive->email;
                $name = explode(' ', $GoogleDrive->name, 2);
                $AuthUser["first_name"]=$name[0];
                $AuthUser["last_name"]=isset($name[1]) ? $name[1] : '';
                $AuthUser["password"]=($GoogleDrive->id);
                $AuthUser["social_unique_id"]=$GoogleDrive->id;
                $AuthUser["avatar"]=$GoogleDrive->avatar;
                $AuthUser["login_by"]="google";
                $AuthUser = Provider::create($AuthUser);

                if(Setting::get('demo_mode', 0) == 1) {
                    $AuthUser->update(['status' => 'approved']);
                    ProviderService::create([
                        'provider_id' => $AuthUser->id,
                        'service_type_id' => '1',
                        'status' => 'active',
                        'service_number' => '4pp03ets',
                        'service_model' => 'Audi R8',
                    ]);
                }
            }    
            if($AuthUser){
                $userToken = JWTAuth::fromUser($AuthUser);
                $User = Provider::with('service', 'device')->find($AuthUser->id);
                if($User->device) {
                    
                    ProviderDevice::where('id',$User->device->id)->update([
                        'udid' => $request->device_id,
                        'token' => $request->device_token,
                        'type' => $request->device_type,
                    ]);
                    
                } else {
                    ProviderDevice::create([
                        'provider_id' => $User->id,
                        'udid' => $request->device_id,
                        'token' => $request->device_token,
                        'type' => $request->device_type,
                    ]);
                }
                return response()->json([
                            "status" => true,
                            "token_type" => "Bearer",
                            "access_token" => $userToken,
                            'currency' => Setting::get('currency', '$'),
                            'sos' => Setting::get('sos_number', '911')
                        ]);
            }else{
                return response()->json(['status'=>false,'message' => "Invalid credentials!"]);
            }  
        } catch (Exception $e) {
            return response()->json(['status'=>false,'message' => trans('api.something_went_wrong')]);
        }
    }

    /**
     * terms Details.
     *
     * @return \Illuminate\Http\Response
     */

    public function terms(Request $request){

        try{

            if($request->ajax()) {
                return response()->json(['status'=>true,
                    'terms' => Setting::get('provider_terms') 
                     ]);
            }

        }catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['status'=>false,'error' => trans('api.something_went_wrong')]);
            }
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function services() {

        if($serviceList = ServiceType::all()) {
            return $serviceList;
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 500);
        }

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function brands($service) {

        $serviceBrand = ServiceCarBrand::where('service_type_id',$service)->with('service_type','cars')->get();
        if($serviceBrand) {
            return $serviceBrand;
        } else {
            return response()->json(['error' => trans('api.services_not_found')], 500);
        }
    }
}
