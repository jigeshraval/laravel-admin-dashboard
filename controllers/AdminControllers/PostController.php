<?php
namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminController;
use App\Objects\Post;
use App\Objects\PostCategory;

class PostController extends AdminController
{
    public function initListing()
    {
        $this->initProcessFilter();

        $post = Post::select('id', 'title', 'published')
        ->orderBy('id', 'desc');

        if ($this->filter) {
            $post->where($this->filter_search);
        }

        $this->obj = $post->paginate($this->paginate);

        $keys = [
              'id' => [
                  'text' => 'ID',
                  'filter' => true
              ],
              'title' => [
                  'text' => 'Title',
                  'filter' => true
              ],
              'published' => [
                  'text' => 'Published',
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
        $this->obj = new Post;
        $this->obj->selected_category = array();

        if ($id) {
            $this->obj = $this->obj->find($id);
            $this->obj->selected_category = $this->obj->post_category()->pluck('post_category.id')->toArray();

            if ($this->obj->image) {
                $this->obj->image->url = $this->obj->image->retrieve(250, 250, false);
            }
        }

        $this->obj->category = PostCategory::select('id', 'title as name')
        ->orderBy('id', 'desc')
        ->get();

        return array(
          'post' => $this->obj
        );

    }

    public function initProcessCreate($id = null)
    {
        $this->obj = new Post;

        if ($id) {
          $this->obj = $this->obj->find($id);
        }

        if (request()->input('display_home')) {
          $display_home = 1;
        } else {
          $display_home = 0;
        }

        if (request()->input('main_post')) {
          $main_post = 1;
        } else {
          $main_post = 0;
        }

        $data = $this->obj;
        $data->title = ucwords(request()->input('title'));
        $data->url = request()->input('url');
        $data->content = protectedString(request()->input('content'));
        $data->id_author = \Auth::guard('admin_user')->user()->id;
        $data->meta_title = request()->input('meta_title');
        $data->meta_description = request()->input('meta_description');
        $data->id_media = request()->input('id_media');
        $data->post_date = request()->input('post_date');
        $data->published = (bool) request()->input('published');
        $data->save();

        if (count(request()->input('category'))) {
          
            if ($id) {
              \App\Objects\PostCategoryAssigned::where('id_post', $data->id)->forceDelete();
          }

          foreach (request()->input('category') as $input) {
              $re = new \App\Objects\PostCategoryAssigned;
              $re->id_category = $input;
              $re->id_post = $data->id;
              $re->save();
          }
        }

        if (!$id) {
            return json('redirect', 'edit/' . $data->id);
        }

        return json('success', t('Post updated'));
    }

    public function initProcessDelete($id = null)
    {

    }

}
