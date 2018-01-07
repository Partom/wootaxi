<?php

namespace App\Http\Controllers\Resource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\ServiceType;
use App\ServiceCarBrand;
use App\CarCategory;

class ServiceCarBrandResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $service)
    {
        try {
            $ServiceCarBrand = ServiceCarBrand::where('service_type_id',$service)->with('service_type','cars')->get();
            $Service = ServiceType::find($service);
            return view('admin.service.brand.index', compact('ServiceCarBrand','Service'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.service.brand.index',$service);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($service)
    {
        $CarCategory = CarCategory::all();

        $Service = ServiceType::find($service);
        return view('admin.service.brand.create', compact('CarCategory','Service'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $service)
    {
        $this->validate($request, [
                'service_brand' => 'required|exists:car_categories,id'
            ]);
        

        try { 
            ServiceCarBrand::create([
                    'service_type_id' => $request->service,
                    'car_categories_id' => $request->service_brand
                ]);

        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.service.brand',$service);
        }
        return redirect()->route('admin.service.brand.index', $service)->with('flash_success', 'Service type car brand updated successfully!');
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
    public function edit($service, $id)
    {
        try {
            $ServiceCarBrand = ServiceCarBrand::where('service_type_id', $service)
                ->findOrFail($id);

            return view('admin.service.brand.edit', compact('ServiceCarBrand'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.service.brand.index',$service);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $provider, $id)
    {
        /*try {

            $Document = ProviderDocument::where('provider_id', $provider)
                ->where('document_id', $id)
                ->firstOrFail();
            $Document->update(['status' => 'ACTIVE']);

            return redirect()
                ->route('admin.provider.document.index', $provider)
                ->with('flash_success', 'Provider document has been approved.');
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.provider.document.index', $provider)
                ->with('flash_error', 'Provider not found!');
        }*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($service, $id)
    {
        try {

            $ServiceCarBrand = ServiceCarBrand::where('service_type_id', $service)
                ->findOrFail($id);
            
            $ServiceCarBrand->delete();

            return redirect()
                ->route('admin.service.brand.index', $service)
                ->with('flash_success', 'Service cars brand has been deleted');
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.service.brand.index', $service)
                ->with('flash_error', 'Service car brand not found!');
        }
    }

}
