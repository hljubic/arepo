<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin IdeHelperGroupAliasMember
 */
class GroupAliasMember extends ResourceModel
{
    protected $table = 'group_alias_members';

    protected $fillable = [
        'group_alias_id', 'email'
    ];

    protected static $title = 'Članovi grupnog aliasa';

    protected static $form = [
        /*
        [
            'field' => 'group_alias_id',
            'rules' => 'required',
            'width' => ['xs' => 12, 'md' => 6, 'lg' => 4, 'xl' => 6],
            'relation' => [
                'name' => 'group_alias',
                'expose' => 'email',
                'method' => 'sync',
            ],
        ],
        */
    ];
}
