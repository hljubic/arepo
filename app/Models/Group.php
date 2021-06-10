<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin IdeHelperGroup
 */
class Group extends ResourceModel
{
    // use SoftDeletes; TODO (custom) - ovo otkomentirat

    protected $table = 'groups';

    protected $fillable = [
        'name', 'label', 'description', 'department', 'members_count', 'school_id', 'school_year_id'
    ];

    protected static $default_widths = ['xs' => 12, 'sm' => 12, 'md' => 12, 'lg' => 12, 'xl' => 12];

    protected static $title = 'Grupe';

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'name',
            'rules' => 'required|min:3',
            'icon' => 'mdi-home',
        ],
        [
            'label' => 'Škola',
            'field' => 'school_id',
            'rules' => 'required|integer|exists:schools,id',
            'relation' => [
                'name' => 'school',
                'expose' => 'name',
                'method' => 'sync',
            ]
        ],
        [
            'label' => 'Školska godina',
            'field' => 'school_year_id',
            'rules' => 'required|integer|exists:school_years,id',
            'relation' => [
                'name' => 'schoolYear',
                'expose' => 'name',
                'method' => 'sync',
            ]
        ],
        [
            'label' => 'Odjeljenje',
            'column' => 'department',
            'rules' => '',
            'excluded' => 'add'
        ],
        [
            'label' => 'Opis',
            'column' => 'description',
            'rules' => 'required|min:3',
        ],
        [
            'label' => 'Broj članova',
            'column' => 'members_count',
            'rules' => '',
            'excluded' => 'add'
        ],
        [
            'label' => 'Članovi',
            'field' => 'users',
            'rules' => '',
            'multiple' => true,
            'relation' => [
                'name' => 'users',
                'expose' => 'first_name',
                'method' => 'sync',
                'pivot' => true,
            ],
            'excluded' => 'index'
        ],
    ];

    protected static $action_buttons = [
        'table' => [
            // ['icon' => 'mdi-account-supervisor-circle', 'callback' => 'create-group'],
            ['icon' => 'mdi-pencil', 'callback' => 'edit-item'],
            ['icon' => 'mdi-delete', 'callback' => 'delete-item'],
        ],
        'heading' => [
            ['icon' => 'mdi-database-import-outline', 'tooltip' => 'Uvezi iz matice', 'callback' => 'import-department',],
        ],
    ];

    public static function index(Request $request, &$query)
    {
        $query->orderByDesc('id');
        switch(Auth::user()->role_id) {
            case 1:
                break;
            case 2: case 3:
            $query->where('school_id', Auth::user()->school_id);
            break;
            default:
                abort(400, 'Groups index');
        }
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->using(GroupUser::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
