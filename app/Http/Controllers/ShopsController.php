<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShopsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$shop_items = Shop::all();    
        if (Auth::user()) {
            $shop_items = DB::table('shops')->paginate(4);
            $jumlah_cart = DB::table('data_pemesan')->count();
            //dd($shop_items);
            return view('home', ['shop_items' => $shop_items, 'jumlah_cart' => $jumlah_cart, 'role_id' => Auth::user()->role_id]);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nama_pemesan = $request->nama_pemesan;
        $nomor_pemesan = $request->nomor_pemesan;
        $jumlah_pemesan = $request->jumlah_pemesan;

        $tambah = DB::table('data_pemesan')->insert([
            'nama_pemesan' => $nama_pemesan,
            'nomor_pemesan' => $nomor_pemesan,
            'jumlah_pemesan' => $jumlah_pemesan
        ]);

        return redirect()->to('https://api.whatsapp.com/send?phone=6283832071086&text=Hi%20Admin%20E-wak.%20Nama%20saya' . $nama_pemesan);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (Auth::user()) {
            $data = DB::table('shops')->where('id', $id)->first();
            return view('detail_pesanan', ['data' => $data, 'role_id' => Auth::user()->role_id]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function cart()
    {
        $join_cart = DB::table('data_pemesan')
            ->join('shops', 'data_pemesan.shop_id', '=', 'shops.id')
            ->join('users', 'data_pemesan.pemesan_id', '=', 'users.id')
            ->get();
        $jumlah = 0;
        foreach ($join_cart as $p) {
            $hargatotal = $p->jumlah_pemesan * $p->unit_price;
            $jumlah = $jumlah + $hargatotal;
        }
        return view('cart', ['pesanan' => $join_cart, 'jumlah' => $jumlah]);
    }
}
