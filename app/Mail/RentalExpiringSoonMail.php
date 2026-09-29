<?php

namespace App\Mail;

use App\Models\Sewa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentalExpiringSoonMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Sewa $rental)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Masa Sewa Anda Akan Berakhir dalam 7 Hari',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.rental-expiring-soon',
            with: [
                'rental' => $this->rental,
            ],
        );
    }
}
