<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExportProductsMail;

class ExportProductsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            Mail::to('administrador@admin.com')->send(new ExportProductsMail($event->employee));
        } catch(Exception $e) {
            Log::error("Error - handle ExportProductsListener -> $e");
        }
        catch (\Throwable $e) {
            Log::error("Error - handle ExportProductsListener -> $e");
        }
    }
}
