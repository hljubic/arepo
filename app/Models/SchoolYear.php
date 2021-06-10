<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperSchoolYear
 */
class SchoolYear extends ResourceModel
{
    // use SoftDeletes; TODO (custom) - ovo otkomentirat

    protected $table = 'school_years';

    protected $fillable = [
        'name', 'starts_at', 'ends_at'
    ];

    protected static $title = 'Školske godine';

    protected static $default_widths = ['xs' => 12, 'sm' => 12, 'md' => 12, 'lg' => 12, 'xl' => 12];

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'name',
            'rules' => 'required|min:3',
            'icon' => 'mdi-home',
        ],
        [
            'label' => 'Početak godine',
            'column' => 'starts_at',
            'icon' => 'mdi-home',
            'rules' => 'date',
            'type' => 'date'
        ],
        [
            'label' => 'Kraj godine',
            'column' => 'ends_at',
            'rules' => 'date',
            'icon' => 'mdi-home',
            'type' => 'date'
        ],
    ];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
