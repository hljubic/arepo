<?php

namespace App\Models;

use App\Traits\HasHrEduId;
use App\Traits\SecondaryFieldsUser;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as IMustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin IdeHelperUser
 */
class User extends ResourceModel implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    IMustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasHrEduId;
    use SoftDeletes;
    use \Illuminate\Auth\Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use SecondaryFieldsUser;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'uid', 'email_verified_at',
        'username', 'password', 'oib', 'basic_institution_connection_id',
        'basic_connection_expiration_date', 'group_affiliation',
        'phone_number', 'mobile_phone_number', 'birth_date',
        'sex', 'professional_status_id', 'occupation_id', 'locked',
        'science_field_id', 'student_type_id', 'institution_position_id',
        'institution_role', 'institution_job_type', 'organisational_unit',
        'room_number', 'postal_code', 'street_house_number',
        'home_postal_address', 'home_phone_number', 'desktop_device',
        'privacy_label', 'role_id', 'school_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    protected static $title = 'Korisnici';

    // TODO skontat kako handleati unique rule kada se radi PUT request
    protected static $form = [
        [
            'label' => 'Ime',
            'column' => 'first_name',
            'rules' => 'required|array',
            'hasSec' => true
        ],
        [
            'label' => 'Prezime',
            'column' => 'last_name',
            'rules' => 'required|array',
            'hasSec' => true
        ],
        [
            'label' => 'OIB',
            'column' => 'oib',
            'rules' => 'required|string',
        ],
        [
            'label' => 'Korisničko ime',
            'column' => 'username',
            'rules' => 'nullable|string',
            'excluded' => 'index'
        ],
//        [
//            'label' => 'Adresa e-pošte',
//            'column' => 'email',
//            'rules' => 'required|email'
//        ],
        [
            'label' => 'Zaključan',
            'column' => 'locked',
            'excluded' => 'index,add',
            'rules' => '',
        ],
        [
            'label' => 'Temeljna povezanost',
            'field' => 'basic_institution_connection_id',
            'rules' => 'required|integer|exists:institution_connections,id',
            'relation' => [
                'name' => 'basicInstitutionConnection',
                'expose' => 'title',
                'method' => 'sync',
            ]
        ],
        [
            'label' => 'Povezanost s ustanovom',
            'field' => 'institution_connection_id',
            'rules' => '',
            'excluded' => 'index',
            'multiple' => true,
            'relation' => [
                'name' => 'institutionConnections',
                'expose' => 'title',
                'method' => 'sync',
                'pivot' => true,
            ]
        ],
        [
            'label' => 'Datum isteka TP',
            'column' => 'basic_connection_expiration_date',
            'rules' => 'required|date',
            'type' => 'date'
        ],
        [
            'label' => 'Pripadnost grupi',
            'column' => 'group_affiliation',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Lokalni telefonski broj',
            'column' => 'phone_number',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Broj mobilnog telefona',
            'column' => 'mobile_phone_number',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'type' => 'date',
            'hasSec' => true
        ],
        [
            'label' => 'Datum rođenja',
            'column' => 'birth_date',
            'rules' => 'nullable|date',
            'excluded' => 'index'
        ],
        [
            'label' => 'Spol',
            'column' => 'sex',
            'rules' => 'nullable|string',
            'excluded' => 'index'
        ],
        [
            'label' => 'Stručni status',
            'column' => 'professional_status_id',
            'rules' => 'nullable|integer|exists:professional_statuses,id',
            'excluded' => 'index',
            'relation' => [
                'name' => 'professionalStatus',
                'expose' => 'title',
                'method' => 'sync',
            ]
        ],
        [
            'label' => 'Zvanje',
            'column' => 'occupation_id',
            'rules' => 'nullable|integer|exists:occupations,id',
            'excluded' => 'index',
            'relation' => [
                'name' => 'occupation',
                'expose' => 'title',
                'method' => 'sync',
            ]
        ],
        [
            'label' => 'Područje znanosti',
            'column' => 'science_field_id',
            'rules' => 'nullable|integer|exists:science_fields,id',
            'excluded' => 'index',
            'relation' => [
                'name' => 'scienceField',
                'expose' => 'title',
                'method' => 'sync',
            ]
        ],
        [
            'label' => 'Vrsta studenta',
            'column' => 'student_type_id',
            'rules' => 'nullable|integer|exists:student_types,id',
            'excluded' => 'index',
            'relation' => [
                'name' => 'studentType',
                'expose' => 'title',
                'method' => 'sync',
            ]
        ],
        [
            'label' => 'Položaj u ustanovi',
            'column' => 'institution_position_id',
            'rules' => 'nullable|integer|exists:institution_positions,id',
            'excluded' => 'index',
            'relation' => [
                'name' => 'institutionPosition',
                'expose' => 'title',
                'method' => 'sync',
            ]
        ],
        [
            'label' => 'Uloga u ustanovi',
            'column' => 'institution_role',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Vrsta posla u ustanovi',
            'column' => 'institution_job_type',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Organizacijska jedinica',
            'column' => 'organisational_unit',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Broj sobe',
            'column' => 'room_number',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Poštanski broj',
            'column' => 'postal_code',
            'rules' => 'nullable|string',
            'excluded' => 'index',
        ],
        [
            'label' => 'Ulica i kućni broj',
            'column' => 'street_house_number',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Kućna poštanska adresa',
            'column' => 'home_postal_address',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Kućni telefonski broj',
            'column' => 'home_phone_number',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Desktop uređaj',
            'column' => 'desktop_device',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Oznaka privatnosti',
            'column' => 'privacy_label',
            'rules' => 'nullable|array',
            'excluded' => 'index',
            'hasSec' => true
        ],
        [
            'label' => 'Uloga',
            'column' => 'role_id',
            'rules' => 'required|integer',
            'excluded' => 'index,add,edit',
        ],
        [
            'label' => 'Škola',
            'column' => 'school_id',
            'rules' => 'nullable|integer',
            'excluded' => 'index,add,edit',
        ],

        // Sta je ovo divider ? - https://translate.google.com/?sl=en&tl=hr&text=divider%0A&op=translate
//        [
//            'column' => 'Divider',
//            'type' => 'divider',
//            'excluded' => 'index',
//            'width' => ['xs' => 12, 'md' => 12, 'lg' => 12, 'xl' => 12],
//        ],
    ];

    protected static $action_buttons = [
        'table' => [
            ['icon' => 'mdi-shield-account', 'callback' => 'print-item'],
            ['icon' => 'mdi-pencil', 'callback' => 'edit-item'],
            ['icon' => 'mdi-delete', 'callback' => 'delete-item'],
        ],
        'heading' => [
            ['icon' => 'mdi-archive-arrow-up-outline', 'tooltip' => 'Grupni unos (CSV)', 'callback' => 'batch-import',],
            ['icon' => 'mdi-account-edit-outline', 'tooltip' => 'Promjena zajedničkih podataka', 'callback' => 'change-group-data',],
            ['icon' => 'mdi-lock-reset', 'tooltip' => 'Resetiraj lozinke', 'callback' => 'reset-group-passwords',],
        ],
    ];

    public function setPasswordAttribute($value, $ignore = false)
    {
        if ($ignore)
            $this->attributes['password'] = $value;
        else
            $this->attributes['password'] = Hash::make($value);
    }

