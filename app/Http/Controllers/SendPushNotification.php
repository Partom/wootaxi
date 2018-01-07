<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\ProviderDevice;
use Exception;

class SendPushNotification extends Controller
{
	/**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function RideAccepted($request){

    	return $this->sendPushToUser($request->user_id, trans('api.push.request_accepted'));
    }

    /**
     * Notify driver message push.
     *
     * @return void
     */
    public function UserNotify($user,$message,$type){

        return $this->sendPushToUser($user,$message,$type);
    }

   /**
     * Notify user message push.
     *
     * @return void
     */
    public function ProviderNotify($provider,$message,$type){

        return $this->sendPushToProvider($provider,$message,$type);

    }
    
    /**
     * Notify driver message push.
     *
     * @return void
     */
    public function GeneralUserPushNotify($user,$message){

        return $this->sendPushToUser($user,$message);
    }

    /**
     * Notify driver message push.
     *
     * @return void
     */
    public function GeneralProviderPushNotify($provider,$message){

        return $this->sendPushToProvider($provider,$message);
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function user_schedule($user){

        return $this->sendPushToUser($user, trans('api.push.schedule_start'));
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function user_booking($user,$bookingId){

        $MessageText = "Woocabs Booking Confirmation Alert. Your Booking ID Is ".$bookingId;
        return $this->sendPushToUser($user,$MessageText );
    }

    /**
     * New Incoming request
     *
     * @return void
     */
    public function provider_schedule($provider){

        return $this->sendPushToProvider($provider, trans('api.push.schedule_start'));

    }

    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function UserCancellRide($request){

        return $this->sendPushToProvider($request->provider_id, trans('api.push.user_cancelled'));
    }


    /**
     * New Ride Accepted by a Driver.
     *
     * @return void
     */
    public function ProviderCancellRide($request){

        return $this->sendPushToUser($request->user_id, trans('api.push.provider_cancelled'));
    }

    /**
     * Driver Arrived at your location.
     *
     * @return void
     */
    public function Arrived($request){

        return $this->sendPushToUser($request->user_id, trans('api.push.arrived'));
    }

    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function ProviderNotAvailable($user_id){

        return $this->sendPushToUser($user_id,trans('api.push.provider_not_available'));
    }

    /**
     * New Incoming request
     *
     * @return void
     */
    public function IncomingRequest($provider){

        return $this->sendPushToProvider($provider, trans('api.push.incoming_request'));

    }
    

    /**
     * Driver Documents verfied.
     *
     * @return void
     */
    public function DocumentsVerfied($provider_id){

        return $this->sendPushToProvider($provider_id, trans('api.push.document_verfied'));
    }

    /**
     * Driver Documents verfied.
     *
     * @return void
     */
    public function DriverUnApproved($provider_id){

        return $this->sendPushToProvider($provider_id, 'Your account has been banned, please contact WooCabs admin.');
    }

    /**
     * Driver Documents verfied.
     *
     * @return void
     */
    public function DriverApproved($provider_id){

        return $this->sendPushToProvider($provider_id, 'Your account has been approved by WooCabs admin');
    }

     /**
     * Driver Documents verfied.
     *
     * @return void
     */
    public function DriverDocumentApproved($provider_id,$message){

        return $this->sendPushToProvider($provider_id, $message);
    }
    
     /**
     * Driver Documents verfied.
     *
     * @return void
     */
    public function DriverDocumentUnApproved($provider_id,$message){

        return $this->sendPushToProvider($provider_id, $message);
    }


    /**
     * Money added to user wallet.
     *
     * @return void
     */
    public function WalletMoney($user_id, $money){

        return $this->sendPushToUser($user_id, $money.' '.trans('api.push.added_money_to_wallet'));
    }

    /**
     * Money charged from user wallet.
     *
     * @return void
     */
    public function ChargedWalletMoney($user_id, $money){

        return $this->sendPushToUser($user_id, $money.' '.trans('api.push.charged_from_wallet'));
    }

    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function sendPushToUser($user_id, $push_message,$type="trip"){

    	try{

	    	$user = User::findOrFail($user_id);

            if($user->device_token != ""){

    	    	if($user->device_type == 'ios'){

    	    		return \PushNotification::app('IOSUser')
    		            ->to($user->device_token)
    		            ->send($push_message);

    	    	}elseif($user->device_type == 'android'){
    	    		
    	    		return \PushNotification::app('AndroidUser')
    		            ->to($user->device_token)
    		            ->send($push_message);

    	    	}
            }

    	} catch(Exception $e){
    		return $e;
    	}

    }

    /**
     * Sending Push to a user Device.
     *
     * @return void
     */
    public function sendPushToProvider($provider_id, $push_message,$type="trip"){

    	try{

	    	$provider = ProviderDevice::where('provider_id',$provider_id)->first();

            if($provider->token != ""){

            	if($provider->type == 'ios'){
            		
            		return \PushNotification::app('IOSProvider')
        	            ->to($provider->token)
        	            ->send($push_message);

            	}elseif($provider->type == 'android'){
            		
            		return \PushNotification::app('AndroidProvider')
        	            ->to($provider->token)
        	            ->send($push_message);

            	}
            }

    	} catch(Exception $e){
    		return $e;
    	}

    }

}
