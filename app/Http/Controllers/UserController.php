<?php

namespace App\Http\Controllers;


use App\Models\Department;
use App\Models\UserPrivilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            if ($user_priv->level != 'TRIAGGE' && $user_priv->level != 'MANAGEMENT') {
                $department = Department::where('code', $user_priv->level)->first();
                if ($department) {
                    session(['access_rights' => $department->department]);
                }
            }

            switch ($user_priv->level) {
                case "TRIAGGE":
                    return redirect()->intended('/opcen');
                default:
                    return redirect()->intended('/department');
            }

            return redirect()->intended('/opcen');
        }
        return back()->with(['message' => 'The provided credentials do not match our records.']);
    }
}
