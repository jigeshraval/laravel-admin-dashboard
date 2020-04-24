<?php
namespace App\Objects;

use App\Objects\IDB;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategoryAssigned extends IDB
{
    use SoftDeletes;

    protected $table = 'post_category_assign';

    public function post()
    {
        return $this->belongsTo('App\Objects\Post', 'id_post');
    }

    public function category()
    {
        return $this->belongsTo('App\Objects\PostCategory', 'id_category');
    }
}
