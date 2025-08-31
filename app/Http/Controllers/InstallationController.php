<?php

namespace App\Http\Controllers;

use App\Models\Installation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Loan;

class InstallationController extends Controller
{
    public function index()
    {
        $installation = Installation::all();
        $pendingInstallations = Installation::where('status', 'pending')->get();
        $completedInstallations = Installation::where('status', 'completed')->get();
        $installationCount = [
            'total' => $installation->count(),
            'pending' => $pendingInstallations->count(),
            'completed' => $completedInstallations->count(),
        ];
        $installationStatus = [
            'pending' => $pendingInstallations,
            'completed' => $completedInstallations,
        ];
        Log::info('Loading installations index', [
            'installation_count' => $installationCount,
            'installation_status' => $installationStatus,
        ]);
        Log::info('Loading installations index view', [
            'installation_count' => $installationCount,
            'installation_status' => $installationStatus,
        ]);

        return view('installations.index', [
            'installationCount'=>$installationCount,
            'installationStatus' => $installationStatus,
            'installations' => Installation::with(['customerVehicle', 'cylinderType'])->get(),
        ]);
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(Installation $installation) {}

    public function approveInstallation($installationId)
    {
        $installation = Installation::find($installationId);

        if (!$installation) {
            Log::error('Installation not found', ['installation_id' => $installationId]);
            abort(404, 'Installation not found.');
        }

        Log::info('Loading approve installation view', ['installation_id' => $installation->id]);

        return view('installations.approve-installation', [
            'installation' => $installation,
        ]);
    }

    public function edit(Installation $installation) {}

    public function updateInstallation(Request $request, $installationId)
    {
        $installation = Installation::find($installationId);

        $loan = $installation->loan ?? null;

        if (!$installation) {
            Log::error('Installation not found for update', ['installation_id' => $installationId]);
            return redirect()->route('users.index')
                ->with('error', 'Installation not found.');
        }

        Log::info('Validating installation', [
            'installation_id' => $installation->id,
            'request_data' => $request->all(),
        ]);

        $request->validate([
            'status' => 'required|in:pending,completed',
        ]);

        Log::info('Updating installation', [
            'installation_id' => $installation->id,
            'status' => $request->status,
        ]);

        $installation->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        if ($loan) {
            $loan->update([
                'loan_start_date' => now(),
            ]);
        } else {
            Log::error('No loan associated with installation ID: ' . $installation->id);
        }

        // ✅ SEND SMS IF STATUS IS "completed"
        if ($request->status === 'completed' && $loan) {
            $phoneNumber = $loan->applicant_phone_number;
            $fullName = $loan->applicant_name;

            // Format number to +255 if needed
            if (preg_match('/^0\d{9}$/', $phoneNumber)) {
                $phoneNumber = '+255' . substr($phoneNumber, 1);
            }

            $username = 'MIKE001';
            $apiKey = 'atsk_a37133bcba27a4928705557b9903b016812000533f89a91f06747a289a8654dca1dac55d';
            $enqueue = 1;
            $message = "Congratulations!  $fullName, You can now start using your loan..";

            try {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'apiKey' => $apiKey,
                ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
                    'username' => $username,
                    'to' => $phoneNumber,
                    'from' => 'NK CNG',
                    'message' => $message,
                    'enqueue' => $enqueue,
                ]);

                if ($response->successful()) {
                    Log::info('SMS sent successfully to customer', [
                        'phone' => $phoneNumber,
                        'message' => $message,
                    ]);
                } else {
                    Log::error('Failed to send SMS', [
                        'response' => $response->body(),
                        'phone' => $phoneNumber,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('SMS sending exception', [
                    'error' => $e->getMessage(),
                    'phone' => $phoneNumber,
                ]);
            }
        }

        Log::info('Installation updated', [
            'installation_id' => $installation->id,
            'status' => $installation->status,
        ]);

        $userId = $installation->customervehicle?->user_id;

        Log::info('Checking user ID', [
            'installation_id' => $installation->id,
            'user_id' => $userId,
        ]);

        if (!$userId) {
            Log::warning('User ID not found for installation', [
                'installation_id' => $installation->id,
                'customer_vehicle_id' => $installation->customer_vehicle_id,
            ]);
            return redirect()->route('users')
                ->with('success', 'Installation updated successfully, but user not found.');
        }

        return redirect()->route('show-user', $userId)
            ->with('success', 'Installation updated successfully.');
    }

    public function destroy(Installation $installation) {}
}
