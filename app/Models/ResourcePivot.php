<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

/**
 * @mixin IdeHelperResourcePivot
 */
class ResourcePivot extends ResourceModel
{
    use AsPivot;

    public $incrementing = true;
}
