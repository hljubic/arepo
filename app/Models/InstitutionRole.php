<?php

namespace App\Models;

/**
 * @mixin IdeHelperInstitutionRole
 */
class InstitutionRole extends ResourceModel
{
    protected static $title = 'Uloge u instituciji';

    protected $fillable = ['title'];

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'title',
            'rules' => 'required|string'
        ]
    ];
}
