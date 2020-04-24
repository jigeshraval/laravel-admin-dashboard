<?php

namespace App\Http\Controllers\AdminControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use Illuminate\Auth\Events\Registered;
use Hash;
use Auth;
use App\Objects\AdminUser;

class AdminUserController extends AdminController
{
    public function initListing()
    {
        $this->initProcessFilter();

        $admin_user = AdminUser::select('id', 'name', 'email')
        ->orderBy('id', 'desc');

        if ($this->filter) {
            $admin_user->where($this->filter_search);
        }

        $this->obj = $admin_user->paginate($this->paginate);

        $keys = [
              'id' => [
                  'text' => 'ID',
                  'filter' => true
              ],
              'name' => [
                  'text' => 'Name',
                  'filter' => true
              ],
              'email' => [
                  'text' => 'Email',
                  'filter' => true
              ]
        ];

        return array(
            'obj' => $this->obj,
            'keys' => $keys
        );
    }

    public function initContentRegister($id = null)
    {
        $this->obj = new AdminUser;

        if ($id) {
          $this->obj = AdminUser::find($id);
        }


        return array(
            'employee' => $this->obj
        );
    }

    public function initProcessRegister(Request $request, $id = null)
    {
        $data = $request->all();
        if ($data['password'] != $data['password_confirmation']) {
            return json('error', 'Password and Confirm Passwords are not matching');
        }

        if (isset($data['password']) && $data['password'] && strlen($data['password']) < 6) {
            return json('error', 'Too short password');
        }

        $getUser = AdminUser::where('email', $data['email'])->first();

        if ($getUser) {
          $admin_user = $getUser;
        }

        if ($getUser && !$id) {
            return json('error', 'Admin User already exists');
        }

        $admin_user->name = $data['name'];
        $admin_user->email = $data['email'];
        $admin_user->password = Hash::make($data['password']);
        $admin_user->save();

        if (!$id) {
            return json('redirect', 'edit/' . $admin_user->id);
        }

        return json('success', t('Admin User updated'));
    }

    public function guard()
    {
        return Auth::guard('admin_user');
    }

    public function initProcessDelete($id = null)
    {
        $obj = AdminUser::find($id);

        if ($obj) {
          
          $obj->delete();

        }

        return redirect(route('admin_user.list'));
    }

    public function initProcessLogin()
    {
        $login_success = Auth::guard('admin_user')->attempt([
          'email' => request()->input('email'),
          'password' => request()->input('password')
        ], request()->input('keep_active'));

        if ($login_success) {
          return array(
            'status' => 'success',
            'message' => 'Authenticated'
          );
        }

        return array(
          'status' => 'error',
          'message' => 'Authentication failed'
        );
    }

    public function initProcessLogout()
    {
        Auth::guard('admin_user')->logout();

        $admin_route = config('adlara.admin_route') . '/app/login';

        $url = url($admin_route);

        return redirect($url);
    }

    public function initProcessCheckLogin()
    {
        if (Auth::guard('admin_user')->check()) {
          return array(
            'status' => 'success'
          );
        }

        return response('Unauthenticated', 401);
    }
}
