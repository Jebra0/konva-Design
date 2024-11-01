<?php

namespace App\Http\Controllers;

use App\Console\Commands\AddFonts;
use App\Models\Design;
use App\Models\Template;
use App\Models\Text;
use App\Models\Shape;
use App\Models\Font;
use App\Models\TemplateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

class TemplateController extends Controller
{
    public function index(Request $request, $id)
    {

        if ($request->type == 'template') {
            return Template::where('id', $id)->get();
        }
        if ($request->type == 'text') {
            return Text::where('id', $id)->get();
        }
        if ($request->type == 'shape') {
            return Shape::where('id', $id)->get();
        }
        if ($request->type == 'myDesigns') {
            return Design::where('id', $id)->get();
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "data" => "required|json",
            "type" => "required",
            "category_id" => "required_if:type,template",
            "image" => "required|image|mimes:png|max:2048",
        ]);

        if ($request->type == 'template') {
            $template = new Template();
            $template->category_id = $request->category_id;
            $folder = 'template_images';
        }
        if ($request->type == 'text') {
            $template = new Text();
            $folder = 'text_images';
        }
        if ($request->type == 'shape') {
            $template = new Shape();
            $folder = 'shape_images';
        }
        if ($request->type == 'myDesigns') {
            $template = new Design();
            $template->user_id = $request->user_id;
            $folder = 'user_design_images';
        }

        $file = $request->file('image');
        $path = $file->store('images/' . $folder, ['disk' => 'public_images']);

        
        $template->name = $request->name;
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

    public function edit(Request $request, $id)
    {

        $request->validate([
            "data" => "required|json",
            "image" => "required|image|mimes:png|max:2048",
            "type" => "string"
        ]);


        if ($request->type == 'template') {
            $template = Template::findOrFail($id);
            $category = 'template_images';
        }
        if ($request->type == 'text') {
            $template = Text::findOrFail($id);
            $category = 'text_images';
        }
        if ($request->type == 'shape') {
            $template = Shape::findOrFail($id);
            $category = 'shape_images';
        }
        if ($request->type == 'myDesigns') {
            $template = Design::findOrFail($id);
            $category = 'user_design_images';
        }

        $oldImage = $template->image;
        Storage::disk('public_images')->delete($oldImage);

        $file = $request->file('image');
        $path = $file->store('images/' . $category, ['disk' => 'public_images']);

        return $template->update([
            'data' => $request->data,
            'image' => $path,
        ]);

    }

    public function destroy(Request $request, $id)
    {
        if ($request->type == 'template') {
            $template = Template::findOrFail($id);
        }
        if ($request->type == 'text') {
            $template = Text::findOrFail($id);
        }
        if ($request->type == 'shape') {
            $template = Shape::findOrFail($id);
        }
        if ($request->type == 'myDesigns') {
            $template = Design::findOrFail($id);
        }

        $image = '/'.$template->pluck('image');
        
        Storage::disk('public_images')->delete($image);
        $template->delete();
    }

    public function search(Request $request)
    {
        $request->validate([
            "name" => "string|required"
        ]);
        $templates = Template::where('name', 'like', '%' . $request->name . '%')
            ->select(['id', 'image'])
            ->get();
        
        return response()->json($templates);
    }
}
