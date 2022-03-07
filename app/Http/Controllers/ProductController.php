<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use Session;

class ProductController extends Controller
{
    /*
    * define view path 
    */
    protected $view_path = '';

    /*
    * initialization function 
    */
    public function __construct() {
        $this->view_path = 'product'; 
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter');
        $products = Product::query()
                    ->with('shops');
                    if(!empty($filter) && is_array($filter)) {
                        if(!empty($filter['min']) && !empty($filter['max'])) {
                            $products->where('price','>=',$filter['min']);        
                            $products->where('price','<',$filter['max']);        
                        } 
                        if(!empty($filter['stock'])) { 
                            $products->where('stock','=',$filter['stock']);   
                        }
                    }
                    $products = $products->whereNull('deleted_at')
                    ->paginate(20);

        return View($this->view_path.'/index',compact('products','filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shops = Shop::query()->whereNull('deleted_at')->get();
        return View($this->view_path.'/create',compact('shops'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            
            'product_name' => 'required|max:255|unique:products,product_name,NULL,id,shop_id,'. $request->shop_id,
            'stock'   => 'required',
            'price'   => 'required',
            'shop_id' => 'required',
            'video'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=100,min_height=100',
        ]);

        if ($request->hasFile('image')) {
            $format = $request->file('image')->getClientOriginalExtension();
            $imageName = $request->name.".".$format;
            Storage::disk('public')->put($imageName, file_get_contents($request->file('image')));
        }

        $product = new Product();
        $product->product_name    = $request->product_name;
        $product->video         = isset($video) ? $video : "";
        $product->stock         = $request->stock;
        $product->price         = $request->price;
        $product->shop_id       = $request->shop_id;
        $product->save();

        Session::flash('message', 'Product Created Successfully!');
        return redirect()->route('product.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $shops = Shop::query()->whereNull('deleted_at')->get();
        return View($this->view_path.'/edit',compact('product','shops'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|max:255|unique:products,product_name,'.$id.',id,shop_id,'. $request->shop_id,
            'stock'   => 'required',
            'price'   => 'required',
            'shop_id' => 'required',
            'video'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=100,min_height=100',
        ]);

        if ($request->hasFile('video')) {
            $format = $request->file('video')->getClientOriginalExtension();
            $imageName = $request->name.".".$format;
            Storage::disk('public')->put($imageName, file_get_contents($request->file('video')));
        }

        $product = Product::find($id);
        $product->product_name    = $request->product_name;
        $product->video        = isset($video) ? $video : "";
        $product->stock        = $request->stock;
        $product->price        = $request->price;
        $product->shop_id      = $request->shop_id;
        $product->save();

        Session::flash('message', 'Products Created Successfully!');
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = $request->get('id');

        $delete = Product::find($id)->delete();
        
        Session::flash('error', 'Opration Failed!');
        return Redirect::back();
    }

    /**
     * Import Product View
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importView(Request $request) {
        $shops = Shop::query()->whereNull('deleted_at')->get();
        return View($this->view_path.'/importview',compact('shops'));
    }

    /**
     * Import Product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importProduct(Request $request) {
        Excel::import(new ProductImport, $request->file('file')->store('files'));
        Session::flash('message', 'Product Uploaded Successfully!');
        return redirect()->route('product.index');     
     
    }

    /**
     * Export Product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportProduct(Request $request) {

        return Excel::download(new ProductExport, 'Product.xlsx');
    }
}