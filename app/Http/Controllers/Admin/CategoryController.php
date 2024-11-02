<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TemplateCategory;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = TemplateCategory::orderBy('id', 'desc')->paginate(5);
        return inertia()
            ->render('Admin/Products/index',  [
                'user' => Auth()->user(),
                'products' => $products,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia()->render('Admin/Products/Create', [
            'user' => Auth()->user(),
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemplateCategory $product)
    {
        return inertia()->render('Admin/Products/Edit', [
            'user' => Auth()->user(),
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = TemplateCategory::findOrFail($id);
        if($product){
            $product->delete();
        }
        return redirect()->route('admin.product.index');
    }

    /*
        get the pagination data 
    */
    public function getProducts()
    {
        $products = TemplateCategory::orderBy('id', 'desc')->paginate(5);
        return response()->json($products);  
    }

    /*
        search for produc
    */
    public function search(Request $request){
        $request->validate([
            'data' => 'required|string|max:250'
        ]);

        $products = TemplateCategory::where('name', 'like', '%'.$request->post('data').'%')
            ->paginate(5);

        return response()->json($products);
    }
}
