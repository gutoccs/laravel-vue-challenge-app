<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use \Gumlet\ImageResize;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show(Product $product)
    {
        //
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
    public function destroy(Product $product)
    {
        //
    }


}
