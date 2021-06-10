<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperOccupation
 */
class Occupation extends ResourceModel
{
    protected static $title = 'Znanstvena zvanja';

    protected $fillable = ['title'];

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'title',
            'rules' => 'required|string'
        ]
    ];}
