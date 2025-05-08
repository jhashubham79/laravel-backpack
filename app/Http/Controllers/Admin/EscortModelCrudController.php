<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EscortModelRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EscortModelCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EscortModelCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    

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

    // Tab 1: Personal Info
    CRUD::field('name')->tab('Personal Info');
    CRUD::field('email')->tab('Personal Info');
    CRUD::field('phone_number')->tab('Personal Info');

    // Tab 2: Body Stats
    CRUD::field('height_cm')->tab('Body Stats');
    CRUD::field('weight_kg')->tab('Body Stats');
    CRUD::field('bust_size')->tab('Body Stats');

    // Tab 3: Other Details
    CRUD::field('age')->tab('Other Details');
    CRUD::field('gender')->type('select_from_array')->options([
        'female' => 'Female',
        'male' => 'Male',
        'transgender' => 'Transgender',
    ])->tab('Other Details');

    CRUD::field('ethnicity')->tab('Other Details');
    CRUD::field('hair_color')->tab('Other Details');
    CRUD::field('eye_color')->tab('Other Details');
    CRUD::field('language_spoken')->tab('Other Details');
    CRUD::field('city')->tab('Other Details');
    CRUD::field('country')->tab('Other Details');
    CRUD::field('services_offered')->type('textarea')->tab('Other Details');
    CRUD::field('price_hourly')->tab('Other Details');
    CRUD::field('price_overnight')->tab('Other Details');
    CRUD::field('available')->type('boolean')->tab('Other Details');
    CRUD::field('main_image')->type('upload')->withFiles(['disk' => 'public'])->tab('Other Details');
    CRUD::field('gallery_images')->type('textarea')->hint('Comma-separated image URLs or JSON array')->tab('Gallery');
    CRUD::field('description')->type('summernote')->tab('Other Details');
}


protected function setupUpdateOperation()
{
    $this->setupCreateOperation();
}

    
}
