<?php

namespace App\Http\Controllers;

use App\Nutrition_record;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        //test only!
        $users = User::all();

        return response()->json([
            'success' => true,
            'message' =>'Data found.',
            'data'    => $users
        ], 200);
    }
    /**
	 * @function registerUser()
	 * @return melakukan registrasi pengguna baru
	 */
	public function registerUser(Request $request) {

    $validator = Validator::make($request->all(), [
        'fullname' => 'required|string|regex:/^[a-z .\-]+$/i',
        'username' => 'required|string|regex:/^[a-z .\-]+$/i',
        'password' => 'required|string|min:6',
        'birthdate' => 'required',
        'gender' => 'required|string',
        'age' => 'required|integer',
        'phone_number' => 'required|string|regex:/^[0-9 .\-]+$/i',
        'email' => 'required|string', 
        'address' => 'required|string',
        'id_number' => 'required|string|regex:/^[0-9 .\-]+$/i',
        'id_type' => 'required'
    ]);
    
        $status = false;
        $message = [];
        $data = null;
        $code = 400;
        $role = 'user';

        if ($validator->fails()) { 
            $errors = $validator->errors();
            $messages = [];
            $fields = [];
            $i = 0;
            foreach ($errors->all() as $msg) {
                array_push($messages,$msg);
                $fields[$i] = explode(" ",$messages[$i]);
                $message[$fields[$i][1]] = $messages[$i];
                $i++;
            }
        }
        else{
            if(strpos($request->fullname, 'Dr.') !== false) {
                $role = 'doctor';
            }
            $user = User::create([
                'role' => $role,
                'fullname' => $request->fullname,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'birthdate' => $request->birthdate,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'id_number' => $request->id_number,
                'id_type' => $request->id_type,
                'profile_picture' => "default.png"
            ]);
            if($user){
                $status = true;
                $message['success'] = "register successfully";
                $data = $user->toArray();
                $code = 200;
            }
            else{
                $message['username'] = 'register failed, username already exist';
            }
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    /**
	 * @function loginUser()
	 * @return melakukan login pengguna
	 */
	public function loginUser(Request $request) {
		$validator = Validator::make($request->all(), [
            'username' => 'required', 
            'password' => 'required', 
        ]);
        $status = false;
        $message = [];
        $data = null;
        $code = 401;
        $isUserFound = true;

        if ($validator->fails()) { 
            $errors = $validator->errors();            
            $messages = [];
            $fields = [];
            $i = 0;
            foreach ($errors->all() as $msg) {
                array_push($messages,$msg);
                $fields[$i] = explode(" ",$messages[$i]);
                $message[$fields[$i][1]] = $messages[$i];
                $i++;
            }
        } else {
                try {
                    $user = User::where('username', '=', $request->username)->firstOrFail();
                } catch (\Throwable $th) {
                    $isUserFound = false;
                }
                if ($isUserFound) {
                    if (Hash::check($request->password, $user->password)) {
                        $status = true;
                        $message['success'] = 'Login successfully';
                        $data = $user->toArray();
                        $code = 200;
                    } else {
                        $message['password'] = "Login failed, invalid password";
                    }
                } else {
                        $message['nim'] = "Login failed, invalid NIM";
                }
            }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    /**
     * @function editProfile(id)
     * @return menyimpan perubahan data profil pengguna
     */
	public function editProfile(Request $request, $id) {
        $message = [];
        $data = [];
        $code = 400;
        $status = false;
            $validator = Validator::make($request->all(), [
                'username' => 'required|regex:/^[a-z .\-]+$/i',
                'phone_number' => 'required|regex:/^[0-9 .\-]+$/i',
                'email' => 'required',
                'address' => 'required'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                
                $messages = [];
                $fields = [];
                $i = 0;

                foreach ($errors->all() as $msg) {
                    array_push($messages,$msg);
                    $fields[$i] = explode(" ",$messages[$i]);
                    $message[$fields[$i][1]] = $messages[$i];
                    $i++;
                }
            } else {
                $user = User::find($id);
                if (!is_null($user)) {
                    $user->username = $request->username;
                    $user->phone_number = $request->phone_number;
                    $user->email = $request->email;
                    $user->address = $request->address;
                    $user->save();
                    
                    $message['success'] = 'user updated!';
                    $code = 200;
                    $data = $user;
                    $status = true;
                } else {
                    $message['error'] = "Error, user not found";
                    $code = 404;
                }
            }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    /**
     * @function changePassword(id)
     * @return menyimpan perubahan password pengguna
     */
	public function changePassword(Request $request, $id) {
        $message = [];
        $data = [];
        $code = 400;
        $status = false;
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|min:6',
                'new_password' => 'required|min:6'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                
                $messages = [];
                $fields = [];
                $i = 0;

                foreach ($errors->all() as $msg) {
                    array_push($messages,$msg);
                    $fields[$i] = explode(" ",$messages[$i]);
                    $message[$fields[$i][1]] = $messages[$i];
                    $i++;
                }
            } else {
                $user = User::find($id);
                if (!is_null($user)) {
                    if (Hash::check($request->old_password, $user->password)) {
                        $user->password = Hash::make($request->new_password);
                        $user->save();
                    } else {
                        $message['password'] = "Login failed, invalid password";
                    }
                    $message['success'] = 'password updated!';
                    $code = 200;
                    $data = $user;
                    $status = true;
                } else {
                    $message['error'] = "Error, user not found";
                    $code = 404;
                }
            }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
	}
	/**
	 * @function getUserById(id)
	 * @return menampilkan data profil pengguna berdasarkan id pengguna
	 */
    public function getUserById($id)
    {
		$isDataFound = true;
        try {
            $data = User::where('id','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
            return response()->json([
                'status' => true,
                'message' => 'User found.',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }
	/**
	 * @function getNutritionRecordById(id)
	 * @return menampilkan data rekaman gizi berdasarkan id pasien
	 */
    public function getNutritionRecordById($id)
    {
        $isDataFound = true;
        try {
            $data = Nutrition_record::where('id_patient','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
            return response()->json([
                'status' => true,
                'message' => 'Nutrition record found.',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Nutrition record not found.'
            ], 404);
        }
    }
}