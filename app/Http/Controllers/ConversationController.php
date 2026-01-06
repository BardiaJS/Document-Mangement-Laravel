<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ForbiddenException;

class ConversationController extends Controller
{
    public function send_message(User $user){
        if(Auth::user()->is_support == 1){
            $message = Conversation::create([
                'sender_id' => Auth::user()->id ,
                'receiver_id' => $user->id,
                'text' => request()->input('text')
            ]);
           broadcast(new MessageSent($message))->toOthers();
            
            return $message;
        }else if(!(Auth::user()->is_support) and ($user->is_support == 1)){
            $message = Conversation::create([
                'sender_id' => Auth::user()->id ,
                'receiver_id' => $user->id,
                'text' => request()->input('text')
            ]);
            broadcast(new MessageSent($message))->toOthers();
 
           return $message;
        }else{
            throw new ForbiddenException();
        }

    }

    public function receive_message(User $user){
        return Conversation::query()
            ->where(function ($query) use ($user) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', auth()->id());
            })
            ->with(['sender', 'receiver'])
            ->orderBy('id', 'asc')
            ->get();
    }
}
