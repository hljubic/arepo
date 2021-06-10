<?php

namespace App\Models;

use App\Helpers\Constants;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @mixin IdeHelperExampleModel
 */
class ExampleModel extends ResourceModel
{
//    use CustomDateFormat, SoftDeletes;
//    use HasRelationships;

    protected $table = 'schools';

    protected $fillable = [
        'name', 'address', 'type', 'lat', 'lng', 'contact_phone', 'contact_person',
        'contact_it', 'contact_email', 'township_id', 'headmaster_id'
    ];

    protected static $title = 'Škole';
    protected static $default_widths = ['xs' => 6, 'sm' => 12, 'md' => 6, 'lg' => 6, 'xl' => 6];


    protected static $action_buttons = [
        'table' => [
            ['icon' => 'mdi-magnify', 'callback' => 'read-item'],
            ['icon' => 'mdi-pencil', 'callback' => 'edit-item'],
            ['icon' => 'mdi-delete', 'callback' => 'delete-item'],
        ],
        'heading' => [
            ['icon' => 'mdi-cloud-print-outline', 'tooltip' => 'Ispis', 'callback' => 'print-page',],
        ],
    ];

    protected static $form = [
        [
            'label' => 'Ime',
            'column' => 'name',
            'rules' => 'required|min:3',
            'icon' => 'mdi-pencil',
            'width' => ['lg' => 12]
        ],
        [
            'label' => 'Kontakt (email)',
            'column' => 'contact_email',
            'rules' => 'email',
        ],
        [
            'column' => 'contact_email',
            'rules' => 'email',
        ],
        [
            'column' => 'contact_it',
            'excluded' => 'index', // index je tablica
            'icon' => 'mdi-home',
        ],
        [
            'column' => 'address',
            'rules' => 'required',
            'excluded' => 'add', // add je dodavanje, edit je uređivanje, read je pregled
        ],
        [
            'column' => 'type',
            'items' => [
                ['value' => 'primary', 'text' => 'Osnovna škola'],
                ['value' => 'high', 'text' => 'Srednja škola'],
                ['value' => 'preschool', 'text' => 'Vrtić'],
            ],
            'rules' => 'required',
        ],
        [
            'field' => 'township_id',
            'rules' => 'required',
            'icon' => 'mdi-delete',
            'width' => ['xs' => 12, 'md' => 6, 'lg' => 4, 'xl' => 6],
            'relation' => [
                'name' => 'township',
                'expose' => 'title',
                'method' => 'sync',
            ],
            // 'multiple' => true,
        ],
        /*
        [
            'column' => 'school_date',
            'type' => 'date',
            'rules' => 'required',
        ],
        [
            'column' => 'image',
            'type' => 'file',
            'width' => ['xs' => 12, 'md' => 12, 'lg' => 12, 'xl' => 6]
        ],
        [
            'column' => 'gallery',
            'type' => 'files',
            'height' => '480px',
        ],
        [
            'column' => 'description',
            'type' => 'editor',
            'rules' => 'required',
        ],
*/
    ];


    public function getTypeAttribute($value)
    {
        return $value == 'high' ? 'Srednja škola' : 'Osnovna škola';
    }

    public static function index(Request $request, &$query)
    {
        // $query->where('id', 1);
        /*
            $currentRole = Auth::user()->getCurrentRole();

            switch ($currentRole->name) {
                case Constants::SUPERADMIN:
                    break;
                case Constants::CANTONCOORDINATOR:
                case Constants::TOWNSHIPCOORDINATOR:
                    $schoolIds = Auth::user()->getOwnedSchools()->pluck('value');
                    $query->whereIn('schools.id', $schoolIds);
                    break;
                case Constants::COORDINATOR:
                case Constants::DIRECTOR:
                    $query->where('id', $currentRole->organisation_id);
                    break;
                default:
                    abort(403, 'Index Schools');
            }
        */
    }

    public function userRoles()
    {
        return $this->morphMany(ModelHasRole::class, 'organisation');
    }

    public function coordinators()
    {
        return $this->userRoles()->where('role_id', 3)
            ->join('users', 'model_has_roles.model_id', '=', 'users.id');
    }

    public function departments()
    {
        return $this->hasManyThrough(
            Department::class,
            SchoolEducationalField::class,
            'school_id', // strani kljuc na medutablici
            'school_educational_field_id', // strani ključ na krajnjoj tablici - departments
            'id', //
            'id'
        );
    }

    public function township()
    {
        return $this->belongsTo(Township::class);
    }

    public function canton()
    {
        return $this->hasOneThrough(
            Canton::class,
            Township::class,
            'id',
            'id',
            'township_id',
            'canton_id');
    }

    public function headmaster()
    {
        return $this->belongsTo(User::class);
    }

    public function schoolEducationalFields()
    {
        return $this->hasMany(SchoolEducationalField::class);
    }

    public function educationalFields()
    {
        return $this->belongsToMany(EducationalField::class, 'school_educational_field')
            ->using(SchoolEducationalField::class)
            ->withPivot(['id', 'label'])
            ->withTimestamps();
    }

    public function availableSchoolPlans()
    {
        return $this->hasManyDeepFromRelations($this->educationalFields(), (new EducationalField())->schoolPlans());
    }
}
