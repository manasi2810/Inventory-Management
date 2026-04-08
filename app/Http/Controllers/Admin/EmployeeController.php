<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;       
use App\Models\Employee;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = \App\Models\Employee::with('user')->get();

        return view('admin.employee.index', compact('employees'));
    }

public function create()
    { 
     $roles = Role::all();  
    return view('Admin.Employee.create', compact('roles'));
    }

public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact_no' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|string',
            'password' => 'required|string|min:6',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'date_of_join' => 'nullable|date',
            'salary' => 'nullable|numeric',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'certificates.*' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'id_proof' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Step 1: Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                 'role_id' => \Spatie\Permission\Models\Role::where('name', $request->role)->first()->id,
                'password' => Hash::make($request->password),
            ]);
              $user->assignRole($request->role);

            // Step 2: Handle File Uploads
            $profilePhotoPath = $request->file('profile_photo') ? $request->file('profile_photo')->store('employees/profile_photos','public') : null;
            $resumePath = $request->file('resume') ? $request->file('resume')->store('employees/resumes','public') : null;
            $idProofPath = $request->file('id_proof') ? $request->file('id_proof')->store('employees/id_proofs','public') : null;

            $certificatesPaths = null;
            if ($request->hasFile('certificates')) {
                $certificatesPaths = [];
                foreach ($request->file('certificates') as $file) {
                    $certificatesPaths[] = $file->store('employees/certificates','public');
                }
                $certificatesPaths = json_encode($certificatesPaths);
            }

            // Step 3: Create Employee
            Employee::create([
                'user_id' => $user->id,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                'department' => $request->department,
                'designation' => $request->designation,
                'date_of_join' => $request->date_of_join,
                'salary' => $request->salary,
                'profile_photo' => $profilePhotoPath,
                'resume' => $resumePath,
                'certificates' => $certificatesPaths,
                'id_proof' => $idProofPath,
            ]);

            DB::commit();
            return redirect('/Employee')->with('success', 'Employee created successfully'); 
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('admin.employee.edit', compact('employee'));
    }

    // Update employee
    public function update(Request $request, $id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user->id,
            'contact_no' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|string',
            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'date_of_join' => 'nullable|date',
            'salary' => 'nullable|numeric',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:2048',
            'certificates.*' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
            'id_proof' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Update User
            $employee->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                'role' => $request->role,
            ]);

            // File uploads (optional)
            if ($request->hasFile('profile_photo')) {
                $employee->profile_photo = $request->file('profile_photo')->store('employees/profile_photos','public');
            }
            if ($request->hasFile('resume')) {
                $employee->resume = $request->file('resume')->store('employees/resumes','public');
            }
            if ($request->hasFile('id_proof')) {
                $employee->id_proof = $request->file('id_proof')->store('employees/id_proofs','public');
            }
            if ($request->hasFile('certificates')) {
                $certs = [];
                foreach ($request->file('certificates') as $file) {
                    $certs[] = $file->store('employees/certificates','public');
                }
                $employee->certificates = json_encode($certs);
            }

            // Update Employee
            $employee->update([
                'department' => $request->department,
                'designation' => $request->designation,
                'date_of_join' => $request->date_of_join,
                'salary' => $request->salary,
            ]);

            DB::commit();
            return redirect()->route('Employee.index')->with('success', 'Employee updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }
}