//    public static function getLockedAttribute($value)
//    {
//        return $value ? 'Da' : 'Ne';
//    }

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
                abort(400, 'Users index');
        }
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function basicInstitutionConnection()
    {
        return $this->belongsTo(InstitutionConnection::class);
    }

    public function institutionConnections()
    {
        return $this->belongsToMany(InstitutionConnection::class, 'user_institution_connection')
            ->using(UserInstitutionConnection::class);
    }

    public function professionalStatus()
    {
        return $this->belongsTo(ProfessionalStatus::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function scienceField()
    {
        return $this->belongsTo(ScienceField::class);
    }

    public function studentType()
    {
        return $this->belongsTo(StudentType::class);
    }

    public function institutionPosition()
    {
        return $this->belongsTo(InstitutionPosition::class);
    }

//    public function scopeIncludeAllFields($query)
//    {
//        $query->join('institution_connections', 'users.basic_connection_institution_id', '=', 'institution_connections.id');
//    }

    public function changeLockStatus(bool $lock): bool
    {
        if ($lock === (bool)$this->locked)
            return false;

        if ($lock) {
            $this->locked = true;
            $this->setPasswordAttribute($this->password .= '*', true);
        } else {
            $this->locked = false;
            $this->password = Str::replaceLast('*', '', $this->password);
        }
        $this->save();
        return true;
    }

    public function institutionJobTypes(): array
    {
        return [
            'Adminstrativno osoblje',
            'Podrška',
            'ICT'
        ];
    }
}
