<?php
namespace App\Objects;

use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends IDB
{
    use SoftDeletes;

    protected $table = 'post';

    public function post_category()
    {
        return $this->belongsToMany('App\Objects\PostCategory', 'post_category_assign', 'id_post', 'id_category');
    }

    public function image()
    {
        return $this->belongsTo('App\Objects\Media', 'id_media');
    }

    public function author()
    {
        return $this->belongsTo('App\Objects\AdminUser', 'id_author');
    }
}
