<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function chat(){
        $data['header_title'] = "Liste des messages";
            return view('chat.list', $data);
    }

}
