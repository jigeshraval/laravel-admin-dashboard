<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\AdminController;
use App\Objects\Page;
use App\Objects\Media;

class PageController extends AdminController
{
    public function initListing()
    {
        $this->initProcessFilter();

        $page = Page::select('id', 'name', 'url', 'status')
        ->orderBy('id', 'desc');

        if ($this->filter) {
            $page->where($this->filter_search);
        }

        $this->obj = $page->paginate($this->paginate);

        $keys = [
              'id' => [
                  'text' => 'ID',
                  'filter' => true
              ],
              'name' => [
                  'text' => 'Name',
                  'filter' => true
              ],
              'url' => [
                  'text' => 'Url',
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
        $actions = array(
          'heading' => 'Page',
          'slug' => '/pages'
        );

        $this->obj = new Page;
        if ($id) {
          $this->obj = $this->obj->find($id);
          $actions = array(
            'heading' => 'Page : ' . $this->obj->name,
            'slug' => '/pages'
          );
        }

        return array(
          'page' => $this->obj,
          'actions' => $actions
        );
    }

    public function initProcessCreate($id = null)
    {
        $this->obj = new Page;

        if ($id) {
          $this->obj = $this->obj->find($id);
        }

        if (request()->input('status')) {
          $status = 1;
        } else {
          $status = 0;
        }

        $data = $this->obj;
        $data->name = request()->input('name');
        $data->url = \Str::slug(request()->input('url'));
        $data->content = protectedString(request()->input('content'));
        $data->status = $status;
        $data->meta_title = request()->input('meta_title');
        $data->meta_description = request()->input('meta_description');
        $data->save();

        if (!$id) {
            return json('redirect', 'edit/' . $data->id);
        }

        return json('success', t('Page Updated'));
    }


    public function initProcessDelete()
    {
        $component = request()->input('component');
        $id = request()->input('id');

        if ($component == 'employee') {
            $component = 'admin_user';
        }

        if (!$id) {
            return true;
        }

        if (!$component) {
            return true;
        }

        $component = \Str::camel($component);
        $class = '\App\\Objects\\' . $component;

        $c = $class::find($id);
        $c->delete();

        return json('success', 'Deleted successfully');
    }

    public function initProcessChangeStatus()
    {
        $component = request()->input('component');
        $id = request()->input('id');
        $column = request()->input('column');
        $value = request()->input('value');

        $component = ucwords(\Str::camel($component));
        $class = '\App\\Objects\\' . $component;

        $c = $class::find($id);

        if ($c) {

            $c->updateColumn($column, $value);

            return array(
                'status' => 'success',
                'text' => 'Changes updated'
            );
        }

        return array(
            'status' => 'error',
            'text' => 'Object could not be initialised'
        );


        if (!$c->approved && !$c->status) {
          $first = true;
        }

        $c->$column = $status;
        $c->save();

        return json('success', ucfirst($column) . ' updated');
    }
}
