<?php

namespace App\Http\Controllers;

use App\Models\Installation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Loan;

class InstallationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Installation $installation)
    {
        //
    }

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
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Installation $installation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Installation $installation)
    {
        //
    }
}
