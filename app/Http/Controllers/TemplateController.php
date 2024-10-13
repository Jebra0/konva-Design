<?php

namespace App\Http\Controllers;

use App\Console\Commands\AddFonts;
use App\Models\Template;
use App\Models\Font;
use App\Models\TemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

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
            "data" => "required|json",
            "category_id" => "required|exists:templates_categories,id",
            "image" => "required|image|mimes:png|max:2048",
        ]);
        $category = TemplateCategory::findOrFail($request->category_id);

        $file = $request->file('image');
        $path = $file->store('images/' . $category->name, ['disk' => 'public_images']);

        $template = new Template();
        $template->name = $request->name;
        $template->data = $request->data;
        $template->image = $path;
        $template->category_id = $request->category_id;
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


    public function addCategory(Request $request)
    {
        $category = new TemplateCategory();
        $category->name = $request->name;
        $category->save();

        return $category;
    }

    public function deleteCategory(TemplateCategory $templateCategory)
    {
        $templateCategory->delete();

        return response()
            ->json(['message' => 'Category deleted successfully'], 200);
    }

    public function edit(Template $template, Request $request)
    {
        $request->validate([
            "data" => "required|json",
            "image" => "required|image|mimes:png|max:2048",
        ]);
        $category = TemplateCategory::findOrFail($template->category_id);
        // return $category;

        $oldImage = $template->image;
        Storage::disk('public_images')->delete($oldImage);

        $file = $request->file('image');
        $path = $file->store('images/' . $category->name, ['disk' => 'public_images']);

        return $template->update([
            'data' => $request->data,
            'image' => $path,
        ]);

    }

    public function destroy(Template $template)
    {
        $image = $template->image;
        Storage::disk('public_images')->delete($image);
        $template->delete();
    }

    public function search(Request $request){
        $request->validate([
            "name" => "string|required"
        ]);
        $templates = Template::where('name', 'like', '%'.$request->name.'%')
            ->get();

        return response()->json($templates);
    }
}
