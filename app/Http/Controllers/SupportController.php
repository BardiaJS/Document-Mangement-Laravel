<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ForbiddenException;

class SupportController extends Controller
{
    public function ban_user(User $user){
        if(Auth::user()->is_support == 1){
            $user->is_banned = 1;
            $user->save();
        }else{
            throw new ForbiddenException();
        }
    }
    public function unban_user(User $user){
        if(Auth::user()->is_support ==1 ){
            $user->is_banned = 0;
            $user->save();
        }else{
            throw new ForbiddenException();
        }
    }
}
