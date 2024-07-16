<?php

namespace App\Http\Controllers;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\UpdateServiceActivationRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;


class ServiceController extends Controller
{
    public function index()
    {
       return ServiceResource::collection(Service::paginate(10));
    }
    public function store(CreateServiceRequest $request)
    {
        $data = $request->validated();
        // $data['company_id'] = Auth::user()->id;
        $data['discounted_packages'] = $data['discounted_packages'] ?? 0;  
        $data['activation'] = $data['activation'] ?? 1;  
        foreach (config('app.locales') as $locale) {
            $data['name'][$locale] = $data[$locale . '_name'];
            $data['description'][$locale] = $data[$locale . '_description'];
        }
        $service = Service::create($data);
        foreach($data['images'] as $image){
            $service->images()->create(['url' => $image]);
        }
        return success(new ServiceResource($service));        
    }
    public function show(Service $service)
    {
        return success(new ServiceResource($service));
    }
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        foreach (config('app.locales') as $locale) {
            if(isset($data[$locale . '_name']))
                $data['name'][$locale] = $data[$locale . '_name'];
            if(isset($data[$locale . '_description']))
                $data['description'][$locale] = $data[$locale . '_description'];
        }
        if(isset($data['images'])){
            foreach($data['images'] as $image)
                $service->images()->create(['url' => $image]);
        }
        if(isset($data['remove_images']) && count($service->images) > 1)
            $service->images()->whereIn('id', $data['remove_images'])->delete();
        $service->update($data);
        return success(new ServiceResource($service));
    }
    public function updateActivation(UpdateServiceActivationRequest $request,Service $service)
    {
        $data = $request->validated();
        // if($service->company_id == Auth::user()->id)

        // delete this condition
        if($service->category_id == 5){
            $service->update($data);
            return success(new ServiceResource($service));
        }
        else{
            return error('Invalid service id');
        }
    }
    public function destroy (Service $service)
    {
        if($service->company_id == Auth::user()->id){
            $service->delete();
            return success();
        }
        else{
            return error('Invalid service id');
        }
    }

}
