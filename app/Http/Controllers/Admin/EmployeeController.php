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
 
    public function __construct()
        {
            $this->middleware('permission:employee.view')
                ->only(['index']);

            $this->middleware('permission:employee.create')
                ->only(['create', 'store']);

            $this->middleware('permission:employee.edit')
                ->only(['edit', 'update']);

            $this->middleware('permission:employee.delete')
                ->only(['destroy']);
        }

        // Employee index
    public function index()
        {
            $employees = \App\Models\Employee::with('user')->get();
            return view('Admin.Employee.index', compact('employees'));
        }
        
        // Employee Create Page Open
    public function create()
        {  
            $roles = Role::all();  
            return view('Admin.Employee.create', compact('roles'));
        }
        
        // Employee creation (Save)
   public function store(StoreEmployeeRequest $request)
{
    DB::beginTransaction();

    try {

        $data = $request->validated();

        $role = Role::where('name', $data['role'])->firstOrFail();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'contact_no' => $data['contact_no'] ?? null,
            'address' => $data['address'] ?? null,
            'role_id' => $role->id,
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        $profilePhotoPath = $request->hasFile('profile_photo')
            ? $request->file('profile_photo')
                ->store('employees/profile_photos', 'public')
            : null;

        $resumePath = $request->hasFile('resume')
            ? $request->file('resume')
                ->store('employees/resumes', 'public')
            : null;

        $idProofPath = $request->hasFile('id_proof')
            ? $request->file('id_proof')
                ->store('employees/id_proofs', 'public')
            : null;

        $certificatesPaths = null;

        if ($request->hasFile('certificates')) {

            $certificates = [];

            foreach ($request->file('certificates') as $file) {
                $certificates[] = $file->store(
                    'employees/certificates',
                    'public'
                );
            }

            $certificatesPaths = json_encode($certificates);
        }

        Employee::create([
            'user_id' => $user->id,
            'contact_no' => $data['contact_no'] ?? null,
            'address' => $data['address'] ?? null,
            'department' => $data['department'] ?? null,
            'designation' => $data['designation'] ?? null,
            'date_of_join' => $data['date_of_join'] ?? null,
            'salary' => $data['salary'] ?? null,
            'profile_photo' => $profilePhotoPath,
            'resume' => $resumePath,
            'certificates' => $certificatesPaths,
            'id_proof' => $idProofPath,
        ]);

        DB::commit();

        return redirect('/Employee')
            ->with('success', 'Employee created successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->with(
                'error',
                'Something went wrong: ' . $e->getMessage()
            );
    }
}
 
        // Edit Emplyeee
    public function edit($id)
        {
            $employee = Employee::with('user')->findOrFail($id); 
            $roles = Role::all(); 
            return view('Admin.Employee.edit', compact('employee', 'roles'));
        }

        // Update Created Emplyee Details 
   public function update(
    UpdateEmployeeRequest $request,
    Employee $employee
) {
    DB::beginTransaction();

    try {

        $data = $request->validated();

        $employee->load('user');

        $employee->user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'contact_no' => $data['contact_no'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        $employee->user->syncRoles([
            $data['role']
        ]);

        if ($request->hasFile('profile_photo')) {

            $employee->profile_photo =
                $request->file('profile_photo')
                    ->store(
                        'employees/profile_photos',
                        'public'
                    );
        }

        if ($request->hasFile('resume')) {

            $employee->resume =
                $request->file('resume')
                    ->store(
                        'employees/resumes',
                        'public'
                    );
        }

        if ($request->hasFile('id_proof')) {

            $employee->id_proof =
                $request->file('id_proof')
                    ->store(
                        'employees/id_proofs',
                        'public'
                    );
        }

        if ($request->hasFile('certificates')) {

            $certificates = [];

            foreach ($request->file('certificates') as $file) {

                $certificates[] = $file->store(
                    'employees/certificates',
                    'public'
                );
            }

            $employee->certificates =
                json_encode($certificates);
        }

        $employee->update([
            'department' => $data['department'] ?? null,
            'designation' => $data['designation'] ?? null,
            'date_of_join' => $data['date_of_join'] ?? null,
            'salary' => $data['salary'] ?? null,
        ]);

        DB::commit();

        return redirect()
            ->route('Employee.index')
            ->with(
                'success',
                'Employee updated successfully!'
            );

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->with(
                'error',
                'Something went wrong: ' . $e->getMessage()
            );
    }
}
            // Delete Employee delete
    public function destroy($id)
        {
                DB::beginTransaction();

                try {
                    $employee = Employee::findOrFail($id);

                    User::where('id', $employee->user_id)->delete();
                    $employee->delete();

                    DB::commit();

                    return redirect('/Employee')
                        ->with('success', 'Employee deleted successfully');

                } catch (\Exception $e) {
                    DB::rollBack();

                    return redirect('/Employee')
                        ->with('error', $e->getMessage());
                }
            }
}
