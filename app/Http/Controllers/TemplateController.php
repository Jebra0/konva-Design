<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(Template $template)
    {
        return $template;
    }
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "type" => "required|string",
            "data" => "required|json",
            "image" => "required|image|mimes:png",
        ]);

        $file = $request->file('image');
        $path = $file->store('images/'.$request->type, ['disk' => 'public_images']);

        $template = new Template();
        $template->name = $request->name;
        $template->type = $request->type;
        $template->data = $request->data;
        $template->image = $path;
        $template->save();

        return $template;
    }
}
