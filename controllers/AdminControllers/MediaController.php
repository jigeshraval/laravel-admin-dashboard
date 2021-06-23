<?php

namespace App\Http\Controllers\AdminControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Objects\Media;

class MediaController extends AdminController
{
    public function initProcessUpload()
    {
        
        collect(
            request()->file('files')
        )->each(function ($file) {

            $media = new Media;
            $media->store($file);
            
        });

    }

    public function initProcessListing()
    {
        $type = request()->input('type');
        $media = new Media;

        if ($type == 'pdf') {
            $type = 'application';
        }

        $list = $media
        ->orderBy('id', 'desc')
        ->where('type', $type);

        if ($type == 'application') {
            $list->where('format', 'pdf');
        }

        $list = $list->paginate(24);

        if (count($list)) {
            foreach ($list as $key => $media) {
                $file = $media->retrieve(250, 250, false);
                if ($file) {
                    $list[$key]['url'] = $file;
                }
            }
        }

        return $list;
    }
}
