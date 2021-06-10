<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin IdeHelperGroupAlias
 */
class GroupAlias extends ResourceModel
{
    use SoftDeletes;

    protected $table = 'group_aliases';

    protected $fillable = [
        'name', 'description', 'uuid'
    ];

    protected static $title = 'Grupni aliasi';

    protected static $form = [
        [
            'label' => 'Naziv',
            'column' => 'name',
            'rules' => 'required|min:3',
            'icon' => 'mdi-home',
            'width' => ['lg' => 12]
        ],
        [
            'label' => 'Opis',
            'column' => 'description',
            'rules' => 'required|min:3',
            'icon' => 'mdi-home',
            'width' => ['lg' => 12]
        ],
        [
            'label' => 'Aliasi',
            'column' => 'members',
            'rules' => '',
            'relation' => [
                'name' => 'groupAliasMembers',
                'expose' => 'email'
            ],
            'excluded' => 'index'
        ],
    ];

    public function groupAliasMembers()
    {
        return $this->hasMany(GroupAliasMember::class);
    }

    public function groupAliasMembersSyncable()
    {
        return $this->hasManySyncable(GroupAliasMember::class);
    }
}
