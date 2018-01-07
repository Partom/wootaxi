<?php

namespace App\Http\Controllers\Resource;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CarCategory;
use App\Location;
use Setting;

class LocationResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $location = Location::orderBy('created_at' , 'desc')->get();
        return view('admin.location.index', compact('location'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.location.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $this->validate($request, [
            'from_city' => 'required|max:255',
            'from_address' => 'required|max:255',
            'to_city' => 'required|max:255',
            'to_address' => 'required|max:255',
            ]);
       try{
		   $Location["from_address"]= $request->from_address;
		   $Location["from_city"]= $request->from_city;
           $Location["to_address"]= $request->to_address;
           $Location["to_city"]= $request->to_city;
		   $Location["latitude"]= 0.0;
		   $Location["longitude"]= 0.0;
		   Location::create($Location);
       
            return back()->with('flash_success','Location Saved Successfully');
        } catch (Exception $e) {
            //dd("Exception", $e);
            return back()->with('flash_error', 'Location Not Found');
        }
       
            
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\car  $providercar
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\car  $providercar
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $location = Location::find($id);
            return view('admin.location.edit',compact('location'));
        } catch (Exception $e) {
            return back()->with('flash_error', 'Location Not Found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\car  $providercar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $this->validate($request, [
            'from_city' => 'required|max:255',
            'from_address' => 'required|max:255',
            'to_city' => 'required|max:255',
            'to_address' => 'required|max:255',
            ]);
       try{
           $Location = Location::find($id);
           $Location->from_address= $request->from_address;
           $Location->from_city= $request->from_city;
           $Location->to_address= $request->to_address;
           $Location->to_city= $request->to_city;
           $Location->save();

            return back()->with('flash_success','Location Updated Successfully');
        } catch (Exception $e) {
            //dd("Exception", $e);
            return back()->with('flash_error', 'Location Not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\car  $providercar
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Location::find($id)->delete();
            return back()->with('message', 'Location deleted successfully');
        } 
        catch (Exception $e) {
            return back()->with('flash_error', 'Location Not Found');
        }
    }
}
