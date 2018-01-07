<?php

namespace App\Http\Controllers\Resource;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CarCategory;
use Setting;

class CarResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cars = CarCategory::orderBy('created_at' , 'desc')->get();
        return view('admin.car.index', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.car.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@hepto.com');
        }
        
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        try{

            CarCategory::create($request->all());
            return redirect()->route('admin.car.index')->with('flash_success','Car Saved Successfully');

        } 

        catch (Exception $e) {
            return back()->with('flash_error', 'car Not Found');
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
        try {
            return CarCategory::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $e;
        }
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
            $car = CarCategory::findOrFail($id);
            return view('admin.car.edit',compact('car'));
        } catch (ModelNotFoundException $e) {
            return $e;
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
            'name' => 'required|max:255'
        ]);

        try {
            CarCategory::where('id',$id)->update([
                    'name' => $request->name
                ]);
            return redirect()->route('admin.car.index')->with('flash_success', 'Car Updated Successfully');    
        } 

        catch (Exception $e) {
            return back()->with('flash_error', 'Car Not Found');
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
         if(Setting::get('demo_mode', 0) == 1) {
            return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@hepto.com');
        }
        try {
            CarCategory::find($id)->delete();
            return back()->with('message', 'Car deleted successfully');
        } 
        catch (Exception $e) {
            return back()->with('flash_error', 'Car Not Found');
        }
    }
}
