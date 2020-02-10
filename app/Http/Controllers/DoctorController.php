<?php

namespace App\Http\Controllers;

use App\Patient;
use App\Nutrition_record;
use App\Article;
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
            $data = Nutrition_record::where('id','=',$id)->firstOrFail();
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
	/**
	 * @function updateNutritionRecord(id)
	 * @param id pasien
	 * @return melakukan perubahan data gizi pada pasien tertentu
	 */
	public function updateNutritionRecord(Request $request, $id) //TODO: test this function!
	{
		$status = false;
		$message = [];
		$data = null;
		$code = 400;

		if($this->checkPatient($id)) {
			$validator = Validator::make($request->all(), [
				'bb' => 'required|numeric',
				'tb' => 'required|numeric',
				'lila' => 'required|numeric',
				'bbi' => 'required|numeric',
				'fat' => 'required|numeric',
				'visceral_fat' => 'required|numeric',
				'muscle' => 'required|numeric',
				'body_age' => 'required|numeric',
				'gda' => 'required|numeric', 
				'gdp' => 'required|numeric', 
				'gd2jpp' => 'required|numeric', 
				'asam_urat' => 'required|numeric', 
				'trigliserida' => 'required|numeric', 
				'kolesterol' => 'required|numeric', 
				'ldl' => 'required|numeric', 
				'hdl' => 'required|numeric', 
				'ureum' => 'required|numeric', 
				'kreatinin' => 'required|numeric', 
				'sgot' => 'required|numeric', 
				'sgpt' => 'required|numeric', 
				'tensi' => 'required|numeric', 
				'rr' => 'required|numeric', 
				'suhu' => 'required|numeric',
				'lainnya' => 'required', 
				'oedema' => 'required|numeric', 
				'aktivitas' => 'required', 
				'durasi_olahraga' => 'required', 
				'jenis_olahraga' => 'required', 
				'diagnosa_dahulu' => 'required', 
				'diagnosa_skrg' => 'required', 
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
				'diagnosa' => 'required',
				'angka_tb_bb' => 'required|numeric',
				'keterangan_tb_bb' => 'required',
				'angka_bb_u' => 'required|numeric',
				'keterangan_bb_u' => 'required',
				'angka_tb_u' => 'required|numeric',
				'keterangan_tb_u' => 'required',
				'angka_imt_u' => 'required|numeric',
				'keterangan_imt_u' => 'required',
				'angka_hc_u' => 'required|numeric',
				'keterangan_hc_u' => 'required', 
				'energi' => 'required|numeric', 
				'keterangan_inter' => 'required',
				'persen_karbohidrat' => 'required|numeric',
				'persen_protein' => 'required|numeric', 
				'persen_lemak' => 'required|numeric',
				'mon_date' => 'required',
				'result' => 'required'
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
				$imt = $bb / pow(($tb / 100),2);
				$status = $this->checkImt($imt);
				
				$energi = $request->energi;
				$persen_karbohidrat = $request->persen_karbohidrat;
				$gram_karbohidrat = ($persen_karbohidrat / 100) * $energi / 4;

				$persen_protein = $request->persen_protein;
				$gram_protein = ($persen_protein / 100) * $energi / 4;
				
				$persen_lemak = $request->persen_lemak;
				$gram_lemak = ($persen_lemak / 100) * $energi / 9;

				$nutrition_record = Nutrition_record::create([
					'id_patient' => $id,
					'bb' => $bb,
					'tb' => $tb,
					'lila' => $request->lila,
					'imt' => $imt,
					'bbi' => $request->bbi,
					'status' => $status,
					'fat' => $request->fat,
					'visceral_fat' => $request->visceral_fat,
					'muscle' => $request->muscle,
					'body_age' => $request->body_age,
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
					'diagnosa' => $request->diagnosa,
					'angka_tb_bb' => $request->angka_tb_bb,
					'keterangan_tb_bb' => $request->keterangan_tb_bb,
					'angka_bb_u' => $request->angka_bb_u,
					'keterangan_bb_u' => $request->keterangan_bb_u,
					'angka_tb_u' => $request->angka_tb_u,
					'keterangan_tb_u' => $request->keterangan_tb_u,
					'angka_imt_u' => $request->angka_imt_u,
					'keterangan_imt_u' => $request->keterangan_imt_u,
					'angka_hc_u' => $request->angka_hc_u,
					'keterangan_hc_u' => $request->keterangan_hc_u, 
					'energi' => $energi, 
					'keterangan_inter' => $request->keterangan_inter,
					'persen_karbohidrat' => $persen_karbohidrat,
					'gram_karbohidrat' => $gram_karbohidrat, 
					'persen_protein' => $persen_protein, 
					'gram_protein' => $gram_protein, 
					'persen_lemak' => $persen_lemak,
					'gram_lemak' => $gram_lemak,
					'mon_date' => $request->mon_date,
					'result' => $request->result
				]);
				if($nutrition_record){
					$status = true;
					$message['success'] = "nutrition record updated successfully";
					$data = $nutrition_record->toArray();
					$code = 200;
				}
				else{
					$message['error'] = 'nutrition record failed to update.';
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