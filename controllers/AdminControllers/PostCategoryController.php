<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminController;
use App\Objects\PostCategory;

class PostCategoryController extends AdminController
{

  public function initListing()
  {
      $this->initProcessFilter();

      $post_category = PostCategory::select('id', 'title', 'status')
      ->orderBy('id', 'desc');

      if ($this->filter) {
          $post_category->where($this->filter_search);
      }

      $this->obj = $post_category->paginate($this->paginate);

      $keys = [
            'id' => [
                'text' => 'ID',
                'filter' => true
            ],
            'title' => [
                'text' => 'Title',
                'filter' => true
            ],
            'status' => [
                'text' => 'Status',
                'filter' => false,
                'switch' => true
            ]
      ];

      return array(
          'obj' => $this->obj,
          'keys' => $keys
      );
  }

  public function initContentCreate($id = null)
  {
    $this->obj = new PostCategory;
    if ($id) {
      $this->obj = $this->obj->find($id);
    }

    return array(
      'post_category' => $this->obj
    );
  }

  public function initProcessCreate($id = null)
  {

    $this->obj = new PostCategory;

    if ($id) {
      $this->obj = $this->obj->find($id);
    }

    if (request()->input('status')) {
      $status = 1;
    } else {
      $status = 0;
    }

    $data = $this->obj;
    $data->title = request()->input('name');
    $data->url = \Str::slug(request()->input('url'));
    $data->status = $status;
    $data->meta_title = request()->input('meta_title');
    $data->meta_description = request()->input('meta_description');
    $data->save();

    if (!$id) {
      return json('redirect', 'edit/' . $data->id);
    }

    return json('success', t('Post Category updated'));
  }

  public function initProcessDelete($id = null)
  { }
}
