<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Lead extends Mailable
{
    use Queueable, SerializesModels;

    public $club;
	public $user;
	public $lead;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\User $user, \App\Club $club, \App\Leads\Base $lead)
    {
	    $this->club = $club;
	    $this->user = $user;
	    $this->lead = $lead;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(ucwords($this->lead->sub_type) . " lead for {$this->club->name}")
                    ->from('no-reply@arcisgolf.com', $this->club->name)
                    ->view('emails.lead');
    }
}
