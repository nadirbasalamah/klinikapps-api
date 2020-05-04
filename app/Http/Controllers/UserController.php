<?php

namespace App\Http\Controllers;

use App\Antropometry;
use App\Biochemistry;
use App\Clinic;
use App\Dietary;
use App\Diagnose;
use App\Interenvention;
use App\Monitoring;
use App\User;
use App\Nutritionist;
use App\Patient;
use App\FoodMenu;
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

    public function checkPatient($fullname)
	{
		$isDataFound = true;
        try {
            $data = Patient::where('fullname','=',$fullname)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
		}
		return ['status' => $isDataFound, 'id' => $data->id, 'nut_id' => $data->id_nutritionist];
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
                'id_type' => $request->id_type
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
        $isNutritionistFound = true;

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
            if(strpos($request->username,"AG") !== false) {
                try {
                    $nutritionist = Nutritionist::where('username', '=', trim($request->username,"AG_"))->firstOrFail();
                } catch (\Throwable $th) {
                    $isNutritionistFound = false;
                }
                if ($isNutritionistFound) {
                    if (Hash::check($request->password, $nutritionist->password)) {
                        $status = true;
                        $message['success'] = 'Login successfully';
                        $data = $nutritionist->toArray();
                        $code = 200;
                    } else {
                        $message['password'] = "Login failed, invalid password";
                    }
                } else {
                        $message['username'] = "Login failed, invalid username";
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
                        $message['username'] = "Login failed, invalid username";
                }
            }
            }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    /**
	 * @function forgetPassword()
	 * @return melakukan reset password pengguna
	 */
	public function forgetPassword(Request $request) {
		$validator = Validator::make($request->all(), [
            'username' => 'required', 
            'password' => 'required', 
        ]);
        $status = false;
        $message = [];
        $data = null;
        $code = 401;
        $isUserFound = true;
        $isNutritionistFound = true;

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
            if(strpos($request->username,"AG") !== false) {
                try {
                    $nutritionist = Nutritionist::where('username', '=', trim($request->username,"AG_"))->firstOrFail();
                } catch (\Throwable $th) {
                    $isNutritionistFound = false;
                }
                if ($isNutritionistFound) {
                    $nutritionist->password = Hash::make($request->password);
                    $nutritionist->save();
                    $message['success'] = 'nutritionist password updated!';
                    $code = 200;
                    $data = $nutritionist;
                    $status = true;
                } else {
                        $message['username'] = "Reset password failed, invalid nutritionist username";
                }
            } else {
                try {
                    $user = User::where('username', '=', $request->username)->firstOrFail();
                } catch (\Throwable $th) {
                    $isUserFound = false;
                }
                if ($isUserFound) {
                    $user->password = Hash::make($request->password);
                    $user->save();
                    $message['success'] = 'user password updated!';
                    $code = 200;
                    $data = $user;
                    $status = true;
                } else {
                        $message['username'] = "Reset password failed, invalid username";
                }
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
                        $status = true;
                        $code = 200;
                        $data = $user;
                        $message['success'] = 'password updated!';
                    } else {
                        $message['password'] = "Login failed, invalid password";
                    }
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
        $user = User::find($id);
        $patient = $this->checkPatient($user->fullname);
        $isDataFound = true;
        if(!is_null($user)) {
            if($patient['status']) {
                try {
                    $nutritionist_data = Nutritionist::where('id','=',$patient['nut_id'])->firstOrFail();
                    $antropometry_data = Antropometry::where('id_patient','=',$patient['id'])->firstOrFail();
                    $biochemistry_data = Biochemistry::where('id_patient','=',$patient['id'])->firstOrFail();
                    $clinic_data = Clinic::where('id_patient','=',$patient['id'])->firstOrFail();
                    $dietary_data = Dietary::where('id_patient','=',$patient['id'])->firstOrFail();
                    $diagnose_data = Diagnose::where('id_patient','=',$patient['id'])->firstOrFail();
                    $interenvention_data = Interenvention::where('id_patient','=',$patient['id'])->firstOrFail();
                    $monitoring_data = Monitoring::where('id_patient','=',$patient['id'])->firstOrFail();
                } catch (\Throwable $th) {
                    $isDataFound = false;
                }
                if($isDataFound) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Nutrition record found.',
                        'nutritionist_data' => $nutritionist_data,
                        'antropometry_data' => $antropometry_data,
                        'biochemistry_data' => $biochemistry_data,
                        'clinic_data' => $clinic_data,
                        'dietary_data' => $dietary_data,
                        'diagnose_data' => $diagnose_data,
                        'interenvention_data' => $interenvention_data,
                        'monitoring_data' => $monitoring_data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Nutrition record not found.'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Patient not found.'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }

    /**
	 * @function getFoodMenuById(id)
	 * @return menampilkan data rancangan menu makanan berdasarkan id pasien
	 */
    public function getFoodMenuById($id)
    {
        $user = User::find($id);
        $patient = $this->checkPatient($user->fullname);
        $isDataFound = true;
        if(!is_null($user)) {
            if($patient['status']) {
                try {
                    $foodMenuData = FoodMenu::where('id_patient','=',$patient['id'])->firstOrFail();
                } catch (\Throwable $th) {
                    $isDataFound = false;
                }
                if($isDataFound) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Food menu found.',
                        'data' => $foodMenuData,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Food menu not found.'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Patient not found.'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }
}