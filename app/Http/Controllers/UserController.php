<?php

namespace App\Http\Controllers;


use App\Models\Department;
use App\Models\User;
use App\Models\UserPrivilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = ['username' => $request->username, 'password' => $request->password];

        if (Auth::attempt($credentials)) {
            $user_priv = UserPrivilege::where([['user_id', Auth::id()], ['syscode', 'notifier']])->get()->first();
            if ($user_priv == null) {
                Auth::logout();
                return back()->with(['message' => 'Access denied']);
            }

            session(['access_rights' => $user_priv->level]);
            session(['code' => $user_priv->level]);

            if ($user_priv->level == "EMED") {
                session(['code' => "TRIAGGE"]);
            }

            $department = Department::where('code', session('code'))->first();
            if ($department) {
                session(['access_rights' => $department->department]);
            }

            switch (session('code')) {
                case "TRIAGGE":
                    return redirect()->intended('/triagge');
                default:
                    return redirect()->intended('/referrals');
            }
        }
        return back()->with(['message' => 'The provided credentials do not match our records.']);
    }

    public function changePassword(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if (!Hash::check($request->old_password, $user->password)) {
                return ['code' => 500, 'message' => 'Incorrect old password'];
            }

            $user->password = bcrypt($request->new_password);
            $user->save();
            return ['code' => 200, 'message' => 'Password successfully changed'];
        } catch (\Exception $exception) {
            return ['code' => 500, 'message' => $exception->getMessage()];
        }
    }
}
