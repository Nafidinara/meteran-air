<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Hash;
use Validator;
use Exception;
use DB;
use Session;

class UserController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed'
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
            $user = User::create([
                'username' => $request->get('username'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            if (!$user->save()){
                DB::rollBack();
                $response = [
                    'msg' => 'error menambahkan user',
                    'status_code' => '0003',
                    'error' => ''
                ];

                return response()->json($response,500);
            }

        } catch (Exception $e){
            DB::rollBack();
            $response = [
                'msg' => 'error menambahkan user',
                'status_code' => '0004',
                'error' => $e
            ];

            return response()->json($response,500);
        }

        $response = [
            'msg' => 'berhasil menambahkan user',
            'status_code' => '0001',
            'data' => $user
        ];

        DB::commit();
        return response()->json($response,201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()){
            $response = [
                'msg' => 'error when validate',
                'status_code' => '0002',
                'error' => $validator->errors()->toJson()
            ];

            return response()->json($response,400);
        }

        $username = $request->get('username');
        $password = $request->get('password');

        $user = User::where('username',$username)->first();

        if ($user){
            if (Hash::check($password,$user->password)){
                $response = [
                    'msg' => 'berhasil login',
                    'status_code' => '0001',
                    'data' => $user
                ];
                Session::put('login',$user);
                return response()->json($response,200);
            }else{
                $response = [
                    'msg' => 'password salah, periksa kembali!',
                    'status_code' => '0005',
                    'data' => ''
                ];

                return response()->json($response,404);
            }
        }else{
            $response = [
                'msg' => 'username salah, periksa kembali!',
                'status_code' => '0006',
                'data' => $user
            ];

            return response()->json($response,404);
        }
    }

    public function update($user_id, Request $request){
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'email' => 'required'
        ]);

        if ($validator->fails()){
            $response = [
                'msg' => 'error when validate',
                'status_code' => '0002',
                'error' => $validator->errors()->toJson()
            ];

            return response()->json($response,400);
        }

        $username = $request->get('username');
        $email = $request->get('email');

        try {
            DB::beginTransaction();
            $user = User::find($user_id);
            $user->username = $username;
            $user->email = $email;
            $user->update();

            if (!$user->update()){
                DB::rollBack();
                $response = [
                    'msg' => 'gagal update data',
                    'status_code' => '0007',
                ];
                return response()->json($response,400);
            }else{
                $response = [
                    'msg' => 'data berhasil di update',
                    'status_code' => '0001',
                    'data' => $user
                ];
                DB::commit();
                return response()->json($response,200);
            }

        } catch (Exception $e){
            DB::rollBack();
            $response = [
                'msg' => 'gagal update data',
                'status_code' => '0008',
                'error' => $e
            ];
            return response()->json($response,400);
        }

    }
}
