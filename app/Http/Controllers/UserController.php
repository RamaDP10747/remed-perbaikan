<?php

namespace App\Http\Controllers;
use App\Helpers\ApiFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        try {
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store (Request $request) {
        try {
            $this->validate($request, [
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role' => 'required|in:admin, staff',
            ]);

            $data = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            ]);

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }
    
    public function show($id) {
        try{
            $data = User::where('id', $id)->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }
    }

    public function edit(User $user)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'username' => 'required',
                'email' => 'required',
                'password' => 'required',
                'role' => 'required|in:admin, staff',
            ]);

            $checkProses = User::where('id', $id)->update([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            if($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengubah data! ');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }
    }

    public function destroy($id)
    {
        try {
            $checkProses = User::where('id', $id)->delete();

                return ApiFormatter::sendResponse(200, 'success', 'Data berhasil dihapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }

    public function trash()
    {
        try {
            $data = User::onlyTrashed()->get();

                return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkProses = User::onlyTrashed()->where('id', $id)->restore();

            if($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data! ');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
            }
    }

    public function deletePermanent($id)
    {
        try {
            $checkProses = User::onlyTrashed()->where('id', $id)->forceDelete();

                return ApiFormatter::sendResponse(200, 'success', 'Berhasil menghapus data secara permanen!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse (400, 'bad request', $err->getMessage());
        }
    }

    public function login(Request $request){
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
            ]);

            // dd($request->email);
            $user = User::where('email', $request->email)->first(); // mencari dan mendapatkan data user berdasarkan email yang digunakan untuk login

            if(!$user) {
                //Jika email tidak terdaftar maka akan dikembalikan response error
                return ApiFormatter::sendResponse(400, false, 'Login Failed! User Does Not Exists');
            } else {
                // Mencocokkan Password yang diinput dengan Password di database
                $isValid = Hash::check($request->password, $user->password);

                if (!$isValid) {
                    // Jika password tidak cocok m aka akan dikembalikan dengan response error
                    return ApiFormatter::sendResponse(400, 'Login Failed! User Does Not Exists');
                } else {
                    // Jika password sesuai selanjutnya akan membuat token
                    // bin2hex digunakan untuk dapat mengonversi string karakter ASCII menjadi nilai heksadesimal
                    // random_bytes menghasilkan byye pseuido-acak yang aman secara kriptografis dengan panjang 40 karakter
                    $generateToken = bin2hex(random_bytes(40));
                    
                    // Token inilah nanti yang digunakan pada proses authentication user yang login
                    $user->update([
                        'token' => $generateToken
                     ]);
    
                    return ApiFormatter::sendResponse(200, 'Login Successfully', $user);
                    }
                } 
            } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, $err->getMessage());
        }
    }

    public function logout(Request $request){
        try {
            $this->validate($request, [
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ApiFormatter::sendResponse(400, 'Login failed! User Doesnt Exist');
            } else {
                if (!$user->token) {
                    return ApiFormatter::sendResponse(400, 'Logout failed! User Doesnt Login Sciene');
                } else {
                    $logout = $user->update(['token' => null]);
                    if ($logout) {
                        return ApiFormatter::sendResponse(200, 'Logout Successfully');
                    }
                }
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(500, false, $err->getMessage());
        }
    }

}