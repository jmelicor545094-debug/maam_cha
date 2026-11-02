<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = in_array((int) $request->input('per_page', 10), [10, 25, 50], true) ? (int) $request->input('per_page', 10) : 10;
        $categories = Category::withCount(['products as products_count' => fn ($query) => $query->withTrashed()])
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->input('search').'%'))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('categories.index', compact('categories', 'perPage'));
    }
    public function archived(): View { return view('categories.archived', ['categories' => Category::onlyTrashed()->withCount(['products as products_count' => fn ($query) => $query->withTrashed()])->orderBy('name')->paginate(15)]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required','string','max:100','unique:categories,name'], 'description' => ['nullable','string','max:1000']]);
        $category = Category::create($data); ActivityLog::record('Categories', 'Created', 'Created category '.$category->name.'.');
        return back()->with('status', 'Category created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate(['name' => ['required','string','max:100', Rule::unique('categories', 'name')->ignore($category->id)], 'description' => ['nullable','string','max:1000']]);
        $category->update($data); ActivityLog::record('Categories', 'Updated', 'Updated category '.$category->name.'.');
        return back()->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $name = DB::transaction(function () use ($category) {
            $lockedCategory = Category::whereKey($category->id)->lockForUpdate()->firstOrFail();
            if (Product::withTrashed()->where('category_id', $lockedCategory->id)->exists()) {
                return null;
            }

            $name = $lockedCategory->name;
            $lockedCategory->forceDelete();

            return $name;
        });

        if ($name === null) {
            return back()->withErrors(['category_delete' => 'This category is still linked to products and cannot be deleted.']);
        }

        ActivityLog::record('Categories', 'Deleted', 'Permanently deleted category '.$name.'.');

        return back()->with('status', 'Category deleted permanently.');
    }

    public function restore(int $categoryId): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($categoryId); $category->restore();
        ActivityLog::record('Categories', 'Restored', 'Restored category '.$category->name.'.');
        return redirect()->route('categories.archived')->with('status', 'Category restored.');
    }

    public function permanentlyDelete(int $categoryId): RedirectResponse
    {
        $name = DB::transaction(function () use ($categoryId) {
            $category = Category::onlyTrashed()->whereKey($categoryId)->lockForUpdate()->firstOrFail();
            if (Product::withTrashed()->where('category_id', $category->id)->exists()) {
                return null;
            }

            $name = $category->name;
            $category->forceDelete();

            return $name;
        });

        if ($name === null) {
            return back()->withErrors(['category_delete' => 'This category is still linked to products and cannot be permanently deleted.']);
        }

        ActivityLog::record('Categories', 'Permanently Deleted', 'Permanently deleted category '.$name.'.');

        return back()->with('status', 'Category permanently deleted.');
    }
}
