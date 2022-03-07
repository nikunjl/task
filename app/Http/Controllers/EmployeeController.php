<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Company;
use Redirect;
use Session;
use Auth;

class EmployeeController extends Controller
{
    /*
    * define view path 
    */
    protected $view_path = '';

    /*
    * initialization function 
    */
    public function __construct() {
        $this->view_path = 'employee'; 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::query()->with('companies')
                        ->whereNull('deleted_at')
                        ->paginate(10);

        return View($this->view_path.'/index',compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::query()->whereNull('deleted_at')->get();
        return View($this->view_path.'/create',compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|max:255',
            'last_name'     => 'required|max:255',
            'company_id'    => 'required',
            'email'         => 'email|unique:employee',
            'phone'         => 'max:11',
        ]);

        $employee = New Employee();
        $employee->first_name  = $request->first_name;
        $employee->last_name   = $request->last_name;
        $employee->company_id  = $request->company_id;
        $employee->email       = $request->email;
        $employee->phone       = $request->phone;
        $employee->created_by  = Auth::user()->name;
        $employee->save();

        Session::flash('message', 'Employee Created Successfully!.');
        return redirect()->route('employee.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::find($id);
        $companies = Company::query()->whereNull('deleted_at')->get();
        return View($this->view_path.'/edit',compact('employee','companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name'    => 'required|max:255',
            'last_name'     => 'required|max:255',
            'company_id'    => 'required',
            'email'         => 'required|unique:companies,email,'.$id,
            'phone'         => 'required|max:11',
        ]);

        $employee = Employee::find($id);
        $employee->first_name  = $request->first_name;
        $employee->last_name   = $request->last_name;
        $employee->company_id  = $request->company_id;
        $employee->email       = $request->email;
        $employee->phone       = $request->phone;
        $employee->created_by  = Auth::user()->name;
        $employee->save();

        Session::flash('message', 'Employee Updated Successfully!.');
        return redirect()->route('employee.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->get('id');

        $delete = Employee::find($id)->delete();
        if($delete) {
            Session::flash('message', 'Employee Deleted Successfully.');
            return Redirect::back();
        } 
        
        Session::flash('error', 'Opration Failed!');
        return Redirect::back();
    }
}
