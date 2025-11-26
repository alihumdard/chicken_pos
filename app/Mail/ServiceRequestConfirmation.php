<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceRequestConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ServiceRequest $requestDetails
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We Have Received Your Service Request',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-request-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}