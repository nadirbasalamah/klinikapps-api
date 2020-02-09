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
	public function editPatient($id)
	{
		// $data = array(
		// 	'id' => $id,
		// 	'fullname' => $this->input->post('fullname'),
		// 	'address' => $this->input->post('address'),
		// 	'phone_number' => $this->input->post('phone_number'),
		// 	'email' => $this->input->post('email'),
		// 	'education' => $this->input->post('education'),
		// 	'job' => $this->input->post('job'),
		// 	'religion' => $this->input->post('religion')
		// );
		// $result = $this->Patients_api->updatePatient($data);
		// echo json_encode($result);
	}
	/**
	 * @function addPatient()
	 * @return menambahkan data pasien baru
	 */
	public function addPatient()
	{
		// $data = array(
		// 	'id_patient' => 0,
		// 	'rm_number' => $this->input->post('rm_number'),
		// 	'rmgizi_number' => $this->input->post('rmgizi_number'),
		// 	'visitdate' => $this->input->post('visitdate'),
		// 	'referral' => $this->input->post('referral'),
		// 	'fullname' => $this->input->post('fullname'),
		// 	'age' => $this->input->post('age'),
		// 	'gender' => $this->input->post('gender'),
		// 	'address' => $this->input->post('address'),
		// 	'phone_number' => $this->input->post('phone_number'),
		// 	'email' => $this->input->post('email'),
		// 	'birthdate' => $this->input->post('birthdate'),
		// 	'education' => $this->input->post('education'),
		// 	'job' => $this->input->post('job'),
		// 	'religion' => $this->input->post('religion')
		// );
		// $result = $this->Patients_api->addPatient($data);
		// echo json_encode($result);
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