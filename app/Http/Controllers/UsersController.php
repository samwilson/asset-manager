<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Role;

class UsersController extends Controller {

    public function login() {
        $this->view->title = 'Log in';
        $adldap = config('adldap');
        $this->view->adldap_enabled = $adldap['enabled'];
        $this->view->adldap_suffix = $adldap['account_suffix'];
        $this->view->breadcrumbs = [
            'users' => 'Users',
            'login' => 'Log In',
        ];
        return $this->view;
    }

    public function loginPost(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');

        // Try to log in.
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $this->alert('success', 'You are now logged in.', TRUE);
            return redirect()->intended();
        }

        // If that fails, try Adldap.
        $adldapConfig = config('adldap');
        if ($adldapConfig['enabled']) {
            $adldap = new \Adldap\Adldap($adldapConfig);
            if (empty($adldap->getConfiguration()->getAdminUsername())) {
                $adldap->getConfiguration()->setAdminUsername($username);
                $adldap->getConfiguration()->setAdminPassword($password);
            }
            if ($adldap->authenticate($username, $password)) {
                $user = \App\Model\User::firstOrCreate(['username' => $username]);
                $ldapUser = $adldap->users()->find('='.$username);
                $user->name = $ldapUser->getDisplayName();
                $user->email = $ldapUser->getEmail();
                $user->save();
                Auth::login($user);
                $this->alert('success', 'You are now logged in.', TRUE);
                return redirect('/');
            }
        }

        // If we're still here, authentication has failed.
        $this->alert('warning', 'Athentication failed.');
        return redirect()->back()->withInput();
    }

    public function logout() {
        Auth::logout();
        $this->alert('success', 'You have been logged out.');
        return redirect('/login');
    }

    public function profileGet($username) {
        $user = \App\Model\User::where('username', $username)->first();
        if (!$user) {
            $this->alert('info', "User '$username' not found.");
        }
        $view = $this->getView('users.profile');
        $view->the_user = $user;
        return $view;
    }

    public function profilePost($username) {
        $user = \App\Model\User::where('username', $username)->first();
        $this->alert('success', 'User profile information saved.');
        return redirect('users/' . $user->username);
    }

    public function index() {
        $this->view->users = User::orderBy('name', 'ASC')->paginate(20);
        return $this->view;
    }

}
