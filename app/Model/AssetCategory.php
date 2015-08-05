<?php

namespace App\Model;

class AssetCategory extends \Illuminate\Database\Eloquent\Model {

    protected $fillable = ['id', 'name'];

    public function parentCategory() {
        return $this->belongsTo('App\Model\AssetCategory', 'parent_id');
    }

    public function childCategories() {
        return $this->hasMany('App\Model\AssetCategory', 'parent_id');
    }

    public function getChildren() {
        return $this->childCategories()->orderBy('name', 'ASC')->get();
    }

}
