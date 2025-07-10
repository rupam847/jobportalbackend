<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyJobRequest;
use App\Jobs\SendJobAlertEmailsJob;
use App\Models\CompanyJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $jobs = CompanyJob::with('category')->withCount('applications')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $jobs]);
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
    public function store(CompanyJobRequest $request)
    {
        $user = Auth::user();
        $request->merge(['job_id' => uniqid()]);
        $request->merge(['user_id' => $user->id]);
        $job = CompanyJob::create($request->all());

        // Send email alerts to all users
        $users = User::where('role', 'user')->get();
        $new_job = [
            'company_name' => $job->user->name,
            'link' => route('jobs.view', $job->job_id)
        ];
        SendJobAlertEmailsJob::dispatch($users, $new_job);

        return response()->json(['success' => true, 'message' => 'Job created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $job = CompanyJob::where('user_id', $user->id)->where('job_id', $id)->first();
        return response()->json(['success' => true, 'data' => $job]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyJobRequest $request, string $id)
    {
        $user = Auth::user();
        $job = CompanyJob::where('user_id', $user->id)->where('job_id', $id)->update($request->except(['job_id', 'created_at', 'updated_at']));
        return response()->json(['success' => true, 'message' => 'Job updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = CompanyJob::where('job_id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Job deleted successfully']);
    }
}
