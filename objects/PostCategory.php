<?php
namespace App\Objects;

use App\Objects\IDB;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends IDB
{
    use SoftDeletes;

    protected $table = 'post_category';

    public function posts()
    {
        return $this->belongsToMany('App\Objects\Post', 'post_category_assigned', 'id_category', 'id_post');
    }
}
