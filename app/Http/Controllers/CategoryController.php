<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')
            ->leftJoin('branches', 'branches.id', '=', 'categories.branch_id')
            ->select(
                'categories.*',
                'branches.name as branch_name'
            )
            ->get();

        return view('customers.categories', [
            'categories' => $categories
        ]);
    }
}