<?php
namespace App\Traits;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Generate uuid
 */
trait Uuid
{
    public static function boot(){
        parent::boot();
        static::creating(function($obj){
            $obj->id = RamseyUuid::uuid4();
        });
    }
}
