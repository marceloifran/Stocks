<?php

namespace App\Services;

use App\Mail\QuoteRequestMail;
use App\Models\EmailLog;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Mail;

class SendQuotationRequestService
{
    /**
     * Send quote requests to all selected suppliers for a purchase request.
     */
    public function send(PurchaseRequest $request)
    {
        $suppliers = $request->suppliers;

        foreach ($suppliers as $supplier) {
            try {
                // Prepare the email
                $mailable = new QuoteRequestMail($request);
                
                // Send the email
                Mail::to($supplier->email)->send($mailable);

                // Log the success
                $request->emailLogs()->create([
                    'supplier_id' => $supplier->id,
                    'subject' => "Pedido de Cotización - " . $request->company->name,
                    'body' => $request->product_name . " x " . $request->quantity,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

            } catch (\Exception $e) {
                // Log the failure
                $request->emailLogs()->create([
                    'supplier_id' => $supplier->id,
                    'subject' => "Pedido de Cotización - " . $request->company->name,
                    'body' => "Error: " . $e->getMessage(),
                    'status' => 'failed',
                    'sent_at' => now(),
                ]);
            }
        }

        // Update request status
        $request->update(['status' => 'sent']);
        
        return true;
    }
}
