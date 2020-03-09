<?php

namespace App\Http\Controllers;

use App\Patient;
use App\Nutritionist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
	 * @function getAllPatients()
	 * @return mendapatkan data seluruh pasien
	 */
	public function getAllPatients()
	{
		$patients = Patient::all();

		if (count($patients) === 0) {
			return response()->json([
				'status' => false,
				'message' =>'Data not found.'
			], 404);
		} else {
			return response()->json([
				'status' => true,
				'message' =>'Data found.',
				'data'    => $patients
			], 200);
		}
	}
	/**
	 * @function getPatientById(id)
	 * @param id pasien
	 * @return mendapatkan data pasien berdasarkan id pasien
	 */
	public function getPatientById($id)
	{
		$isDataFound = true;
        try {
            $data = Patient::where('id','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
            return response()->json([
                'status' => true,
                'message' => 'Patient data found.',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Patient data not found.'
            ], 404);
        }
	}
	/**
	 * @function deletePatient(id)
	 * @param id pasien
	 * @return menghapus data pasien tertentu
	 */
	public function deletePatient(Request $request, $id)
	{
		$isDataFound = true;
        try {
            $data = Patient::where('id','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
			$data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Patient data deleted successfully.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Patient data not found.'
            ], 404);
        }
	}
	/**
	 * @function editPatient(id)
	 * @param id pasien
	 * @return menyimpan perubahan data diri pasien
	 */
	public function editPatient(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'address' => 'required', 
			'phone_number' => 'required|string|regex:/^[0-9 .\-]+$/i',
			'education' => 'required',
			'job' => 'required',
			'religion' => 'required'
		]);
		
			$status = false;
			$message = [];
			$data = null;
			$code = 400;
	
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
				$patient = Patient::find($id);
                if (!is_null($patient)) {
                    $patient->address = $request->address;
                    $patient->phone_number = $request->phone_number;
					$patient->education = $request->education;
					$patient->job = $request->job;
					$patient->religion = $request->religion;

                    $patient->save();
                    
                    $message['success'] = 'patient data updated!';
                    $code = 200;
					$data = $patient;
                    $status = true;
                } else {
                    $message['error'] = "Error, patient not found";
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
	 * @function addPatient()
	 * @return menambahkan data pasien baru
	 */
	public function addPatient(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'rm_number' => 'required',
			'rmgizi_number' => 'required',
			'visitdate' => 'required',
			'referral' => 'required',
			'fullname' => 'required|string|regex:/^[a-z .\-]+$/i',
			'age' => 'required|integer',
			'gender' => 'required',
			'address' => 'required', 
			'phone_number' => 'required|string|regex:/^[0-9 .\-]+$/i',
			'birthdate' => 'required',
			'education' => 'required',
			'job' => 'required',
			'religion' => 'required'
		]);
		
			$status = false;
			$message = [];
			$data = null;
			$code = 400;
	
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
				$patient = Patient::create([
						'rm_number' => $request->rm_number,
						'rmgizi_number' => $request->rmgizi_number,
						'visitdate' => $request->visitdate,
						'referral' => $request->referral,
						'fullname' => $request->fullname,
						'age' => $request->age,
						'gender' => $request->gender,
						'address' => $request->address,
						'phone_number' => $request->phone_number,
						'birthdate' => $request->birthdate,
						'education' => $request->education,
						'job' => $request->job,
						'religion' => $request->religion
				]);
				if($patient){
					$status = true;
					$message['success'] = "patient added successfully";
					$data = $patient->toArray();
					$code = 200;
				}
				else{
					$message['error'] = 'patient failed to add';
				}
			}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}
	/**
	 * @function getPatientByName(fullname)
	 * @return menampilkan data pasien berdasarkan nama pasien yang dicari
	 */
	public function getPatientByName($fullname)
	{
		$data = Patient::select('*')
		->where('fullname','LIKE',"%".$fullname."%")
		->orderBy('fullname', 'DESC')
		->get();

        if(count($data) === 0) {
			return response()->json([
				'status' => false,
                'message' => 'Patient data not found.'
            ], 404);
        } else {
			return response()->json([
				'status' => true,
				'message' => 'Patient data found.',
				'data' => $data
			], 200);
        }
	}
	/**
	 * @function getAllNutritionists()
	 * @return mendapatkan data seluruh ahli gizi
	 */
	public function getAllNutritionists()
	{
		$nutritionists = Nutritionist::all();

		if (count($nutritionists) === 0) {
			return response()->json([
				'status' => false,
				'message' =>'Data not found.'
			], 404);
		} else {
			return response()->json([
				'status' => true,
				'message' =>'Data found.',
				'data'    => $nutritionists
			], 200);
		}
	}
	/**
	 * @function getNutritionistById(id)
	 * @param id ahli gizi
	 * @return mendapatkan data ahli gizi berdasarkan id ahli gizi
	 */
	public function getNutritionistById($id)
	{
		$isDataFound = true;
        try {
            $data = Nutritionist::where('id','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
            return response()->json([
                'status' => true,
                'message' => 'Nutritionist data found.',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Nutritionist data not found.'
            ], 404);
        }
	}
	/**
	 * @function deleteNutritionist(id)
	 * @param id ahli gizi
	 * @return menghapus data ahli gizi tertentu
	 */
	public function deleteNutritionist(Request $request, $id)
	{
		$isDataFound = true;
        try {
            $data = Nutritionist::where('id','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
			$data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Nutritionist data deleted successfully.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Nutritionist data not found.'
            ], 404);
        }
	}
	/**
	 * @function editNutritionist(id)
	 * @param id ahli gizi
	 * @return menyimpan perubahan data ahli gizi
	 */
	public function editNutritionist(Request $request, $id)
	{
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
                $user = Nutritionist::find($id);
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
                $user = Nutritionist::find($id);
                if (!is_null($user)) {
                    if (Hash::check($request->old_password, $user->password)) {
                        $user->password = Hash::make($request->new_password);
                        $user->save();
                    } else {
                        $message['password'] = "Password change failed, invalid password";
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
	 * @function addNutritionist()
	 * @return menambahkan data ahli gizi baru
	 */
	public function addNutritionist(Request $request)
	{
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
			'nip' => 'required|string|regex:/^[0-9 .\-]+$/i'
		]);
		
			$status = false;
			$message = [];
			$data = null;
			$code = 400;

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
				$user = Nutritionist::create([
					'fullname' => $request->fullname,
					'username' => $request->username,
					'password' => Hash::make($request->password),
					'birthdate' => $request->birthdate,
					'gender' => $request->gender,
					'age' => $request->age,
					'phone_number' => $request->phone_number,
					'email' => $request->email,
					'address' => $request->address,
					'nip' => $request->nip
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
	 * @function getNutritionistByName(fullname)
	 * @return menampilkan data ahli gizi berdasarkan nama ahli gizi yang dicari
	 */
	public function getNutritionistByName($fullname)
	{
		$data = Nutritionist::select('*')
		->where('fullname','LIKE',"%".$fullname."%")
		->orderBy('fullname', 'DESC')
		->get();

        if(count($data) === 0) {
			return response()->json([
				'status' => false,
                'message' => 'Nutritionist data not found.'
            ], 404);
        } else {
			return response()->json([
				'status' => true,
				'message' => 'Nutritionist data found.',
				'data' => $data
			], 200);
        }
	}
}