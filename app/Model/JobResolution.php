<?php

namespace App\Model;

class JobResolution extends Model
{

    const SUCCEEDED = 1;
    const FAILED = 2;
    const CANCELLED = 3;

    public $fillable = ['id', 'name', 'type',];
    protected $attributes = ['type' => self::SUCCEEDED,];
}
