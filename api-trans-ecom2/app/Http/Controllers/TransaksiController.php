<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;


class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $search = $request->query('search');
        $order = $request->query('order');

        switch ($order[0]['column']) {
            case '0':
                $orderby = 'invoice';
                break;
        
            case '1':
                $orderby = 'pembeli';
                break;
            
            case '2':
                $orderby = 'nama_produk';
                break;
            
            case '3':
                $orderby = 'total';
                break;
            
            default:;
                $orderby = 'created_at';
                break;
        }

        $data_db_total = Transaksi::all();
        $data_db_filtered = Transaksi::where('invoice', 'like', '%'.$search['value'].'%')->get();

        $data_db = Transaksi::selectRaw('*, jumlah_beli*harga_satuan as total')
                    ->where('invoice', 'like', '%'.$search['value'].'%')
                    ->orWhere('pembeli', 'like', '%'.$search['value'].'%')
                    ->offset($request->query('start'))
                    ->limit($request->query('length'))
                    ->orderByRaw($orderby.' '.$order[0]['dir'])
                    ->get();

        $data_formatted = [];

        foreach ($data_db as $key => $value) {
            $total = $value->harga_satuan * $value->jumlah_beli;
            $total = number_format($total, 0, ',', '.');
            $tgl_format = date("d-m-Y", strtotime($value->created_at));
            $row_data = [];
            $row_data[] = $value->invoice;
            $row_data[] = $value->pembeli;
            $row_data[] = $value->jumlah_beli.' x '.$value->nama_produk;
            $row_data[] = 'Rp '.$total;
            $row_data[] = date("d-M-Y", strtotime($value->created_at));
            $row_data[] = '<a href="#" onclick="callPage(\'form_transaksi.html\', '. 
            $value->id_transaksi .')">Edit</a> || <a href="#" onclick="hapusData('. 
            $value->id_transaksi .')">Hapus</a>';
            
            $data_formatted[] = $row_data;

        }

        $data_json = [
            'draw' => $request->query('draw'),
            'recordsTotal' => count($data_db_total),
            'recordsFiltered' => count($data_db_filtered),
            'data' => $data_formatted,
        ];

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
            'invoice' => 'required',
            'pembeli' => 'required',
            'nama_produk' => 'required',
            'jumlah_beli' => 'required',
            'harga_satuan' => 'required'
        ]);

        // upload file dan ekstensi
        $extension = $request->file('file')->extension();
        $path = $request->file('file')->storeAs(
            'bayar', $request->get('invoice').'.'.$extension
        );

        $data_db = new Transaksi([
            'invoice' => $request->get('invoice'),
            'pembeli' => $request->get('pembeli'),
            'nama_produk' => $request->get('nama_produk'),
            'jumlah_beli' => $request->get('jumlah_beli'),
            'harga_satuan' => $request->get('harga_satuan')
        ]);

        $data_db->save();

        $data_json = [
            'pesan' => 'Sukses',
            'data_insert' => $data_db,
        ];

        return json_encode($data_json);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function show(Transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaksi  $transaksi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaksi $transaksi)
    {
        //
    }

    public function cekTransaksi()
    {
        $label = [];
        $data = [];
        $data_1d = [];
        $total = [];
        $tgl_sekarang = date("d-M-Y H:i:s");
        $tgl_awal = date("Y-m-d", strtotime("-6 days"));
        $tgl_akhir = date("Y-m-d");

        while ($tgl_awal <= $tgl_akhir) {
            $data_db = Transaksi::selectRaw('sum(jumlah_beli*harga_satuan) as total')
            ->whereRaw('date(created_at) = "'.$tgl_awal.'"')
            ->first();

            $data_1d[] = $data_db->total==null?0:$data_db->total;
            $total += $data_db->total==null?0:$data_db->total;
            $label[] =  date("d-M-Y", strtotime($tgl_awal));

            if ($tgl_awal == $tgl_akhir) {
                $data_1d[] = $data_db->total==null?0:$data_db->total;
            }

            $tgl_awal = date("Y-m-d", strtotime($tgl_awal.' +1 day'));
        }

        $data[] = $$data_1d;
        $data_json = [
            "data" => $data,
            "label" => $label,
            "total" => number_format($total,0,',','.'),
            "tgl_update" => $tgl_sekarang
        ];

        return json_encode($data_json);
    }
}
