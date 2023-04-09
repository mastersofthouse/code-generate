<?php

namespace SoftHouse\CodeGenerate\traits;

use SoftHouse\CodeGenerate\CodeGenerate;

trait hasCodeGenerate
{
    protected static function boothasCodeGenerate(){
        static::creating(function ($model) {
            $model->code = CodeGenerate::generate($model);
        });

        static::updating(function ($model) {
            $model->code = $model->getOriginal('code');
        });
    }
}
