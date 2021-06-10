<?php


namespace App\Traits;

use Illuminate\Support\Str;

trait AttachesUuid
{
    protected static function bootAttachesUuid()
    {
        static::creating(function ($model) {
            $model->uuid = (string)Str::uuid();
        });
    }
}
