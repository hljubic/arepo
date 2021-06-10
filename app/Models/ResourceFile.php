<?php

namespace App\Models;

use App\Traits\AttachesUuid;
use Wildside\Userstamps\Userstamps;

/**
 * @mixin IdeHelperResourceFile
 */
class ResourceFile extends ResourceModel
{
    use Userstamps, AttachesUuid;

    protected $table = 'files';

    protected $casts = [
        'attributes' => 'array',
    ];

    protected $fillable = [
        'name', 'attributes',
        'filepath', 'mimetype'
    ];
}
