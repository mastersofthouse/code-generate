<?php

namespace SoftHouse\CodeGenerate\traits;

use Illuminate\Support\Str;

trait hasUuid
{
    protected static function boothasUuid(){

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string)Str::uuid(4);
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
