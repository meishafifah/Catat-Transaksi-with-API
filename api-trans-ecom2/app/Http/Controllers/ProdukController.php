<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data_list = Produk::all();

        $data_json = [
            'jumlah_data' => count($data_list),
            'data_list' => $data_list,
            'title' => 'Homepage',
        ];

        return json_encode($data_json);
    }

    public function search(Request $request)
    {
        $data_list = Produk::where('nama_produk', 'like', '%'.$request->query('term').'%')->limit(5)->get();

        $data_json = [];

        foreach ($data_list as $key => $value) {
            $value->id = $value->id_produk;
            $value->label = $value->nama_produk;
            $value->value = $value->nama_produk;

            $data_json[] = $value;
        }

        return json_encode($data_json);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //
        $request->validate([
            'nama_produk' => 'required',
            'deskripsi_produk' => 'required',
            'stok' => 'required',
            'harga' => 'required'
        ]);

        $produk = new Produk([
            'nama_produk' => $request->get('nama_produk'),
            'deskripsi_produk' => $request->get('deskripsi_produk'),
            'stok' => $request->get('stok'),
            'harga' => $request->get('harga'),
        ]);
        
        $saved = $produk->save();

        if(!$saved){
            $data_json = [
                'pesan' => 'Gagal Menambah Data',
                'produk' => $produk,
            ];
        } else {
            $data_json = [
                'pesan' => 'Sukses',
                'produk' => $produk,
            ];
        }

        return json_encode($data_json);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Produk $produk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Produk $produk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produk $produk)
    {
        //
    }
}
