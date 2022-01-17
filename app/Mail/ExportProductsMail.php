<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportProductsMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $employee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.export_products')
                    ->subject("Han exportado los Productos")
                    ->with([
                        'fullName' => $this->employee->user->name . ' ' . $this->employee->user->surname,
                    ]);
    }
}
