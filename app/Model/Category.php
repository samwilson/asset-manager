<?php

namespace App\Model;

class Category extends \Illuminate\Database\Eloquent\Model {

    protected $fillable = ['id', 'name'];

    public function parentCategory() {
        return $this->belongsTo('App\Model\Category', 'parent_id');
    }

    public function childCategories() {
        return $this->hasMany('App\Model\Category', 'parent_id');
    }

    public function getChildren() {
        return $this->childCategories()->orderBy('name', 'ASC')->get();
    }

}
