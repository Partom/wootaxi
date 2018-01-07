<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Setting;
use App\UserRequests;


class RequestUserBookingReminder extends Mailable
{
    use Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = env('MAIL_FROM_ADDRESS');
        $name = Setting::get('site_title','Woocabs Admin');
        $sitename = Setting::get('site_name','Woocabs');
        $subject = 'Woocabs Booking Receipts';
        $check_status = ['CANCELLED','SCHEDULED'];
        $EmailData = UserRequests::with('user','provider','provider_service', 'provider_service.cars','profile','payment')->findOrFail($this->request);

        return $this->view('emails.user.challen')
                ->with('Email',$EmailData)
                ->from($address, $name)
                ->cc($address, $name)
                ->bcc($address, $name)
                ->replyTo($address, $name)
                ->subject($subject);
    }
}
