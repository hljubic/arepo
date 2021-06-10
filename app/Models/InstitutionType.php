<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin IdeHelperInstitutionType
 */
class InstitutionType extends ResourceModel
{
    use SoftDeletes;

    protected $table = 'institution_types';

    protected $fillable = [
        'title',
    ];

    protected static $title = 'Vrste institucija';

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'title',
            'rules' => 'required|min:3',
            'icon' => 'mdi-home',
            'width' => ['lg' => 12]
        ],
    ];
}
