<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use App\Events\ExportProductsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use \Gumlet\ImageResize;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::select('id', 'name', 'description', 'price', 'reduced_image', 'created_at');

        //Auth::user()->hasRole('employee)
        if(isset(Auth::user()->employee)) {
            if($request->exists('min_price'))
                $products = $products->where('price', '>=', $request->min_price);

            if($request->exists('max_price'))
                $products = $products->where('price', '<=', $request->max_price);
        }

        $products = $products->get();

        return response()->json([
            'status'    =>  'OK',
            'products'  =>  $products
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          =>  'required|max:64',
            'description'   =>  'max:128',
            'price'         =>  'required|numeric',
            'image'         =>  'file|max:12288|dimensions:min_width=300,max_width=7200,min_height=300,max_height=7200|mimes:jpeg,bmp,png'
        ],
        [
            'name.required'         =>  'El Nombre es requerido',
            'name.max'              =>  'El Nombre no debe exceder los 64 caracteres',
            'description.max'       =>  'La Descripción no debe exceder los 128 caracteres',
            'price.required'        =>  'El Precio es requerido',
            'price.numeric'         =>  'El Precio debe ser numérico',
            'image.file'            =>  'La Imagen debe ser un tipo de archivo',
            'image.max'             =>  'La Imagen debe tener un peso máximo de 12MB',
            'image.dimensions'      =>  'El tamaño de la Imagen debe estar entre 300px y 7200px',
            'image.mimes'           =>  'La Imagen debe ser jpg, bmp o png'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        $auxPath = '';
        $path;

        try {
            DB::transaction(function() use ($request) {
                $product = new Product();

                $product->name = $request->name;

                if($request->exists('description'))
                    $product->description = $request->description;

                $product->price = $request->price;

                $product->save();

                if($request->exists('image')) {

                    $auxPath = "files/products/$product->id";
                    $path = public_path($auxPath);
                    Storage::makeDirectory($path);

                    $auxIMG = $request->image;
                    $extension = $auxIMG->extension();
                    $originalName = str_replace(' ','', $auxIMG->getClientOriginalName());
                    $auxIMG->move($path, $originalName);

                    $fullPathOriginalImage = $auxPath . '/' . $originalName;

                    $product->image_name = $originalName;
                    $product->original_image = $auxPath . '/' . $originalName;

                    $fullPathNewImage = $auxPath . '/' . Str::random(12) . '.' . $extension;
                    $image = new ImageResize($fullPathOriginalImage);
                    $image->resize(300, 300);
                    $image->save($fullPathNewImage);

                    $product->reduced_image = $fullPathNewImage;

                    $product->save();
                }
            });
        } catch (\Exception $e) {
            if($auxPath != '')
                File::deleteDirectory($path);
            Log::error($e);
            return response()->json(['error' => 'No se pudo guardar el producto']);
        }

        return response()->json(['status' => 'OK']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($productId)
    {
        $product = Product::select('id', 'name', 'description', 'price', 'reduced_image', 'created_at')
                            ->where('id', $productId)
                            ->first();

        return response()->json([
            'status'    =>  'OK',
            'product'  =>  $product
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function showView($idProduct)
    {
        $product = Product::find($idProduct);

        if($product)
            return view('product_description', [
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => ($product->reduced_image != null) ? env('APP_URL') . $product->reduced_image : '',
            ]);

        return;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId)
    {
        $product = Product::find($productId);

        if(!$product)
            return response()->json(['status'    =>  'fail', 'error'    =>  'Producto no existe'], 400);


        if($product->delete())
            return response()->json(['status'    =>  'OK'], 200);


        return response()->json(['status'    =>  'fail', 'error'    =>  'No se pudo borrar al producto'], 400);

    }

    public function importFromExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products'         =>  'required|file|max:12288|mimes:xls,xlsx'
        ],
        [
            'products.required'        =>  'El Archivo es requerido',
            'products.file'            =>  'El Archivo debe ser un tipo de archivo',
            'products.max'             =>  'El Archivo debe tener un peso máximo de 12MB',
            'products.mimes'           =>  'El Archivo debe ser xls o xlsx'
        ]);

        if($validator->fails())
            return response()->json(['errors' => $validator->errors()], 400);

        try {
            (new ProductsImport)->import(request()->file('products'));
        }
        catch (\Exception $e) {
            return response()->json(['status' => 'Fail', 'error' => 'Revise el archivo'], 400);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'Fail', 'error' => 'Revise el archivo'], 400);
        }

        return response()->json(['status' => 'OK'], 200);
    }

    public function exportToExcel()
    {
        // Si se coloca un request, pudiera crearse algunos filtros

        // Si en Product Export  se usa el trait exportable se pudiera guardar en disco también

        $products = Product::select('id', 'name', 'description', 'price', 'image_name', 'created_at')
                    ->get();

        $file = new ProductsExport($products);

        // Paso el empleado que lo descarga, con el fin de pasar algo
        event(new ExportProductsEvent(Auth::user()->employee));

        return $file->download('products.xlsx');
    }

    public function generatePDF($idProduct) {

        $product = Product::find($idProduct);

        if(!$product)
            return response()->json(['status' => 'fail'], 400);

        $data = [
            'name' => $product->name,
            'price' => $product->price,
            'image' => ($product->reduced_image != null) ? env('APP_URL') . $product->reduced_image : '',
        ];

        $pdf = \PDF::loadView('product_description', $data);

        return $pdf->download("{$product->name}.pdf");
    }

}
