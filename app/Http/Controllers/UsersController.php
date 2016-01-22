<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Role;

class UsersController extends Controller
{

    public function login(Request $request)
    {
        $this->view->title = 'Log in';
        $adldap = config('adldap');
        $this->view->adldap_enabled = $adldap['enabled'];
        $this->view->adldap_suffix = $adldap['account_suffix'];
        $this->view->breadcrumbs = [
            'users' => 'Users',
            'login' => 'Log In',
        ];
        $this->view->username = $request->session()->get('username');
        return $this->view;
    }

    public function loginPost(Request $request)
    {
        $username = $request->input('username');
        $request->session()->set('username', $username);
        $password = $request->input('password');

        // Try to log in.
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $this->alert('success', 'You are now logged in.', true);
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
            try {
                $authed = $adldap->authenticate($username, $password);
//                if (!$authed) {
//                    
//                }
                $user = User::firstOrCreate(['username' => $username]);
                $ldapUser = $adldap->users()->find($username);
                $user->name = $ldapUser->getDisplayName();
                $user->email = $ldapUser->getEmail();
                $user->save();
                Auth::login($user);
                $this->alert('success', 'You are now logged in.', true);
                return redirect('/');
            } catch (\Adldap\Exceptions\AdldapException $ex) {
                // Invalid credentials.
            }
        }

        // If we're still here, authentication has failed.
        $this->alert('warning', 'Athentication failed.');
        return redirect()->back()->withInput();
    }

    public function logout()
    {
        Auth::logout();
        $this->alert('success', 'You have been logged out.');
        return redirect('/login');
    }

    public function remind(Request $request)
    {
        $this->view->breadcrumbs = [
            'users' => 'Users',
            'login' => 'Log In',
            'remind' => 'Password reminder',
        ];
        $this->view->title = 'Password reminder';
        return $this->view;
    }

    public function remindPost(Request $request)
    {
        $user = User::whereUsername($request->input('username'))->first();
        if ($user instanceof User) {
            $user->sendPasswordReminder();
        }
        // Doesn't matter if it's not actually a success; don't tell them that.
        $this->alert('success', 'Please check your email.');
        return redirect('/login');
    }

    public function profile($username)
    {
        $user = User::firstOrNew(['username' => $username]);
        $isOwn = ($this->user && $this->user->id === $user->id);
        $isAdmin = ($this->user && $this->user->isAdmin());
        if (!$isOwn && !$isAdmin) {
            $this->alert('warning', "You are not allowed to view other users' profiles.", false);
            return $this->view;
        }
        $this->view->the_user = $user;
        $this->view->user_dates = $user->UserUnavailabilities()->orderBy('start_date', 'DESC')->get();
        $this->view->roles = Role::orderBy('name', 'ASC')->get();
        $this->view->title = 'User profile';
        $this->view->breadcrumbs = [
            'users' => 'Users',
            'users/' . $user->username => $user->name,
        ];
        return $this->view;
    }

    public function profilePost(Request $request, $username)
    {
        $user = User::firstOrNew(['username' => $username]);
        $isOwn = ($this->user && $this->user->id === $user->id);
        $isAdmin = ($this->user && $this->user->isAdmin());
        if (!$isOwn && !$isAdmin) {
            return redirect('users/' . $this->user->username);
        }
        $user->username = $request->input('username');
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->input('password') !== $request->input('password_confirmation')) {
            $this->alert('warning', 'Your passwords did not match. Not changed.');
        } else {
            $user->password = bcrypt($request->input('password'));
        }
        $user->save();

        // Roles.
        if ($isAdmin && $request->input('roles')) {
            $user->roles()->sync($request->input('roles'));
        }

        // Save unavailabilities.
        \DB::table('user_unavailabilities')->where('user_id', '=', $user->id)->delete();
        foreach ($request->input('unavailabilities') as $unavail) {
            if (empty($unavail['start_date']) && empty($unavail['end_date'])) {
                continue;
            }
            $unavailability = new \App\Model\UserUnavailability();
            $unavailability->user_id = $user->id;
            $unavailability->start_date = $unavail['start_date'];
            $unavailability->end_date = $unavail['end_date'];
            $unavailability->save();
        }

        $this->alert('success', 'User profile information saved.');
        return redirect('users');
    }

    public function index(Request $request)
    {
        if ($request->input('username')) {
            return redirect('users/' . $request->input('username'));
        }
        $this->view->title = 'Users';
        $this->view->breadcrumbs = [
            'users' => 'Users',
        ];
        $this->view->email_queue_count = \App\Model\QueuedEmail::count();
        $this->view->roles = Role::orderBy('name', 'ASC')->get();
        $this->view->users = User::orderBy('name', 'ASC')->paginate(20);
        $start = new \DateTime('today');
        $end = new \DateTime('1 month');
        $this->view->dates = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        $this->view->day_count = iterator_count($this->view->dates);
        return $this->view;
    }

    public function json(Request $request)
    {
        $term = '%' . $request->input('term') . '%';
        $users = User::where('name', 'LIKE', $term)
                ->orWhere('username', 'LIKE', $term)
                ->get();
        $out = array();
        foreach ($users as $user) {
            $out[] = array('label' => $user->username);
        }
        return \Response::json($out);
    }
}
