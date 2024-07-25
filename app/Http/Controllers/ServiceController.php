<?php

namespace App\Http\Controllers;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\UpdateServiceActivationRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Stichoza\GoogleTranslate\GoogleTranslate;


class ServiceController extends Controller
{
    public function index()
    {
       return ServiceResource::collection(Service::paginate(10));
    }
    public function store(CreateServiceRequest $request)
    {
        $data = $request->validated();
        $currentLocale = app()->getLocale();
        $targetLocale = ($currentLocale === 'en') ? 'ar' : 'en';

        $lang = new GoogleTranslate($currentLocale);
        $lang->setSource($currentLocale)->setTarget($targetLocale);

        $data['name'] = [
            $currentLocale => $data['name'],
            $targetLocale => $lang->translate($data['name'])
        ];
        $data['description'] = [
            $currentLocale => $data['description'],
            $targetLocale => $lang->translate($data['description'])
        ];

        // $data['company_id'] = Auth::user()->id;
        $data['discounted_packages'] = $data['discounted_packages'] ?? 0;
        $data['activation'] = $data['activation'] ?? 1;
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
        $currentLocale = app()->getLocale();
        $targetLocale = ($currentLocale === 'en') ? 'ar' : 'en';

        $lang = new GoogleTranslate($currentLocale);
        $lang->setSource($currentLocale)->setTarget($targetLocale);
            if(isset($data['name']))
                $data['name'] = [
                    $currentLocale => $data['name'],
                    $targetLocale => $lang->translate($data['name'])
                ];

            if(isset($data['description']))
            $data['description'] = [
                $currentLocale => $data['description'],
                $targetLocale => $lang->translate($data['description'])
            ];

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
    public function destroy(Service $service)
    {
        // set this condition when prepared
        // if($service->company_id == Auth::user()->id){
            $service->images()->delete();
            $service->delete();
            return success();
        // }
        // else{
        //     return error('Invalid service id');
        // }
    }

    public function indexByCategory(Category $category)
    {
        $services = $category->services()->withAvg('reviews','rete')->paginate(10);
        return success(ServiceResource::collection($services));
    }

}
