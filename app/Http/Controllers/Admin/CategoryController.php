<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Http\Request;
use App\Models\TemplateCategory;
use Illuminate\Support\Facades\DB;
use Log;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = TemplateCategory::orderBy('id', 'desc')
            ->paginate(10);
        // add the authorization attribute
        $products->getCollection()->transform(function ($product) {
            $product->authorized = $product->user_id !== null && auth()->id() == $product->user_id;
            return $product;
        });
        return inertia()
            ->render('Admin/Products/index', [
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
    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $category = TemplateCategory::create([
                'user_id' => auth()->id(),
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
                        'value' => $value['value'],
                        'price' => $value['price']
                    ]);
                }

                $option_rows[] = $opt->id;
            }
            $category->options()->attach($option_rows);

            DB::commit();

            return redirect()->route('admin.product.index')
                ->with('message', 'Product and options created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.product.index')
                ->with('error', 'Try again, Some thing wrong.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = TemplateCategory::with('options')->findOrFail($id);
        if($product->user_id != auth()->id() ){
            abort(403);
        }
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
        $request->validate([
            'product_name' => 'required|string|max:250',
            'product_price' => 'required|integer',
            'product_quantity' => 'required|integer',
            'options' => 'required|array',
            'options.*.opt_id' => 'nullable|exists:options,id',
            'options.*.opt_values.*.value_id' => 'nullable|exists:option_values,id',
        ]);

        try {
            DB::beginTransaction();

            $category = TemplateCategory::findOrFail($id);

            if($category->user_id != auth()->id() ){
                abort(403);
            }
            $category->fill([
                'name' => $request->post('product_name'),
                'price' => $request->post('product_price'),
                'quantity' => $request->post('product_quantity'),
            ]);

            if ($category->isDirty()) {
                $category->save();
            }

            $options = $request->post('options');
            $option_rows = [];

            foreach ($options as $option) {
                if (isset($option['opt_id'])) {
                    $opt = Option::with('values')->findOrFail($option['opt_id']);
                    $opt->fill(['name' => $option['opt_name']]);

                    if ($opt->isDirty()) {
                        $opt->save();
                    }

                    $currentValueIds = $opt->values->pluck('id')->toArray();

                    foreach ($option['opt_values'] as $value) {
                        if (isset($value['value_id'])) {

                            $val = OptionValue::findOrFail($value['value_id']);
                            $val->fill([
                                'option_id' => $opt->id,
                                'value' => $value['value'],
                                'price' => $value['price']
                            ]);
                            $val->save();

                            $currentValueIds = array_diff($currentValueIds, [$val->id]);
                        } else {
                            OptionValue::create([
                                'option_id' => $opt->id,
                                'value' => $value['value'],
                                'price' => $value['price']
                            ]);
                        }
                    }

                    foreach ($currentValueIds as $idToDelete) {
                        OptionValue::destroy($idToDelete);
                    }

                    $option_rows[] = $opt->id;
                } else {
                    $opt = Option::create(['name' => $option['opt_name']]);

                    foreach ($option['opt_values'] as $value) {
                        OptionValue::create([
                            'option_id' => $opt->id,
                            'value' => $value['value'],
                            'price' => $value['price']
                        ]);
                    }

                    $option_rows[] = $opt->id;
                }
            }

            $category->options()->sync($option_rows);

            DB::commit();

            return redirect()->route('admin.product.index')
                ->with('message', 'Product and options updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.product.index')
                ->with('error', 'Try again, something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = TemplateCategory::findOrFail($id);

        if($product->user_id != auth()->id() ){
            abort(403);
        }
        if ($product) {
            $product->delete();
        }
        return redirect()->route('admin.product.index')
            ->with('message', 'Deleted successfully');
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
        $data = $request->query('searshData');
        if ($data) {
            $request->validate([
                'searshData' => 'required|string|min:2|max:250'
            ]);
        }

        $products = TemplateCategory::where('name', 'like', '%' . $data . '%')
            ->paginate(10)->withQueryString();

        return inertia('Admin/Products/index', compact('products'));
    }
}
