<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        // $techs = Product::all();
        
        //  return view('dashboard', compact('techs'));
        
        // // return view('dashboard',[
        // //     'techs' => DB::table('products')->paginate(15)
        // // ]);


        // $$techs= DB::table('products')->paginate(4); //Query Builder
	//Or you can use
        $techs= Product::paginate(15); //Eloquent ORM

        return view('dashboard', compact('techs'));
        

    }
}
