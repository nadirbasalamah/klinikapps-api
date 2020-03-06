<?php

namespace App\Http\Controllers;

use App\Patient;
use App\Article;
use App\Antropometry;
use App\Biochemistry;
use App\Clinic;
use App\Dietary;
use App\Diagnose;
use App\Interenvention;
use App\Monitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
	/**
	 * @function checkImt(imt)
	 * @param double imt pasien
	 * @return menentukan status gizi berdasarkan nilai IMT
	 */
	public function checkImt($imt)
	{
		if ($imt < 18.5) {
			return "underweight";
		} else if($imt >= 18.5 && $imt <= 22.9) {
			return "normal";
		} else if($imt >= 23 && $imt <= 29.9) {
			return "overweight";
		} else {
			return "obese";
		}
	}
	/**
	 * @function checkPatient(id)
	 * @param id pasien
	 * @return menentukan apakah pasien telah terdaftar atau belum
	 */
	public function checkPatient($id)
	{
		$isDataFound = true;
        try {
            $data = Patient::where('id','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
		}
		return $isDataFound;
	}
	/**
	 * @function getNutritionRecordById(id)
	 * @param id data gizi
	 * @return mendapatkan data gizi berdasarkan id data gizi
	 */
	public function getNutritionRecordById($id)
    {
        $isDataFound = true;
        try {
			$antropometry_data = Antropometry::where('id_patient','=',$id)->firstOrFail();
			$biochemistry_data = Biochemistry::where('id_patient','=',$id)->firstOrFail();
			$clinic_data = Clinic::where('id_patient','=',$id)->firstOrFail();
			$dietary_data = Dietary::where('id_patient','=',$id)->firstOrFail();
			$diagnose_data = Diagnose::where('id_patient','=',$id)->firstOrFail();
			$interenvention_data = Interenvention::where('id_patient','=',$id)->firstOrFail();
			$monitoring_data = Monitoring::where('id_patient','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
            return response()->json([
                'status' => true,
                'message' => 'Nutrition record found.',
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
	}

	public function setNutritionist(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'id_nutritionist' => 'required'
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
                    $patient->id_nutritionist = $request->id_nutritionist;
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
	 * @function updateAntropometry(id)
	 * @param id pasien
	 * @return melakukan perubahan data antropometri
	 */
	public function updateAntropometry(Request $request, $id) 
	{
		$status = false;
		$message = [];
		$data = null;
		$code = 400;

		if($this->checkPatient($id)) {
			$validator = Validator::make($request->all(), [
				'bb' => 'required',
				'tb' => 'required',
				'lila' => 'required',
				'bbi' => 'required',
				'fat' => 'required',
				'visceral_fat' => 'required',
				'muscle' => 'required',
				'body_age' => 'required',
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
			}
			else{
				$bb = $request->bb;
				$tb = $request->tb;
				$imt = floatval($bb / pow(($tb / 100),2));
				$imt_status = $this->checkImt($imt);
				
				$isDataFound = true;
				try {
					$nutRecord = Antropometry::where('id_patient','=',$id)->firstOrFail();
				} catch (\Throwable $th) {
					$isDataFound = false;
				}

				if($isDataFound) {
					$nutRecord->bb = $bb;
					$nutRecord->tb = $tb;
					$nutRecord->lila = $request->lila;
					$nutRecord->imt = $imt;
					$nutRecord->bbi = $request->bbi;
					$nutRecord->status = $imt_status;
					$nutRecord->fat = $request->fat;
					$nutRecord->visceral_fat = $request->visceral_fat;
					$nutRecord->muscle = $request->muscle;
					$nutRecord->body_age = $request->body_age;
					$nutRecord->save();

					$status = true;
					$message['success'] = "nutrition record updated successfully";
					$data = $nutRecord->toArray();
					$code = 200;

				} else {
					$nutrition_record = Antropometry::create([
						'id_patient' => $id,
						'bb' => $bb,
						'tb' => $tb,
						'lila' => $request->lila,
						'imt' => $imt,
						'bbi' => $request->bbi,
						'status' => $imt_status,
						'fat' => $request->fat,
						'visceral_fat' => $request->visceral_fat,
						'muscle' => $request->muscle,
						'body_age' => $request->body_age,
					]);
					if($nutrition_record){
						$status = true;
						$message['success'] = "nutrition record added successfully";
						$data = $nutrition_record->toArray();
						$code = 200;
					}
					else{
						$message['error'] = 'nutrition record failed to add.';
					}	
				}
			}
		} else {
			$status = false;
			$message['error'] = "Error, patient data not found";
			$code = 404;
		}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}

	/**
	 * @function updateBiochemistry(id)
	 * @param id pasien
	 * @return melakukan perubahan data biokimia
	 */
	public function updateBiochemistry(Request $request, $id) 
	{
		$status = false;
		$message = [];
		$data = null;
		$code = 400;

		if($this->checkPatient($id)) {
			$validator = Validator::make($request->all(), [
				'gda' => 'required', 
				'gdp' => 'required', 
				'gd2jpp' => 'required', 
				'asam_urat' => 'required', 
				'trigliserida' => 'required', 
				'kolesterol' => 'required', 
				'ldl' => 'required', 
				'hdl' => 'required', 
				'ureum' => 'required', 
				'kreatinin' => 'required', 
				'sgot' => 'required', 
				'sgpt' => 'required', 
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
			}
			else{
				$isDataFound = true;
				try {
					$nutRecord = Biochemistry::where('id_patient','=',$id)->firstOrFail();
				} catch (\Throwable $th) {
					$isDataFound = false;
				}

				if($isDataFound) {
					$nutRecord->gda = $request->gda; 
					$nutRecord->gdp = $request->gdp; 
					$nutRecord->gd2jpp = $request->gd2jpp; 
					$nutRecord->asam_urat = $request->asam_urat; 
					$nutRecord->trigliserida = $request->trigliserida; 
					$nutRecord->kolesterol = $request->kolesterol; 
					$nutRecord->ldl = $request->ldl; 
					$nutRecord->hdl = $request->hdl; 
					$nutRecord->ureum = $request->ureum; 
					$nutRecord->kreatinin = $request->kreatinin; 
					$nutRecord->sgot = $request->sgot; 
					$nutRecord->sgpt = $request->sgpt; 

					$nutRecord->save();

					$status = true;
					$message['success'] = "nutrition record updated successfully";
					$data = $nutRecord->toArray();
					$code = 200;

				} else {
					$nutrition_record = Biochemistry::create([
						'id_patient' => $id,
						'gda' => $request->gda, 
						'gdp' => $request->gdp, 
						'gd2jpp' => $request->gd2jpp, 
						'asam_urat' => $request->asam_urat, 
						'trigliserida' => $request->trigliserida, 
						'kolesterol' => $request->kolesterol, 
						'ldl' => $request->ldl, 
						'hdl' => $request->hdl, 
						'ureum' => $request->ureum, 
						'kreatinin' => $request->kreatinin, 
						'sgot' => $request->sgot, 
						'sgpt' => $request->sgpt, 
					]);
					if($nutrition_record){
						$status = true;
						$message['success'] = "nutrition record added successfully";
						$data = $nutrition_record->toArray();
						$code = 200;
					}
					else{
						$message['error'] = 'nutrition record failed to add.';
					}	
				}
			}
		} else {
			$status = false;
			$message['error'] = "Error, patient data not found";
			$code = 404;
		}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}

	/**
	 * @function updateClinic(id)
	 * @param id pasien
	 * @return melakukan perubahan data klinik
	 */
	public function updateClinic(Request $request, $id) 
	{
		$status = false;
		$message = [];
		$data = null;
		$code = 400;

		if($this->checkPatient($id)) {
			$validator = Validator::make($request->all(), [
				'tensi' => 'required', 
				'rr' => 'required', 
				'suhu' => 'required',
				'lainnya' => 'required', 
				'oedema' => 'required', 
				'aktivitas' => 'required', 
				'durasi_olahraga' => 'required', 
				'jenis_olahraga' => 'required', 
				'diagnosa_dahulu' => 'required', 
				'diagnosa_skrg' => 'required', 
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
			}
			else{
				$isDataFound = true;
				try {
					$nutRecord = Clinic::where('id_patient','=',$id)->firstOrFail();
				} catch (\Throwable $th) {
					$isDataFound = false;
				}

				if($isDataFound) {
					$nutRecord->tensi = $request->tensi; 
					$nutRecord->rr = $request->rr; 
					$nutRecord->suhu = $request->suhu;
					$nutRecord->lainnya = $request->lainnya; 
					$nutRecord->oedema = $request->oedema; 
					$nutRecord->aktivitas = $request->aktivitas; 
					$nutRecord->durasi_olahraga = $request->durasi_olahraga; 
					$nutRecord->jenis_olahraga = $request->jenis_olahraga; 
					$nutRecord->diagnosa_dahulu = $request->diagnosa_dahulu; 
					$nutRecord->diagnosa_skrg = $request->diagnosa_skrg; 
				
					$nutRecord->save();

					$status = true;
					$message['success'] = "nutrition record updated successfully";
					$data = $nutRecord->toArray();
					$code = 200;

				} else {
					$nutrition_record = Clinic::create([
						'id_patient' => $id,
						'tensi' => $request->tensi, 
						'rr' => $request->rr, 
						'suhu' => $request->suhu,
						'lainnya' => $request->lainnya, 
						'oedema' => $request->oedema, 
						'aktivitas' => $request->aktivitas, 
						'durasi_olahraga' => $request->durasi_olahraga, 
						'jenis_olahraga' => $request->jenis_olahraga, 
						'diagnosa_dahulu' => $request->diagnosa_dahulu, 
						'diagnosa_skrg' => $request->diagnosa_skrg, 
					]);
					if($nutrition_record){
						$status = true;
						$message['success'] = "nutrition record added successfully";
						$data = $nutrition_record->toArray();
						$code = 200;
					}
					else{
						$message['error'] = 'nutrition record failed to add.';
					}	
				}
			}
		} else {
			$status = false;
			$message['error'] = "Error, patient data not found";
			$code = 404;
		}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}

	/**
	 * @function updateDietary(id)
	 * @param id pasien
	 * @return melakukan perubahan data dietary
	 */
	public function updateDietary(Request $request, $id) 
	{
		$status = false;
		$message = [];
		$data = null;
		$code = 400;

		if($this->checkPatient($id)) {
			$validator = Validator::make($request->all(), [
				'nafsu_makan' => 'required', 
				'frekuensi_makan' => 'required', 
				'alergi' => 'required', 
				'makanan_kesukaan' => 'required', 
				'dietary_nasi' =>  'required', 
				'dietary_lauk_hewani' => 'required', 
				'dietary_lauk_nabati' => 'required', 
				'dietary_sayur' => 'required', 
				'dietary_sumber_minyak' => 'required', 
				'dietary_minuman' => 'required', 
				'dietary_softdrink' => 'required', 
				'dietary_jus' => 'required', 
				'dietary_suplemen' => 'required', 
				'dietary_lainnya' => 'required', 
				'lain_lain' => 'required', 
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
			}
			else{
				$isDataFound = true;
				try {
					$nutRecord = Dietary::where('id_patient','=',$id)->firstOrFail();
				} catch (\Throwable $th) {
					$isDataFound = false;
				}

				if($isDataFound) {
					$nutRecord->nafsu_makan = $request->nafsu_makan; 
					$nutRecord->frekuensi_makan = $request->frekuensi_makan; 
					$nutRecord->alergi = $request->alergi; 
					$nutRecord->makanan_kesukaan = $request->makanan_kesukaan; 
					$nutRecord->dietary_nasi = $request->dietary_nasi ; 
					$nutRecord->dietary_lauk_hewani = $request->dietary_lauk_hewani; 
					$nutRecord->dietary_lauk_nabati = $request->dietary_lauk_nabati; 
					$nutRecord->dietary_sayur = $request->dietary_sayur; 
					$nutRecord->dietary_sumber_minyak = $request->dietary_sumber_minyak; 
					$nutRecord->dietary_minuman = $request->dietary_minuman; 
					$nutRecord->dietary_softdrink = $request->dietary_softdrink; 
					$nutRecord->dietary_jus = $request->dietary_jus; 
					$nutRecord->dietary_suplemen = $request->dietary_suplemen; 
					$nutRecord->dietary_lainnya = $request->dietary_lainnya; 
					$nutRecord->lain_lain = $request->lain_lain; 
			
					$nutRecord->save();

					$status = true;
					$message['success'] = "nutrition record updated successfully";
					$data = $nutRecord->toArray();
					$code = 200;

				} else {
					$nutrition_record = Dietary::create([
						'id_patient' => $id,
						'nafsu_makan' => $request->nafsu_makan, 
						'frekuensi_makan' => $request->frekuensi_makan, 
						'alergi' => $request->alergi, 
						'makanan_kesukaan' => $request->makanan_kesukaan, 
						'dietary_nasi' => $request->dietary_nasi , 
						'dietary_lauk_hewani' => $request->dietary_lauk_hewani, 
						'dietary_lauk_nabati' => $request->dietary_lauk_nabati, 
						'dietary_sayur' => $request->dietary_sayur, 
						'dietary_sumber_minyak' => $request->dietary_sumber_minyak, 
						'dietary_minuman' => $request->dietary_minuman, 
						'dietary_softdrink' => $request->dietary_softdrink, 
						'dietary_jus' => $request->dietary_jus, 
						'dietary_suplemen' => $request->dietary_suplemen, 
						'dietary_lainnya' => $request->dietary_lainnya, 
						'lain_lain' => $request->lain_lain, 
					]);
					if($nutrition_record){
						$status = true;
						$message['success'] = "nutrition record added successfully";
						$data = $nutrition_record->toArray();
						$code = 200;
					}
					else{
						$message['error'] = 'nutrition record failed to add.';
					}	
				}
			}
		} else {
			$status = false;
			$message['error'] = "Error, patient data not found";
			$code = 404;
		}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}

	/**
	 * @function updateDiagnose(id)
	 * @param id pasien
	 * @return melakukan perubahan data diagnosa
	 */
	public function updateDiagnose(Request $request, $id) 
	{
		$status = false;
		$message = [];
		$data = null;
		$code = 400;

		if($this->checkPatient($id)) {
			$validator = Validator::make($request->all(), [
				'diagnose' => 'required'
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
			}
			else{
				$isDataFound = true;
				try {
					$nutRecord = Diagnose::where('id_patient','=',$id)->firstOrFail();
				} catch (\Throwable $th) {
					$isDataFound = false;
				}

				if($isDataFound) {
					$nutRecord->diagnose = $request->diagnose;

					$nutRecord->save();

					$status = true;
					$message['success'] = "nutrition record updated successfully";
					$data = $nutRecord->toArray();
					$code = 200;

				} else {
					$nutrition_record = Diagnose::create([
						'id_patient' => $id,
						'diagnose' => $request->diagnose,
					]);
					if($nutrition_record){
						$status = true;
						$message['success'] = "nutrition record added successfully";
						$data = $nutrition_record->toArray();
						$code = 200;
					}
					else{
						$message['error'] = 'nutrition record failed to add.';
					}	
				}
			}
		} else {
			$status = false;
			$message['error'] = "Error, patient data not found";
			$code = 404;
		}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}

	/**
	 * @function updateInterenvention(id)
	 * @param id pasien
	 * @return melakukan perubahan data interenvensi
	 */
	public function updateInterenvention(Request $request, $id) 
	{
		$status = false;
		$message = [];
		$data = null;
		$code = 400;

		if($this->checkPatient($id)) {
			$validator = Validator::make($request->all(), [
				'energi' => 'required', 
				'keterangan_inter' => 'required',
				'persen_karbohidrat' => 'required',
				'persen_protein' => 'required', 
				'persen_lemak' => 'required',
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
			}
			else{
				$energi = $request->energi;
				$persen_karbohidrat = $request->persen_karbohidrat;
				$gramKarbo = ($persen_karbohidrat / 100) * $energi / 4;
				$gram_karbohidrat = floatval($gramKarbo);

				$persen_protein = $request->persen_protein;
				$gramProtein = ($persen_protein / 100) * $energi / 4;
				$gram_protein = floatval($gramProtein);
				
				$persen_lemak = $request->persen_lemak;
				$gramLemak = ($persen_lemak / 100) * $energi / 9;
				$gram_lemak = floatval($gramLemak);

				$isDataFound = true;
				try {
					$nutRecord = Interenvention::where('id_patient','=',$id)->firstOrFail();
				} catch (\Throwable $th) {
					$isDataFound = false;
				}

				if($isDataFound) {
					$nutRecord->energi = $energi; 
					$nutRecord->keterangan_inter = $request->keterangan_inter;
					$nutRecord->persen_karbohidrat = $persen_karbohidrat;
					$nutRecord->gram_karbohidrat = $gram_karbohidrat; 
					$nutRecord->persen_protein = $persen_protein; 
					$nutRecord->gram_protein = $gram_protein; 
					$nutRecord->persen_lemak = $persen_lemak;
					$nutRecord->gram_lemak = $gram_lemak;

					$nutRecord->save();

					$status = true;
					$message['success'] = "nutrition record updated successfully";
					$data = $nutRecord->toArray();
					$code = 200;

				} else {
					$nutrition_record = Interenvention::create([
						'id_patient' => $id,
						'energi' => $energi, 
						'keterangan_inter' => $request->keterangan_inter,
						'persen_karbohidrat' => $persen_karbohidrat,
						'gram_karbohidrat' => $gram_karbohidrat, 
						'persen_protein' => $persen_protein, 
						'gram_protein' => $gram_protein, 
						'persen_lemak' => $persen_lemak,
						'gram_lemak' => $gram_lemak,
					]);
					if($nutrition_record){
						$status = true;
						$message['success'] = "nutrition record added successfully";
						$data = $nutrition_record->toArray();
						$code = 200;
					}
					else{
						$message['error'] = 'nutrition record failed to add.';
					}	
				}
			}
		} else {
			$status = false;
			$message['error'] = "Error, patient data not found";
			$code = 404;
		}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}

	/**
	 * @function updateMonitoringResult(id)
	 * @param id pasien
	 * @return melakukan perubahan data antropometri
	 */
	public function updateMonitoring(Request $request, $id) 
	{
		$status = false;
		$message = [];
		$data = null;
		$code = 400;

		if($this->checkPatient($id)) {
			$validator = Validator::make($request->all(), [
				'mon_date' => 'required',
				'result' => 'required',
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
			}
			else{
				$isDataFound = true;
				try {
					$nutRecord = Monitoring::where('id_patient','=',$id)->firstOrFail();
				} catch (\Throwable $th) {
					$isDataFound = false;
				}

				if($isDataFound) {
					$nutRecord->mon_date = $request->mon_date;
					$nutRecord->result = $request->result;
					$nutRecord->return_date = $request->return_date;

					$nutRecord->save();

					$status = true;
					$message['success'] = "nutrition record updated successfully";
					$data = $nutRecord->toArray();
					$code = 200;

				} else {
					$nutrition_record = Monitoring::create([
						'id_patient' => $id,
						'mon_date' => $request->mon_date,
						'result' => $request->result,
						'return_date' => $request->return_date
					]);
					if($nutrition_record){
						$status = true;
						$message['success'] = "nutrition record added successfully";
						$data = $nutrition_record->toArray();
						$code = 200;
					}
					else{
						$message['error'] = 'nutrition record failed to add.';
					}	
				}
			}
		} else {
			$status = false;
			$message['error'] = "Error, patient data not found";
			$code = 404;
		}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}
	/**
	 * @function getAllArticles()
	 * @return menampilkan seluruh data artikel gizi
	 */
	public function getAllArticles()
	{
		$articles = Article::select('*')
		->where('type','=',"article")
		->orderBy('id', 'DESC')
		->get();

		if (count($articles) === 0) {
			return response()->json([
				'status' => false,
				'message' =>'Article data not found.'
			], 404);
		} else {
			return response()->json([
				'status' => true,
				'message' =>'Article data found.',
				'data'    => $articles
			], 200);
		}
	}
	/**
	 * @function getAllGuides()
	 * @return menampilkan seluruh data panduan gizi
	 */
	public function getAllGuides()
	{
		$data = Article::select('*')
		->where('type','=',"guide")
		->orderBy('id', 'DESC')
		->get();

        if(count($data) === 0) {
			return response()->json([
				'status' => false,
                'message' => 'Article data not found.'
            ], 404);
        } else {
			return response()->json([
				'status' => true,
				'message' => 'Article data found.',
				'data' => $data
			], 200);
        }
	}
	/**
	 * @function getArticleById(id)
	 * @return menampilkan data artikel tertentu
	 */
	public function getArticleById($id)
	{
		$isDataFound = true;
        try {
            $data = Article::where('id','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
            return response()->json([
                'status' => true,
                'message' => 'Article data found.',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Article data not found.'
            ], 404);
        }
	}
	/**
	 * @function addArticle()
	 * @return menambahkan data artikel gizi terbaru
	 */
	public function addArticle(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'author' => 'required',
			'type' => 'required',
			'title' => 'required',
			'description' => 'required',
			'source' => 'required',
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
				$article = Article::create([
					'author' => $request->author,
					'type' => $request->type,
					'title' => $request->title,
					'description' => $request->description,
					'source' => $request->source
				]);
				if($article){
					$status = true;
					$message['success'] = "article added successfully";
					$data = $article->toArray();
					$code = 200;
				}
				else{
					$message['error'] = 'article failed to add';
				}
			}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}
	/**
	 * @function deleteArticle(id)
	 * @return menghapus data artikel tertentu
	 */
	public function deleteArticle($id)
	{
		$isDataFound = true;
        try {
            $data = Article::where('id','=',$id)->firstOrFail();
        } catch (\Throwable $th) {
            $isDataFound = false;
        }
        if($isDataFound) {
			$data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Article deleted successfully.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Article not found.'
            ], 404);
        }
	}
	/**
	 * @function editArticle(id)
	 * @return mengubah data artikel
	 */
	public function editArticle(Request $request, $id)
	{
		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'description' => 'required',
			'source' => 'required',
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
				$article = Article::find($id);
                if (!is_null($article)) {
                    $article->title = $request->title;
                    $article->description = $request->description;
					$article->source = $request->source;

                    $article->save();
                    
                    $message['success'] = 'article updated!';
                    $code = 200;
					$data = $article;
                    $status = true;
                } else {
                    $message['error'] = "Error, article not found";
                    $code = 404;
                }
			}
			return response()->json([
				'status' => $status,
				'message' => $message,
				'data' => $data
			], $code);
	}
}