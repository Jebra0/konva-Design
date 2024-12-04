<?php

namespace App\Http\Controllers;

use App\Console\Commands\AddFonts;
use App\Models\Design;
use App\Models\Template;
use App\Models\Text;
use App\Models\Shape;
use App\Models\Font;
use App\Models\UserImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{

    public function getAllTemplates(){
        return Template::with('category')->select(['id', 'image', 'user_id'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
    public function getTemplate(Request $request, $id)
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
            "jsonData" => "required|json",
            "type" => "required|in:text,shape,template,myDesigns",
            "category_id" => "nullable|required_if:type,template|exists:templates_categories,id",
            "image" => "required|image|mimes:png|max:2048",
        ]);

        if ($request->type == 'template') {
            $template = new Template();
            $template->category_id = $request->category_id;
            $template->user_id = auth()->user()->id;
            $folder = 'template_images';
        }
        if ($request->type == 'text') {
            $template = new Text();
            $template->user_id = auth()->user()->id;
            $folder = 'text_images';
        }
        if ($request->type == 'shape') {
            $template = new Shape();
            $template->user_id = auth()->user()->id;
            $folder = 'shape_images';
        }
        if ($request->type == 'myDesigns') {
            $template = new Design();
            $template->user_id = auth()->user()->id;
            $folder = 'user_design_images';
        }

        $file = $request->file('image');
        $path = $file->store('images/' . $folder, ['disk' => 'public_images']);

        $template->name = $request->name;
        $template->data = $request->jsonData;
        $template->image = $path;
        $template->save();

        return redirect()->back()->with('message', 'Added Successfully.');
    }

    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:png,jpeg,jpg|max:2048',
            'type' => 'required|in:admin,user',
        ]);

        $files = $request->file('images');

        if($request->post('type') == 'admin'){
            $path = 'Templates';
        }else{
            $path = 'user_images';
        }

        foreach ($files as $file) {
            $newFileName = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $image_path = $file->storeAs('images/'.$path, $newFileName, ['disk' => 'public_images']);

            if($request->post('type') == 'user'){
                UserImage::create([
                    'user_id' => auth()->user()->id,
                    'image' => $image_path,
                ]);
            }
        }
        return redirect()->back();
    }

    public function deleteImage(string $id){
        $image = UserImage::findOrFail($id);
        $image->delete();

        Storage::disk('public_images')->delete($image->image);

        return redirect()->back()->with('message', 'Deleted Successfully.');
    }

    public function deleteAdminImages(Request $request){
        Storage::disk('public_images')->delete($request->image);
        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            "jsonData" => "required|json",
            "image" => "required|image|mimes:png|max:2048",
            "type" => "Required|string|in:text,shape,template,myDesigns"
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

        $template->update([
            'data' => $request->post('jsonData'),
            'image' => $path,
        ]);

        return redirect()->back()->with('message', 'Edited Successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:template,text,shape,myDesigns',
        ]);
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
        $imageDeleted = !Storage::disk('public_images')->exists($template->image) || Storage::disk('public_images')->delete($template->image);
        if($imageDeleted){
            $templateDeleted = $template->delete();
            if($templateDeleted){
                return redirect()->back()->with('message', 'Deleted Successfully.');
            }
        }

    }

    public function search(Request $request)
    {
        $request->validate([
            "name" => "string|required"
        ]);
        $templates = Template::where('name', 'like', '%' . $request->name . '%')
            ->select(['id', 'image', 'user_id'])
            ->get();

        return response()->json($templates);
    }
}
