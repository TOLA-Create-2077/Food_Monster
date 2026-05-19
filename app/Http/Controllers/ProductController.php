<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();

        $categories = DB::table('categories')
            ->orderBy('title_en')
            ->get();

        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('branches', 'products.branch_id', '=', 'branches.id')
            ->select(
                'products.id',
                'products.branch_id',
                'products.category_id',
                'products.code',
                'products.title_en',
                'products.title_km',
                'products.description_en',
                'products.description_km',
                'products.unit',
                'products.cost',
                'products.price',
                'products.status',
                'categories.title_en as category_name',
                'branches.name as branch_name'
            )
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('products.category_id', $request->category_id);
            })
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('products.branch_id', $request->branch_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('products.status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('products.code', 'like', "%{$search}%")
                        ->orWhere('products.title_en', 'like', "%{$search}%")
                        ->orWhere('products.title_km', 'like', "%{$search}%")
                        ->orWhere('products.description_en', 'like', "%{$search}%")
                        ->orWhere('products.description_km', 'like', "%{$search}%")
                        ->orWhere('products.unit', 'like', "%{$search}%")
                        ->orWhere('categories.title_en', 'like', "%{$search}%")
                        ->orWhere('branches.name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('products.id')
            ->paginate(10)
            ->withQueryString();

        return view('item_management.products', compact('products', 'categories', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'title_en' => ['required', 'string', 'max:150'],
            'title_km' => ['nullable', 'string', 'max:150'],
            'description_en' => ['nullable', 'string'],
            'description_km' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'cost' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DB::table('products')->insert([
            'branch_id' => $validated['branch_id'],
            'category_id' => $validated['category_id'],
            'code' => $validated['code'],
            'title_en' => $validated['title_en'],
            'title_km' => $validated['title_km'] ?? null,
            'description_en' => $validated['description_en'] ?? null,
            'description_km' => $validated['description_km'] ?? null,
            'unit' => $validated['unit'],
            'cost' => $validated['cost'],
            'price' => $validated['price'],
            'status' => $validated['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $product = DB::table('products')->where('id', $id)->first();

        abort_if(!$product, 404);

        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:products,code,' . $id],
            'title_en' => ['required', 'string', 'max:150'],
            'title_km' => ['nullable', 'string', 'max:150'],
            'description_en' => ['nullable', 'string'],
            'description_km' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'cost' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DB::table('products')
            ->where('id', $id)
            ->update([
                'branch_id' => $validated['branch_id'],
                'category_id' => $validated['category_id'],
                'code' => $validated['code'],
                'title_en' => $validated['title_en'],
                'title_km' => $validated['title_km'] ?? null,
                'description_en' => $validated['description_en'] ?? null,
                'description_km' => $validated['description_km'] ?? null,
                'unit' => $validated['unit'],
                'cost' => $validated['cost'],
                'price' => $validated['price'],
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }
}