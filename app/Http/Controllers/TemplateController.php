<?php

namespace App\Http\Controllers;

use App\Console\Commands\AddFonts;
use App\Models\Template;
use App\Models\Font;
use App\Models\TemplateCategory;
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
            "image" => "required|image|mimes:png|max:2048",
        ]);

        $file = $request->file('image');
        $path = $file->store('images/' . $request->type, ['disk' => 'public_images']);

        $template = new Template();
        $template->name = $request->name;
        $template->type = $request->type;
        $template->data = $request->data;
        $template->image = $path;
        $template->save();

        return $template;
    }

    public function uploadTemplate(Request $request)
    {
        $files = $request->file('images');

        if (!is_array($files)) {
            return response()->json(['error' => 'No files provided or invalid data'], 400);
        }

        $paths = [];

        foreach ($files as $file) {
            try {
                $newFileName = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('images/Templates', $newFileName, ['disk' => 'public_images']);
                $paths[] = $path;
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error uploading file: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['paths' => $paths]);
    }

    // public function addFont(Request $request)
    // {
    //     $file = $request->file('font');
    //     $name = $request->name;

    //     $path = '';

    //     try {
    //         $originalName = $file->getClientOriginalName();

    //         $path = $file->storeAs('fonts/', $originalName, ['disk' => 'public_fonts']);
            
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Error uploading file: ' . $e->getMessage()], 500);
    //     }
    //     // to add the font src to the fonts.css
    //     loadFonts();
    //     $fontImage = pngFont($name, $path);

    //     //add to DB
    //     Font::create([
    //         'name'=> $name,
    //         'font_file' => $path,
    //         'font_image' => $fontImage,
    //     ]);

    //     return response()->json(['paths' => $path]);
    // }


    public function addCategory(Request $request){
        $category = new TemplateCategory();
        $category->name = $request->name;
        $category->save();
        
        return $category;
    }
}
