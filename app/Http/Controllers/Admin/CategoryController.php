<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\OptionValue;
use DB;
use Illuminate\Http\Request;
use App\Models\TemplateCategory;
use Log;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = TemplateCategory::orderBy('id', 'desc')->paginate(5);
        return inertia()
            ->render('Admin/Products/index', [
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
        $request->validate([
            'product_name' => 'required|string|max:250',
            'product_price' => 'required|string',
            'product_quantity' => 'required|integer',

            'options' => 'required|array',
            'options.*.opt_name' => 'required|string',
            'options.*.opt_values' => 'required|array',
            'options.*.opt_values.*' => 'array|size:2',
            'options.*.opt_values.*.0' => 'required|string',
            'options.*.opt_values.*.1' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $category = TemplateCategory::create([
                'name' => $request->post('product_name'),
                'price' => $request->post('product_price'),
                'quantity' => $request->post('product_quantity'),
            ]);

            $options = $request->post('options');
            $option_rows = [];

            foreach ($options as $option) {
                $opt = Option::create([
                    'name' => $option['opt_name'],
                ]);

                foreach ($option['opt_values'] as $value) {
                    OptionValue::create([
                        'option_id' => $opt->id,
                        'value' => $value[0],
                        'price' => $value[1]
                    ]);
                }

                $option_rows[] = $opt->id;
            }
            $category->options()->attach($option_rows);

            DB::commit();
            
            return response()->json(['message' => 'Product and options created successfully.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        if ($product) {
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
    public function search(Request $request)
    {
        $request->validate([
            'data' => 'required|string|max:250'
        ]);

        $products = TemplateCategory::where('name', 'like', '%' . $request->post('data') . '%')
            ->paginate(5);

        return response()->json($products);
    }
}
