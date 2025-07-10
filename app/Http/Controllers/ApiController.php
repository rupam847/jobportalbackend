<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobAppicationRequest;
use App\Mail\WelcomeEmail;
use App\Models\CompanyJob;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->token = $user->createToken('MyApp')->plainTextToken;
            return response()->json(['success' => true, 'message' => 'Login successful', 'data' => $user]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid credentials']);
    }

    // User Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required|confirmed',
            'role' => 'required|in:company,user'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => $request->role
        ]);

        $user->token = $user->createToken('MyApp')->plainTextToken;
        // send email
        Mail::to($user->email)->queue(new WelcomeEmail($user));
        return response()->json(['success' => true, 'message' => 'Registration successful', 'data' => $user]);
    }

    // Profile update
    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'required',
            'address' => 'required'
        ]);
        $user = Auth::user();
        $user->update($request->only('name', 'email', 'phone', 'address'));
        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }

    // Password update
    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed'
        ]);
        
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect']);
        }

        $user->update(['password' => bcrypt($request->password)]);
        return response()->json(['success' => true, 'message' => 'Password updated successfully']);
    }

    // Dashboard
    public function dashboard()
    {
        $no_header = true;
        $user = Auth::user();
        $total_posts = [];
        if ($user->role == 'company') {
            $total_posts = $user->jobs()->count();
            $jobs = CompanyJob::select('job_id')->where('user_id', auth()->user()->id)->get();
            $total_applications = JobApplication::whereIn('job_id', $jobs->pluck('job_id'))->count();
        } else {
            $total_applications = $user->applications()->count();
        }
        return response()->json(['success' => true, 'total_posts' => $total_posts, 'total_applications' => $total_applications]);
    }

    // Get company applications
    public function applications()
    {
        $applications = JobApplication::with(['user', 'job', 'job.user'])->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $applications]);
    }

    // Delete company application
    public function deleteApplication($id)
    {
        $application = JobApplication::find($id);
        $application->delete();
        return response()->json(['success' => true, 'message' => 'Application deleted successfully']);
    }

    // Get users applications
    public function userApplications()
    {
        $no_header = true;
        $user = Auth::user();
        $applications = JobApplication::where('user_id', $user->id)->get();
        return view('job_applications', compact('applications', 'no_header', 'user'));
    }

    // Get job categories
    public function categories()
    {
        $categories = JobCategory::all();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    // Get Jobs by API
    public function getJobs(Request $request)
    {
        // $job_sort = request('job-sort');
        $job_sort = $request->sort;
        $query = CompanyJob::with(['user', 'category'])->orderBy('created_at', 'desc');
        if (!empty($request->title)) {
            $query->where('job_title', 'like', '%' . $request->title . '%');
        }
        if (!empty($request->location)) {
            $query->where('job_location', 'like', '%' . $request->location . '%');
        }
        if (!empty($request->categories)) {
            $query->whereIn('job_category_id', $request->categories);
        }
        if (!empty($request->salary)) {
            $salary = explode(' - ', $request->salary);
            $query->whereBetween('job_salary', $salary);
        }
        switch ($job_sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'highest-salary':
                $query->orderBy('job_salary', 'desc');
                break;
            default:
                $query->inRandomOrder();
                break;
        }
        $jobs = $query->paginate(10, ['*'], 'page', $request->page);
        // dd($jobs->toArray());
        $total_jobs = $jobs->total();
        return response()->json([
            'success' => true,
            'job_count' => $total_jobs,
            'data' => $jobs,
            // 'request' => $request->all()
        ]);
    }

    // Get single job by id
    public function getsinglejob($id)
    {
        $job = CompanyJob::with(['user', 'category'])->where('job_id', $id)->first();
        return response()->json(['success' => true, 'data' => $job]);
    }

    // Apply Job
    public function applyJob(JobAppicationRequest $request)
    {
        $user = Auth::user();
        $applied = JobApplication::where('user_id', $user->id)->where('job_id', $request->jobid)->first();
        if ($applied) {
            return response()->json(['success' => false, 'message' => 'You have already applied for this job']);
        }
        $data = [
            'user_id' => $user->id,
            'job_id' => $request->jobid,
            'applicant_name' => $request->name,
            'applicant_email' => $request->email,
            'applicant_phone' => $request->phone,
            'applicant_cover_letter' => $request->cover_letter,
        ];

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $name = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/api/resumes'), $name);
            $data['applicant_resume'] = $name;
        }

        JobApplication::create($data);
        return response()->json(['success' => true, 'message' => 'Application submitted successfully']);
    }

}
