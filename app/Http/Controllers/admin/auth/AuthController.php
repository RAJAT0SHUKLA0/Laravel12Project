<?php

namespace App\Http\Controllers\admin\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\AuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Helper\Message;
use App\Models\User;

class AuthController extends Controller
{
    public function index(){
        return view('admin.auth.login');
    }
    public function login(AuthRequest $request){
        $validated = $request->validated();
        $credentials= array("mobile"=>$validated['username'],"password"=>$validated['password']);
        if (Auth::attempt($credentials)) {
            Session::regenerate();
            $user =User::with('role')->where('id',Auth::user()->id)->first();
            return redirect()->intended('dashboard') ->with('success', sprintf(Message::AUTH_MESSAGE,$user->role->name));
        }else{
            return back()->withErrors(['error' => 'The provided credentials do not match our records.']); 
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
        return redirect('/')->with('success', Message::LOGOUT_MESSAGE);
    }
}
