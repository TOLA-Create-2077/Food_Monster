<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrinterList;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $printers = PrinterList::orderBy('id')->get();
        return view('setting', compact('printers'));
    }

    // Store printer
    public function storePrinter(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'ip' => 'required|ip',
            'printer_type' => 'nullable|string',
            'bill_type' => 'nullable|string',
            'paper_size' => 'nullable|string',
            'status' => 'in:active,inactive',
            'remark' => 'nullable|string',
        ]);
        PrinterList::create($validated);
        return response()->json(['success' => true]);
    }

    // Get printer for edit
    public function editPrinter($id)
    {
        $printer = PrinterList::findOrFail($id);
        return response()->json($printer);
    }

    // Update printer
    public function updatePrinter(Request $request, $id)
    {
        $printer = PrinterList::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'ip' => 'required|ip',
            'printer_type' => 'nullable|string',
            'bill_type' => 'nullable|string',
            'paper_size' => 'nullable|string',
            'status' => 'in:active,inactive',
            'remark' => 'nullable|string',
        ]);
        $printer->update($validated);
        return response()->json(['success' => true]);
    }

    // Delete printer
    public function deletePrinter($id)
    {
        PrinterList::destroy($id);
        return response()->json(['success' => true]);
    }

    // Helper for settings (you can create a helper or use DB)
    public function updateGeneral(Request $request)
    {
        $this->saveSetting('site_name', $request->site_name);
        $this->saveSetting('site_email', $request->site_email);
        $this->saveSetting('site_phone', $request->site_phone);
        return back()->with('success', 'General settings updated');
    }

    public function updatePayment(Request $request)
    {
        $this->saveSetting('currency', $request->currency);
        $this->saveSetting('exchange_rate', $request->exchange_rate);
        return back()->with('success', 'Payment settings updated');
    }

    public function updateEmail(Request $request)
    {
        $this->saveSetting('mail_host', $request->mail_host);
        $this->saveSetting('mail_port', $request->mail_port);
        $this->saveSetting('mail_username', $request->mail_username);
        if ($request->mail_password) {
            $this->saveSetting('mail_password', encrypt($request->mail_password));
        }
        $this->saveSetting('mail_encryption', $request->mail_encryption);
        return back()->with('success', 'Email settings updated');
    }

    private function saveSetting($key, $value)
    {
        DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value, 'updated_at' => now()]);
    }
}