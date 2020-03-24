<?php

namespace App\Http\Controllers;

use App\Master;
use App\Pelanggan;
use App\Rekening;
use App\Tagihan;
use Illuminate\Http\Request;
use Validator;
use DB;
use Session;
use Exception;

class DataController extends Controller
{
    public function get_rekening($pelanggan_id){
        $rekening = Rekening::where('pelanggan_id',$pelanggan_id)->first();
        return response()->json($rekening,200);
    }

    public function all_data_master(){
        $detail = Master::get();
        if (empty($detail)){
            $response = [
                'msg' => 'data masih kosong',
                'status_code' => '0013',
                'data' => ''
            ];

            return response()->json($response,404);
        }
        $response = [
            'msg' => 'list data master',
            'status_code' => '0001',
            'data' => $detail
        ];

        return response()->json($response,200);
    }

    public function update_data_master(Request $request){

        $validator = Validator::make($request->all(),[
            'harga_m2_msy' => 'required',
            'harga_m2_brh' => 'required',
            'beban' => 'required'
        ]);

        if ($validator->fails()){
            $response = [
                'msg' => 'error when validate',
                'status_code' => '0002',
                'error' => $validator->errors()->toJson()
            ];

            return response()->json($response,400);
        }

        $data = Master::first();
        try {
            DB::beginTransaction();
            $data->delete();

            $newMaster = Master::create([
                'harga_m2_msy' => $request->get('harga_m2_msy'),
                'harga_m2_brh' => $request->get('harga_m2_brh'),
                'beban' => $request->get('beban'),
            ]);

            if (!$newMaster->save()){
                DB::rollBack();
                $response = [
                    'msg' => 'gagal saat perbarui data',
                    'status_code' => '0015',
                    'error' => ''
                ];

                return response()->json($response,400);
            }

        } catch (Exception $e) {
            DB::rollBack();
            $response = [
                'msg' => 'gagal saat perbarui data',
                'status_code' => '0014',
                'error' => $e
            ];

            return response()->json($response,400);
        }

        DB::commit();
        $response = [
            'msg' => 'berhasil perbarui data',
            'status_code' => '0001',
            'data' => $newMaster
        ];

        return response()->json($response,201);
    }

    public function index(){
        $detail = Pelanggan::join('tagihans','pelanggans.pelanggan_id','=','tagihans.pelanggan_id')
            ->join('rekenings','pelanggans.pelanggan_id','=','rekenings.pelanggan_id')
            ->where('tagihans.deleted_at','=',null)
            ->where('rekenings.deleted_at','=',null)
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
            'telfon' => 'required',
            'hutang' => 'required',
            'simpanan' => 'required',
        ]);

        $hutang = $request->get('hutang');
        $simpanan = $request->get('simpanan');

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
                    'pelanggan_id' => $pelanggan->pelanggan_id,
                ]);

                $rekening = Rekening::create([
                    'pelanggan_id' => $pelanggan->pelanggan_id,
                    'hutang' => $hutang,
                    'simpanan' => $simpanan,
                    'total_pembayaran' => $hutang
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

            $d_tagihan = $pelanggan->tagihan;
            $pelanggan->tagihan = $d_tagihan;
            $pelanggan->rekening = $rekening;

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

//    public function create_tagihan($pelanggan_id,Request $request){
//        $mtr_awal = $request->get('mtr_awal');
//        $mtr_akhir = $request->get('mtr_akhir');
//        $hutang = $request->get('hutang');
//        $simpanan = $request->get('simpanan');
//        $simp_status = $request->get('simp_status');
//
//        $dataMaster = Master::first();
//        $pelanggan = Pelanggan::where('pelanggan_id',$pelanggan_id)->first();
//        $p_id = $pelanggan->pelanggan_id;
//        $p_hutang = $pelanggan->p_hutang;
//        $p_simpanan = $pelanggan->p_simpanan;
//
//        if ($pelanggan->level == 'masyarakat'){
//            $harga_m2 = $dataMaster->harga_m2_msy;
//        }elseif ($pelanggan->level == 'buruh'){
//            $harga_m2 = $dataMaster->harga_m2_brh;
//        }
//        $mtr_jumlah = $mtr_akhir - $mtr_awal;
//        $jml_m2 = $mtr_jumlah * $harga_m2;
//        $jml_akhir = $jml_m2 + $dataMaster->beban;
//
//        try {
//            DB::beginTransaction();
//            $tagihan = Tagihan::where('pelanggan_id',$p_id)->first();
//            $tagihan->delete();
//
//            if (!$tagihan->delete()){
//                DB::rollBack();
//                $response = [
//                    'msg' => 'gagal update tagihan',
//                    'status_code' => '0012'
//                ];
//
//                return response()->json($response,400);
//            }
//
//            $newTagihan = Tagihan::create([
//               'pelanggan_id' => $pelanggan_id,
//               'mtr_awal' => $request->get('mtr_awal'),
//                'mtr_akhir' => $request->get('mtr_akhir'),
//                'mtr_jumlah' => $mtr_jumlah,
//                'harga_m2' => $harga_m2,
//                'jml_m2' => $jml_m2,
//                'beban' => $dataMaster->beban,
//                'total_tagihan' => $jml_akhir
//            ]);
//
//            DB::commit();
//            return $newTagihan;
//
//        }catch (Exception $e){
//
//        }
//    }

    public function create_tagihan($pelanggan_id,Request $request){

        $data = array();

        $mtr_awal = $request->get('mtr_awal');
        $mtr_akhir = $request->get('mtr_akhir');

        $dataMaster = Master::first();
        $pelanggan = Pelanggan::where('pelanggan_id',$pelanggan_id)->first();

        if ($pelanggan->level == 'masyarakat'){
            $harga_m2 = $dataMaster->harga_m2_msy;
        }elseif ($pelanggan->level == 'buruh'){
            $harga_m2 = $dataMaster->harga_m2_brh;
        }
        $mtr_jumlah = $mtr_akhir - $mtr_awal;
        $jml_m2 = $mtr_jumlah * $harga_m2;
        $jml_akhir = $jml_m2 + $dataMaster->beban;

        $data['pelanggan_id'] = $pelanggan_id;
        $data['nama_pelanggan'] = $pelanggan->nama;
        $data['mtr_awal'] = $mtr_awal;
        $data['mtr_akhir'] = $mtr_akhir;
        $data['mtr_jumlah'] = $mtr_jumlah;
        $data['harga_m2'] = $harga_m2;
        $data['jml_m2'] = $jml_m2;
        $data['beban'] = $dataMaster->beban;
        $data['total_tagihan'] = $jml_akhir;

        Session::put('data',$data);

        if (!Session::has('coba')){
            $response = [
                'msg' => 'data gagal di catat',
                'status_code' => '0016',
                'data' => ''
            ];

            return response()->json($response,400);
        }

        $response = [
            'msg' => 'data berhasil di catat',
            'status_code' => '0001',
            'data' => $data
        ];

        return response()->json($response,201);
    }
}
