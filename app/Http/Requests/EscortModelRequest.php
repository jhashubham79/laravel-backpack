<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EscortModelRequest extends FormRequest
{
    public function setup()
    {
        CRUD::setModel(\App\Models\EscortModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/escort-model');
        CRUD::setEntityNameStrings('escort model', 'escort models');
    }
    
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('age');
        CRUD::column('gender');
        CRUD::column('city');
        CRUD::column('price_hourly');
        CRUD::column('main_image')->type('image');
        CRUD::column('available')->type('boolean');
    }
    
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|string|max:100',
            'age' => 'required|integer',
            'gender' => 'required|in:female,male,transgender',
            'price_hourly' => 'nullable|numeric',
            'main_image' => 'nullable|image',
        ]);
    
        CRUD::field('name');
        CRUD::field('age');
        CRUD::field('gender')->type('select_from_array')->options([
            'female' => 'Female',
            'male' => 'Male',
            'transgender' => 'Transgender',
        ]);
        CRUD::field('ethnicity');
        CRUD::field('height_cm');
        CRUD::field('weight_kg');
        CRUD::field('bust_size');
        CRUD::field('hair_color');
        CRUD::field('eye_color');
        CRUD::field('language_spoken');
        CRUD::field('city');
        CRUD::field('country');
        CRUD::field('phone_number');
        CRUD::field('email');
        CRUD::field('services_offered')->type('textarea');
        CRUD::field('price_hourly');
        CRUD::field('price_overnight');
        CRUD::field('available')->type('boolean');
        CRUD::field('main_image')->type('upload')->withFiles(['disk' => 'public']);
        CRUD::field('gallery_images')->type('textarea')->hint('Comma-separated image URLs or JSON array');
        CRUD::field('description')->type('ckeditor');
    }
    
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    
}
