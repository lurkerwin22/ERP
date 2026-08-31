<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $selectedCategoryId = $request->query('category_id');

        return view('products.create', compact('categories', 'suppliers', 'selectedCategoryId'));
    }


    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => ['required', 'in:piece,kg,g,l,ml,m,cm,box,pack'],
            'purchase_price' => 'nullable|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'image_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'image_url' => 'nullable|url|max:1000',
        ]);

        $data = $validated;

        if ($request->hasFile('image_file')) {
            $data['image'] = $request->file('image_file')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        Product::create($data);

        return redirect('/products')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => ['required', 'in:piece,kg,g,l,ml,m,cm,box,pack'],
            'purchase_price' => 'nullable|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'image_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'image_url' => 'nullable|url|max:1000',
        ]);

        $data = $validated;

        if ($request->hasFile('image_file')) {
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image_file')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->input('image_url');
        }

        $product->update($data);

        return redirect('/products')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    

    /**
     * Display products by category.
     */
    public function category(Category $category)
    {
        $products = $category->products()
            ->with('category')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::all();
        return view('products.index', compact('products', 'categories', 'category' , 'suppliers'));
    }
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $products = Product::query()
            ->where('stock', '>', 0)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
                
                // Checks sku column if present in table
                if (\Schema::hasColumn('products', 'sku')) {
                    $q->orWhere('sku', 'like', "%{$query}%");
                }
            })
            ->select(['id', 'name', 'price', 'stock', 'unit'])
            ->limit(10)
            ->get();

        return response()->json($products);
    }
}