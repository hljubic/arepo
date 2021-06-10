<?php

namespace App\Models;

use App\Traits\SecondaryFieldsSchool;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin IdeHelperSchool
 */
class School extends ResourceModel
{
    use SoftDeletes, SecondaryFieldsSchool;

    protected $table = 'schools';

    protected $fillable = [
        'identifier', 'identifier_no', 'name', 'email',
        'location', 'postal_address', 'url',
        'uri_policy', 'postal_no', 'address',
        'telephone', 'mobile_phone', 'fax',
        'affiliation', 'institution_type_id', 'admin_id',
        'cms_status', 'site_id'
    ];

    protected static $title = 'Škole';

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'name',
            'rules' => 'required|array',
        ],
        [
            'label' => 'Identifikator',
            'column' => 'identifier',
            'rules' => 'required|string',
            'excluded' => 'add'
        ],
        [
            'label' => 'Brojčani identifikator',
            'column' => 'identifier_no',
            'rules' => 'required|string',
            'excluded' => 'add'
        ],
        [
            'label' => 'Poštanska adresa',
            'column' => 'postal_address',
            'rules' => 'required|array',
        ],
        [
            'label' => 'Mjesto',
            'column' => 'location',
            'rules' => 'required|array',
        ],
        [
            'label' => 'Poštanski broj',
            'column' => 'postal_no',
            'rules' => 'nullable|array',
            'excluded' => 'index'
        ],
        [
            'label' => 'Ulica i kućni broj',
            'column' => 'address',
            'rules' => 'nullable|array',
            'excluded' => 'index'
        ],
        [
            'label' => 'Telefonski broj',
            'column' => 'telephone',
            'rules' => 'nullable|array',
            'excluded' => 'index'
        ],
        [
            'label' => 'Fax broj',
            'column' => 'fax',
            'rules' => 'nullable|array',
            'excluded' => 'index'
        ],
        [
            'label' => 'Broj mobilnog telefona',
            'column' => 'mobile_phone',
            'rules' => 'nullable|array',
            'excluded' => 'index'
        ],
        [
            'label' => 'Elektronička adresa',
            'column' => 'email',
            'rules' => 'required|array',
        ],
        [
            'label' => 'Tip ustanove',
            'column' => 'institution_type_id',
            'rules' => 'required|integer|exists:institution_types,id',
            'relation' => [
                'name' => 'institutionType',
                'expose' => 'title'
            ]
        ],
        [
            'label' => 'Pripadnost ustanovi',
            'column' => 'affiliation',
            'rules' => 'nullable|array',
            'excluded' => 'index'
        ],
        [
            'label' => 'URL adresa ustanove',
            'column' => 'url',
            'excluded' => 'index',
            'rules' => 'required|string'
        ],
        [
            'label' => 'URL adresa politike',
            'column' => 'uri_policy',
            'rules' => 'nullable|array',
            'excluded' => 'index'
        ],
        [
            'label' => 'Adminstrator škole',
            'column' => 'admin_id',
            'rules' => 'nullable|integer|exists:users,id',
            'excluded' => 'index'
        ],
    ];

    protected static function booted()
    {
        parent::booted();
        static::saved(function(School $school) {
            $admin = User::find($school->admin_id);
            if (!$admin)
                return;
            $admin->role_id = 2;
            $admin->school_id = $school->id;
            $admin->save();
        });
    }

    public static function index(Request $request, &$query)
    {
        $query->orderByDesc('id');
        switch(Auth::user()->role_id) {
            case 1:
                break;
            case 2:
                $query->where('schools.id', Auth::user()->school_id);
                break;
            default:
                abort(400, 'Users index');
        }
    }

    public function institutionType()
    {
        return $this->belongsTo(InstitutionType::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class);
    }

    public function getDomainAttribute()
    {
        $domain = '';
        //if ($this->cms_status == 1)
        //    $domain .= 'test-';
        $domain .= $this->identifier . '.' . env('MAIN_DOMAIN');
        return $domain;
    }
}
