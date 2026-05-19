<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SetMenuController extends Controller
{
    public function index(Request $request): View
    {
        $branches = DB::table('branches')
            ->orderBy('name')
            ->get();

        $categories = DB::table('categories')
            ->orderBy('title_en')
            ->get();

        $orderItems = DB::table('order_items')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'order_items.product_id',
                'order_items.item_name',
                'order_items.unit_price',
                'products.title_en as product_title'
            )
            ->whereNotNull('order_items.product_id')
            ->groupBy(
                'order_items.product_id',
                'order_items.item_name',
                'order_items.unit_price',
                'products.title_en'
            )
            ->orderBy('order_items.item_name')
            ->get();

        $setMenus = DB::table('set_menus')
            ->leftJoin('branches', 'set_menus.branch_id', '=', 'branches.id')
            ->leftJoin('categories', 'set_menus.category_id', '=', 'categories.id')
            ->select(
                'set_menus.id',
                'set_menus.branch_id',
                'set_menus.category_id',
                'set_menus.code',
                'set_menus.title_en',
                'set_menus.title_km',
                'set_menus.description',
                'set_menus.price',
                'set_menus.status',
                'branches.name as branch_name',
                'categories.title_en as category_name'
            )
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('set_menus.branch_id', $request->branch_id);
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('set_menus.category_id', $request->category_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('set_menus.status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('set_menus.code', 'like', "%{$search}%")
                        ->orWhere('set_menus.title_en', 'like', "%{$search}%")
                        ->orWhere('set_menus.title_km', 'like', "%{$search}%")
                        ->orWhere('set_menus.description', 'like', "%{$search}%")
                        ->orWhere('branches.name', 'like', "%{$search}%")
                        ->orWhere('categories.title_en', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('set_menus.id')
            ->paginate(10)
            ->withQueryString();

        $selectedItems = [];

        if (DB::getSchemaBuilder()->hasTable('set_menu_items')) {
            $rows = DB::table('set_menu_items')
                ->leftJoin('products', 'set_menu_items.product_id', '=', 'products.id')
                ->select(
                    'set_menu_items.set_menu_id',
                    'set_menu_items.product_id',
                    'set_menu_items.qty',
                    'products.title_en as product_name'
                )
                ->orderBy('set_menu_items.id')
                ->get();

            foreach ($rows as $row) {
                $selectedItems[$row->set_menu_id][] = [
                    'product_id' => $row->product_id,
                    'qty' => $row->qty,
                    'product_name' => $row->product_name,
                ];
            }
        }

        return view('item_management.set_menu', compact(
            'setMenus',
            'branches',
            'categories',
            'orderItems',
            'selectedItems'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:set_menus,code'],
            'title_en' => ['required', 'string', 'max:150'],
            'title_km' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.qty' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($validated) {
            $setMenuId = DB::table('set_menus')->insertGetId([
                'branch_id' => $validated['branch_id'],
                'category_id' => $validated['category_id'],
                'code' => $validated['code'],
                'title_en' => $validated['title_en'],
                'title_km' => $validated['title_km'] ?? null,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'status' => $validated['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($validated['items']) && DB::getSchemaBuilder()->hasTable('set_menu_items')) {
                foreach ($validated['items'] as $item) {
                    if (empty($item['product_id'])) {
                        continue;
                    }

                    DB::table('set_menu_items')->insert([
                        'set_menu_id' => $setMenuId,
                        'product_id' => $item['product_id'],
                        'qty' => $item['qty'] ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return redirect()
            ->route('set_menus.index')
            ->with('success', 'Set menu created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $setMenu = DB::table('set_menus')->where('id', $id)->first();

        abort_if(!$setMenu, 404);

        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:set_menus,code,' . $id],
            'title_en' => ['required', 'string', 'max:150'],
            'title_km' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.qty' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($validated, $id) {
            DB::table('set_menus')
                ->where('id', $id)
                ->update([
                    'branch_id' => $validated['branch_id'],
                    'category_id' => $validated['category_id'],
                    'code' => $validated['code'],
                    'title_en' => $validated['title_en'],
                    'title_km' => $validated['title_km'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'],
                    'status' => $validated['status'],
                    'updated_at' => now(),
                ]);

            if (DB::getSchemaBuilder()->hasTable('set_menu_items')) {
                DB::table('set_menu_items')
                    ->where('set_menu_id', $id)
                    ->delete();

                if (!empty($validated['items'])) {
                    foreach ($validated['items'] as $item) {
                        if (empty($item['product_id'])) {
                            continue;
                        }

                        DB::table('set_menu_items')->insert([
                            'set_menu_id' => $id,
                            'product_id' => $item['product_id'],
                            'qty' => $item['qty'] ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('set_menus.index')
            ->with('success', 'Set menu updated successfully.');
    }
}