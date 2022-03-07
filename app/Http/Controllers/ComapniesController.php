<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Storage;
use Session;
use Redirect;
use Auth;

class ComapniesController extends Controller
{
    /*
    * define view path 
    */
    protected $view_path = '';

    /*
    * initialization function 
    */
    public function __construct() {
        $this->view_path = 'companies'; 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::query()->whereNull('deleted_at')
                        ->paginate(10);

        return View($this->view_path.'/index',compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View($this->view_path.'/create');
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
            'name'      => 'required|max:255',
            'email'     => 'nullable|email|unique:companies',
            'logo'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=100,min_height=100',
            'website'   => 'nullable|max:150',
        ]);

        if ($request->hasFile('logo')) {
            $format = $request->file('logo')->getClientOriginalExtension();
            $imageName = $request->name.".".$format;
            Storage::disk('public')->put($imageName, file_get_contents($request->file('logo')));
        }

        $companies = new Company();
        $companies->name    = $request->name;
        $companies->email   = $request->email;
        $companies->logo    = isset($imageName) ? $imageName : "";
        $companies->website = $request->website;
        $companies->created_by  = Auth::user()->name;
        $companies->save();

        Session::flash('message', 'Companies Created Successfully!');
        return redirect()->route('comapnies.index');
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $companies = Company::find($id);
        return View($this->view_path.'/edit',compact('companies'));
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
            'name'      => 'required|max:255',
            'email'     => 'nullable|unique:companies,email,'.$id,
            'logo'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=100,min_height=100',
            'website'   => 'nullable|max:150',
        ]);

        if ($request->file('logo') != null) {
            $format = $request->file('logo')->getClientOriginalExtension();
            $imageName = $request->name.".".$format;            
            Storage::disk('public')->put($imageName, file_get_contents($request->file('logo')));
        } else {
            $imageName = $request->old_logo;
        }

        $companies = Company::find($id);
        $companies->name = $request->name;
        $companies->email = $request->email;
        $companies->logo = isset($imageName) ? $imageName : "";
        $companies->website = $request->website;
        $companies->created_by  = Auth::user()->name;
        $companies->save();

        Session::flash('message', 'Companies Updated Successfully!');
        return redirect()->route('comapnies.index');
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

        $delete = Company::find($id)->delete();
        if($delete) {
            $companies = Company::find($id);
            if(!empty($companies['logo'])) {
                Storage::disk('public')->delete($companies['logo']);    
            }
                        
            Session::flash('message', 'Companies Deleted Successfully.');
            return Redirect::back();
        } 

        Session::flash('error', 'Opration Failed!');
        return Redirect::back();
    }
}
