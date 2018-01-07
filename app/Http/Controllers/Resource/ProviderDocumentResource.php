<?php

namespace App\Http\Controllers\Resource;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SendPushNotification;

use App\Provider;
use App\ServiceType;
use App\ProviderService;
use App\ProviderDocument;
use App\Document;
use App\CarCategory;

class ProviderDocumentResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $provider)
    {
        try {
            $Provider = Provider::findOrFail($provider);
            $ProviderService = ProviderService::where('provider_id',$provider)->with(['service_type','cars'])->get();
            $ServiceTypes = ServiceType::all();
            $CarCategorys = CarCategory::all();
            return view('admin.providers.document.index', compact('Provider', 'CarCategorys','ServiceTypes','ProviderService'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $provider)
    {
        $this->validate($request, [
                'service_type' => 'required|exists:service_types,id',
                'service_number' => 'required',
                'service_model' => 'required|exists:car_categories,id',
            ]);
        

        try {
            
            $ProviderService = ProviderService::where('provider_id', $provider)->firstOrFail();
            $ProviderService->update([
                    'service_type_id' => $request->service_type,
                    'status' => 'offline',
                    'service_number' => $request->service_number,
                    'car_categories_id' => $request->service_model,
                ]);

        } catch (ModelNotFoundException $e) {
            ProviderService::create([
                    'provider_id' => $provider,
                    'service_type_id' => $request->service_type,
                    'status' => 'offline',
                    'service_number' => $request->service_number,
                    'car_categories_id' => $request->service_model,
                ]);
        }

        return redirect()->route('admin.provider.document.index', $provider)->with('flash_success', 'Provider service type updated successfully!');
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
    public function edit($provider, $id)
    {
        try {
            $Document = ProviderDocument::where('provider_id', $provider)
                ->findOrFail($id);

            return view('admin.providers.document.edit', compact('Document'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.index');
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
        try {

            $Document = ProviderDocument::where('provider_id', $provider)
                ->where('id', $id)
                ->firstOrFail();
            $Document->update(['status' => 'ACTIVE']);

            $DocumentName = Document::where('id',$Document->id)->value('name');

            $PushMsg = $DocumentName." has been approved by WooCabs admin";

            // Sending push to the provider
            (new SendPushNotification)->DriverDocumentApproved($provider,$PushMsg);

            return redirect()
                ->route('admin.provider.document.index', $provider)
                ->with('flash_success', 'Provider document has been approved.');
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.provider.document.index', $provider)
                ->with('flash_error', 'Provider not found!'.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($provider, $id)
    {
        try {

            $Document = ProviderDocument::where('provider_id', $provider)
                ->where('id', $id)
                ->firstOrFail();
            $DocumentName = Document::where('id',$Document->id)->value('name');

            $PushMsg = $DocumentName." has been unapproved by WooCabs admin";

            // Sending push to the provider
            (new SendPushNotification)->DriverDocumentUnApproved($provider,$PushMsg);

            $Document->delete();

            return redirect()
                ->route('admin.provider.document.index', $provider)
                ->with('flash_success', 'Provider document has been deleted');
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.provider.document.index', $provider)
                ->with('flash_error', 'Provider not found!');
        }
    }

    /**
     * Delete the service type of the provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function service_destroy(Request $request, $provider, $id)
    {
        try {

            $ProviderService = ProviderService::where('provider_id', $provider)->firstOrFail();
            $ProviderService->delete();

            return redirect()
                ->route('admin.provider.document.index', $provider)
                ->with('flash_success', 'Provider service has been deleted.');
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.provider.document.index', $provider)
                ->with('flash_error', 'Provider service not found!');
        }
    }
}
