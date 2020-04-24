<?php
namespace App\Objects;

use Illuminate\Database\Eloquent\SoftDeletes;

class MediaImageSize extends IDB
{
    use SoftDeletes;

    protected $table = 'media_image_size';

    public function media()
    {
        return $this->belongsTo('App\Objects\Media', 'id_media');
    }
}
