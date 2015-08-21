<?php

namespace App\Model;

abstract class Model extends \Illuminate\Database\Eloquent\Model {

    /**
     * @link https://github.com/deringer/laravel-nullable-fields
     */
    use \Iatstuti\Database\Support\NullableFields;
}
