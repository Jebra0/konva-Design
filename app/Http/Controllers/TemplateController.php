<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(){

    }
    public function store(Request $request){
        $request->validate([
            "data"=> "required|json",
        ]);
        $template = new Template();
        $template->type = $request->rype;
        $template->data = $request->data;
        $template->save();
    }
}
