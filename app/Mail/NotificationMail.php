<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    /**
     * Create a new message instance using PHP 8 Constructor Promotion.
     *
     * @param string $mailSubject
     * @param string $viewName
     * @param array $viewData
     */
    public function __construct(
        protected string $mailSubject,
        protected string $viewName,
        public $viewData = []
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->viewName,
            with: $this->viewData,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Getters untuk mendukung kebutuhan Unit Testing dan enkapsulasi data.
     */
    public function getMailSubject(): string
    {
        return $this->mailSubject;
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }

    public function getViewData(): array
    {
        return $this->viewData;
    }
}


