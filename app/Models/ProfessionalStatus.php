<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperProfessionalStatus
 */
class ProfessionalStatus extends ResourceModel
{
    protected static $title = 'Stručni statusi';

    protected $fillable = ['title'];

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'title',
            'rules' => 'required|string'
        ]
    ];
}
