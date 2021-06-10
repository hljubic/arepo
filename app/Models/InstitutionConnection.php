<?php

namespace App\Models;


/**
 * @mixin IdeHelperInstitutionConnection
 */
class InstitutionConnection extends ResourceModel
{
    protected static $title = 'Povezanosti s institucijom';

    protected $fillable = ['title'];

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'title',
            'rules' => 'required|string'
        ]
    ];
}
