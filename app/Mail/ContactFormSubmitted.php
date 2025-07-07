<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;
    public $contact;

    /**
     * Create a new message instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        $email =  $this->subject("New contact from Website")
                    ->view("contact.contact_form")
                    ->with([
                        "contact"=> $this->contact
                    ]);

        if ($this->contact->cv) {
            $cvPath = storage_path('app/public/' . $this->contact->cv);
            $email->attach($cvPath);
        }

        return $email;
    }
}
