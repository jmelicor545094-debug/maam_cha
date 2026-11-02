<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $catalog = Product::with(['categoryRecord', 'batches' => fn ($query) => $query->where('status', 'active')->where('quantity', '>', 0)
                ->where(fn ($query) => $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))])
            ->when($request->string('search')->trim()->value(), fn ($query, $search) => $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
            ->orderBy('name')->get();
        $catalog->each(fn ($product) => $product->setAttribute('available_quantity', (int) $product->batches->sum('quantity')));

        $stockStatus = $request->query('stock_status');
        $catalog = $catalog->when($stockStatus === 'in-stock', fn ($items) => $items->filter(fn ($product) => $product->available_quantity > $product->minimum_stock))
            ->when($stockStatus === 'low-stock', fn ($items) => $items->filter(fn ($product) => $product->available_quantity > 0 && $product->available_quantity <= $product->minimum_stock))
            ->when($stockStatus === 'out-of-stock', fn ($items) => $items->filter(fn ($product) => $product->available_quantity <= 0))
            ->values();

        $perPage = 12;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $products = new LengthAwarePaginator($catalog->forPage($page, $perPage)->values(), $catalog->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.form', ['product' => new Product(), 'categories' => Category::orderBy('name')->get(), 'brands' => Product::whereNotNull('brand')->where('brand', '<>', '')->distinct()->orderBy('brand')->pluck('brand')]);
    }

    public function generateSku(Request $request): JsonResponse
    {
        $data = $request->validate(['category_id' => ['required', 'integer', 'exists:categories,id']]);
        $category = Category::findOrFail($data['category_id']);
        $categoryName = Str::lower($category->name);
        $prefix = Str::contains($categoryName, ['medicine', 'medicinal', 'antibiotic', 'pain relief']) ? 'MED' : 'SUP';
        $nextNumber = Product::withTrashed()->pluck('sku')
            ->map(fn ($sku) => preg_match('/^'.preg_quote($prefix, '/').'-(\d+)$/i', $sku, $matches) ? (int) $matches[1] : 0)
            ->max() + 1;

        do {
            $sku = sprintf('%s-%03d', $prefix, $nextNumber++);
        } while (Product::withTrashed()->where('sku', $sku)->exists());

        return response()->json(['sku' => $sku]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request); $category = Category::findOrFail($data['category_id']); $data['category'] = $category->name;
        $product = Product::create($data); ActivityLog::record('Products', 'Created', 'Created product '.$product->sku.' - '.$product->name.'.');
        return redirect()->route('products.index')->with('status', 'Product added.');
    }

    public function edit(Product $product): View
    {
        return view('products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get(), 'brands' => Product::whereNotNull('brand')->where('brand', '<>', '')->distinct()->orderBy('brand')->pluck('brand')]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product); $category = Category::findOrFail($data['category_id']); $data['category'] = $category->name;
        $product->update($data); ActivityLog::record('Products', 'Updated', 'Updated product '.$product->sku.' - '.$product->name.'.');
        return redirect()->route('products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $deletedProduct = DB::transaction(function () use ($product) {
            $lockedProduct = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $hasStock = DB::table('stock_batches')->where('product_id', $lockedProduct->id)->where('quantity', '>', 0)->exists();
            $hasHistory = collect(['stock_movements', 'stock_in_items', 'sales_order_items', 'purchase_order_items'])
                ->contains(fn ($table) => DB::table($table)->where('product_id', $lockedProduct->id)->exists());

            if ($hasStock || $lockedProduct->quantity > 0 || $hasHistory) {
                return null;
            }

            $lockedProduct->forceDelete();

            return ['sku' => $lockedProduct->sku, 'name' => $lockedProduct->name];
        });

        if ($deletedProduct === null) {
            return back()->withErrors(['product' => 'This product has stock or transaction history and cannot be permanently deleted. Clear its stock and retain it in the catalog instead.']);
        }

        ActivityLog::record('Products', 'Deleted', 'Permanently deleted product '.$deletedProduct['sku'].' - '.$deletedProduct['name'].'.');

        return redirect()->route('products.index')->with('status', 'Product deleted permanently.');
    }

    public function archived(): View { return view('products.archived', ['products' => Product::onlyTrashed()->with('categoryRecord')->orderBy('name')->paginate(15)]); }

    public function restore(int $productId): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($productId); $product->restore();
        ActivityLog::record('Products', 'Restored', 'Restored product '.$product->sku.'.');
        return redirect()->route('products.archived')->with('status', 'Product restored.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:64', 'unique:products,sku'.($product ? ','.$product->id : '')],
            'name' => ['required', 'string', 'max:160'],
            'brand' => ['nullable', 'string', 'max:100'],
            'brand_other' => [Rule::requiredIf($request->input('brand') === '__other__'), 'nullable', 'string', 'max:100'],
            'category_id' => ['required', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'unit' => ['required', 'string', 'max:40'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ]);

        $data['brand'] = $data['brand'] === '__other__' ? $data['brand_other'] : ($data['brand'] ?: null);
        unset($data['brand_other']);

        return $data;
    }
}
