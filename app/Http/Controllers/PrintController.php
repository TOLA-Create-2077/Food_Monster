<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrinterList;
use App\Models\PrintJob;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\Log;

class PrintController extends Controller
{
    /**
     * Print a bill group to the designated station printer.
     */
    public function printBill(Request $request)
    {
        $request->validate([
            'group' => 'required|array',
            'group.type' => 'required|string', // 'customer' or 'chef'
            'group.station' => 'nullable|string',
            'group.group' => 'nullable|string', // group name (e.g. "Meat Box Kitchen")
            'group.bill_no' => 'nullable|string',
            'group.items' => 'required|array',
            'group.customer_name' => 'nullable|string',
            // ... other fields as needed
        ]);

        $group = $request->input('group');
        
        // 1. Find the appropriate printer
        $printer = $this->findPrinterForGroup($group);
        if (!$printer) {
            return response()->json(['error' => 'No printer configured for this station'], 404);
        }

        // 2. Create print job record
        $printJob = PrintJob::create([
            'job_type' => 'bill',
            'job_id' => $group['bill_no'] ?? null,
            'printer_list_id' => $printer->id,
            'printer_name' => $printer->name,
            'printer_ip' => $printer->ip,
            'print_method' => 'network',
            'status' => 'processing',
            'attempts' => 0,
        ]);

        try {
            // 3. Send print to printer
            $this->sendToPrinter($printer, $group);
            
            $printJob->update([
                'status' => 'completed',
                'printed_at' => now(),
            ]);
            
            return response()->json(['success' => true, 'message' => 'Print job sent']);
        } catch (\Exception $e) {
            Log::error('Print failed: ' . $e->getMessage());
            $printJob->update([
                'status' => 'failed',
                'print_error' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Match a bill group to a printer from printer_lists table.
     */
    private function findPrinterForGroup($group)
    {
        $type = $group['type'] ?? 'chef';
        $station = $group['station'] ?? $group['group'] ?? '';
        
        // Try to find printer by station name (case-insensitive)
        $printer = PrinterList::where('status', 'active')
            ->where(function($q) use ($station, $type) {
                $q->where('name', 'like', "%{$station}%")
                  ->orWhere('product_group_id', $type === 'customer' ? 1 : 2); // custom mapping
            })
            ->first();
        
        if (!$printer && $type === 'customer') {
            // Fallback: customer bill printer
            $printer = PrinterList::where('bill_type', 'customer')->where('status', 'active')->first();
        }
        
        if (!$printer && $type === 'chef') {
            // Fallback: kitchen printer
            $printer = PrinterList::where('bill_type', 'chef')->where('status', 'active')->first();
        }
        
        return $printer;
    }

    /**
     * Generate ESC/POS commands and send to printer IP.
     */
    private function sendToPrinter($printer, $group)
    {
        $connector = new NetworkPrintConnector($printer->ip, 9100);
        $printerObj = new Printer($connector);
        
        try {
            // Optional: logo
            // $logo = EscposImage::load(public_path('images/logo.png'));
            // $printerObj->graphics($logo);
            
            $printerObj->setJustification(Printer::JUSTIFY_CENTER);
            $printerObj->text("Little Duckling\n");
            $printerObj->text(str_repeat("-", 32) . "\n");
            $printerObj->setJustification(Printer::JUSTIFY_LEFT);
            
            $printerObj->text("Table/Order: " . ($group['table_order'] ?? '-') . "\n");
            $printerObj->text("Bill No: " . ($group['bill_no'] ?? '-') . "\n");
            $printerObj->text("Date: " . ($group['date'] ?? date('d-m-Y H:i')) . "\n");
            $printerObj->text("Station: " . ($group['station'] ?? '-') . "\n");
            $printerObj->text("Order Man: " . ($group['order_man'] ?? '-') . "\n");
            $printerObj->text(str_repeat("-", 32) . "\n");
            
            // Customer info (if any)
            if ($group['customer_name']) {
                $printerObj->text("Customer: " . $group['customer_name'] . "\n");
                $printerObj->text("Tel: " . ($group['tel'] ?? '-') . "\n");
                $printerObj->text("Address: " . ($group['address'] ?? '-') . "\n");
                $printerObj->text(str_repeat("-", 32) . "\n");
            }
            
            // Items
            $printerObj->setEmphasis(true);
            $printerObj->text(str_pad("Item", 20) . str_pad("Qty", 8) . "\n");
            $printerObj->setEmphasis(false);
            foreach ($group['items'] as $item) {
                $desc = substr($item['description'] ?? '-', 0, 20);
                $qty = $item['qty'] ?? 1;
                $printerObj->text(str_pad($desc, 20) . str_pad($qty, 8) . "\n");
            }
            
            $printerObj->text(str_repeat("-", 32) . "\n");
            $printerObj->text("Note: " . ($group['note'] ?? '-') . "\n");
            $printerObj->text("\n\n");
            $printerObj->cut();
            $printerObj->close();
        } catch (\Exception $e) {
            $printerObj->close();
            throw $e;
        }
    }
}