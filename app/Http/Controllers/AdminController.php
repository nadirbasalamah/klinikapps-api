<?php

namespace App\Http\Controllers;

use App\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            $data = Patient::where('id_patient','=',$id)->firstOrFail();
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
	public function deletePatient($id)
	{
		// $result = $this->Patients_api->deletePatient($id);
		// echo json_encode($result);
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
			'email' => 'required',
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
					$patient->email = $request->email;
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
			'email' => 'required',
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
						'email' => $request->email,
						'birthdate' => $request->birthdate,
						'profile_picture' => 'default.png', //TODO: will be replaced with uploaded pict
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
}