<?php

namespace App\Http\Controllers;

use App\Master;
use App\Pelanggan;
use App\Tagihan;
use Illuminate\Http\Request;
use Validator;
use DB;
use Exception;

class DataController extends Controller
{
    public function all_data_master(){
        $detail = Master::get();
    }
    public function update_data_master(Request $request){

    }

    public function index(){
        $detail = Pelanggan::join('tagihans','pelanggans.pelanggan_id','=','tagihans.pelanggan_id')
            ->select('*')->get();

        if (empty($detail)){
            $response = [
                'msg' => 'data not found',
                'status_code' => '0011',
                'data' => ''
            ];

            return response()->json($response,404);
        }

        $response = [
            'msg' => 'list semua data',
            'status_code' => '0001',
            'data' => $detail
        ];

        return response()->json($response,200);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'nama' => 'required',
            'level' => 'required',
            'telfon' => 'required'
        ]);

        if ($validator->fails()){
            $response = [
                'msg' => 'error when validate',
                'status_code' => '0002',
                'error' => $validator->errors()->toJson()
            ];

            return response()->json($response,400);
        }

        try {
            DB::beginTransaction();
            $pelanggan = Pelanggan::create([
                'nama' => $request->get('nama'),
                'level' => $request->get('level'),
                'telfon' => $request->get('telfon'),
            ]);
            if (!$pelanggan->save()){
                DB::rollBack();
                $response = [
                    'msg' => 'gagal membuat akun',
                    'status_code' => '0009',
                    'error' => ''
                ];

                return response()->json($response,400);
            }else{
                $tagihan = Tagihan::create([
                    'pelanggan_id' => $pelanggan->pelanggan_id
                ]);

                if (!$tagihan->save()){
                    DB::rollBack();
                    $response = [
                        'msg' => 'gagal membuat akun tagihan',
                        'status_code' => '0010',
                        'error' => ''
                    ];

                    return response()->json($response,400);
                }
            }

            $tagihan = $pelanggan->tagihan;
            $pelanggan->tagihan = $tagihan;

            DB::commit();
            $response = [
                'msg' => 'berhasil membuat akun tagihan',
                'status_code' => '0001',
                'data' => $pelanggan
            ];

            return response()->json($response,201);

        }catch (Exception $e){
            DB::rollBack();
            $response = [
                'msg' => 'berhasil membuat akun tagihan',
                'status_code' => '0001',
                'rror' => $e
            ];

            return response()->json($response,400);
        }
    }

    public function create_tagihan($pelanggan_id,Request $request){

        $mtr_awal = $request->get('mtr_awal');
        $mtr_akhir = $request->get('mtr_akhir');
//        $mtr_jumlah = $request->get('mtr_jumlah');
        $harga_m2 = $request->get('harga_m2');
//        $jml_m2 = $request->get('jumlah_m2');
        $beban = $request->get('beban');
        $hutang = $request->get('hutang');
        $simpanan = $request->get('simpanan');
        $simp_status = $request->get('simp_status');

        $mtr_jumlah = $mtr_akhir - $mtr_awal;
        $jml_m2 = $mtr_jumlah * $harga_m2;


        $pelanggan = Pelanggan::find($pelanggan_id)->first();
        $p_id = $pelanggan->pelanggan_id;

        try {
            DB::beginTransaction();
            $tagihan = Tagihan::where('pelanggan_id',$p_id)->first();
            $tagihan->delete();
            if (!$tagihan->delete()){
                DB::rollBack();
                $response = [
                    'msg' => 'gagal update tagihan',
                    'status_code' => '0012'
                ];

                return response()->json($response,400);
            }

            Tagihan::create([
               'pelanggan_id' => $pelanggan_id,
               'mtr_awal' => $request->get('mtr_awal'),
                'mtr_akhir' => $request->get('mtr_akhir'),
                'mtr_jumlah' => $request->get('mtr_jumlah'),
                'harga_m2' => $request->get('harga_m2'),
                'jml_m2' => $request->get('jumlah_m2'),
                'beban' => $request->get('beban'),
                'hutang' => $request->get('hutang'),
                'simpanan' => $request->get('simpanan'),
                'simp_status' => $request->get('simp_status'),
            ]);

        }catch (Exception $e){

        }
    }
}
