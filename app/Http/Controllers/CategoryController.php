<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();

        $categories = DB::table('categories')
            ->leftJoin('branches', 'categories.branch_id', '=', 'branches.id')
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->select(
                'categories.id',
                'categories.branch_id',
                'categories.code',
                'categories.title_en',
                'categories.title_km',
                'categories.image',
                'categories.status',
                'branches.name as branch_name',
                DB::raw('COUNT(products.id) as total_items')
            )
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('categories.branch_id', $request->branch_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('categories.code', 'like', "%{$search}%")
                        ->orWhere('categories.title_en', 'like', "%{$search}%")
                        ->orWhere('categories.title_km', 'like', "%{$search}%")
                        ->orWhere('branches.name', 'like', "%{$search}%");
                });
            })
            ->groupBy(
                'categories.id',
                'categories.branch_id',
                'categories.code',
                'categories.title_en',
                'categories.title_km',
                'categories.image',
                'categories.status',
                'branches.name'
            )
            ->orderByDesc('categories.id')
            ->paginate(10)
            ->withQueryString();

        return view('item_management.categories', compact('categories', 'branches'));
    }

    public function create(): View
    {
        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();

        $category = null;

        return view('item_management.category_form', compact('branches', 'category'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'code' => ['required', 'string', 'max:100', 'unique:categories,code'],
            'title_en' => ['required', 'string', 'max:255'],
            'title_km' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        DB::table('categories')->insert([
            'branch_id' => $validated['branch_id'],
            'code' => $validated['code'],
            'title_en' => $validated['title_en'],
            'title_km' => $validated['title_km'] ?? null,
            'image' => $imagePath,
            'status' => $validated['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(int $id): View
    {
        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();

        $category = DB::table('categories')->where('id', $id)->first();

        abort_if(!$category, 404);

        return view('item_management.category_form', compact('branches', 'category'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = DB::table('categories')->where('id', $id)->first();

        abort_if(!$category, 404);

        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'code' => ['required', 'string', 'max:100', 'unique:categories,code,' . $id],
            'title_en' => ['required', 'string', 'max:255'],
            'title_km' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagePath = $category->image;

        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('categories', 'public');
        }

        DB::table('categories')
            ->where('id', $id)
            ->update([
                'branch_id' => $validated['branch_id'],
                'code' => $validated['code'],
                'title_en' => $validated['title_en'],
                'title_km' => $validated['title_km'] ?? null,
                'image' => $imagePath,
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }
}