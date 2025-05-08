<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscortModel extends Model
{
    use CrudTrait;
    use HasFactory;

    // app/Models/EscortModel.php

protected $fillable = [
    'name',
    'age',
    'gender',
    'ethnicity',
    'height_cm',
    'weight_kg',
    'bust_size',
    'hair_color',
    'eye_color',
    'language_spoken',
    'city',
    'country',
    'phone_number',
    'email',
    'services_offered',
    'price_hourly',
    'price_overnight',
    'available',
    'main_image',
    'gallery_images',
    'description',
];

}
