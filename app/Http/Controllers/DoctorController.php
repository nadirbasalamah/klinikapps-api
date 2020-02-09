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
	 * @function getNutritionRecordById(id)
	 * @param id data gizi
	 * @return mendapatkan data gizi berdasarkan id data gizi
	 */
	public function getNutritionRecordById($id)
    {
        $isDataFound = true;
        try {
            $data = Nutrition_record::where('id_record','=',$id)->firstOrFail();
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
	public function updateNutritionRecord($id)
	{
		// $bb = $this->input->post('bb');
		// $tb = $this->input->post('tb');
		// $imt = $bb / pow(($tb / 100),2);
		// $status = $this->checkImt($imt);
		
		// $energi = $this->input->post('energi');
		// $persen_karbohidrat = $this->input->post('persen_karbohidrat');
		// $gram_karbohidrat = ($persen_karbohidrat / 100) * $energi / 4;

		// $persen_protein = $this->input->post('persen_protein');
		// $gram_protein = ($persen_protein / 100) * $energi / 4;
		
		// $persen_lemak = $this->input->post('persen_lemak');
		// $gram_lemak = ($persen_lemak / 100) * $energi / 9;

		// $data = array(
		// 	'id_record' => 0,
		// 	'id_patient' => $id,
		// 	'bb' => $bb,
		// 	'tb' => $tb,
		// 	'lila' => $this->input->post('lila'),
		// 	'imt' => $imt,
		// 	'bbi' => $this->input->post('bbi'),
		// 	'status' => $status,
		// 	'fat' => $this->input->post('fat'),
		// 	'visceral_fat' => $this->input->post('visceral_fat'),
		// 	'muscle' => $this->input->post('muscle'),
		// 	'body_age' => $this->input->post('body_age'),
		// 	'gda' => $this->input->post('gda'), 
		// 	'gdp' => $this->input->post('gdp'), 
		// 	'gd2jpp' => $this->input->post('gd2jpp'), 
		// 	'asam_urat' => $this->input->post('asam_urat'), 
		// 	'trigliserida' => $this->input->post('trigliserida'), 
		// 	'kolesterol' => $this->input->post('kolesterol'), 
		// 	'ldl' => $this->input->post('ldl'), 
		// 	'hdl' => $this->input->post('hdl'), 
		// 	'ureum' => $this->input->post('ureum'), 
		// 	'kreatinin' => $this->input->post('kreatinin'), 
		// 	'sgot' => $this->input->post('sgot'), 
		// 	'sgpt' => $this->input->post('sgpt'), 
		// 	'tensi' => $this->input->post('tensi'), 
		// 	'rr' => $this->input->post('rr'), 
		// 	'suhu' => $this->input->post('suhu'),
		// 	'lainnya' => $this->input->post('lainnya'), 
		// 	'oedema' => $this->input->post('oedema'), 
		// 	'aktivitas' => $this->input->post('aktivitas'), 
		// 	'durasi_olahraga' => $this->input->post('durasi_olahraga'), 
		// 	'jenis_olahraga' => $this->input->post('jenis_olahraga'), 
		// 	'diagnosa_dahulu' => $this->input->post('diagnosa_dahulu'), 
		// 	'diagnosa_skrg' => $this->input->post('diagnosa_skrg'), 
		// 	'nafsu_makan' => $this->input->post('nafsu_makan'), 
		// 	'frekuensi_makan' => $this->input->post('frekuensi_makan'), 
		// 	'alergi' => $this->input->post('alergi'), 
		// 	'makanan_kesukaan' => $this->input->post('makanan_kesukaan'), 
		// 	'dietary_nasi' => preg_replace('~(?<=\s)\s~', '&nbsp;', $this->input->post('dietary_nasi')) , 
		// 	'dietary_lauk_hewani' => $this->input->post('dietary_lauk_hewani'), 
		// 	'dietary_lauk_nabati' => $this->input->post('dietary_lauk_nabati'), 
		// 	'dietary_sayur' => $this->input->post('dietary_sayur'), 
		// 	'dietary_sumber_minyak' => $this->input->post('dietary_sumber_minyak'), 
		// 	'dietary_minuman' => $this->input->post('dietary_minuman'), 
		// 	'dietary_softdrink' => $this->input->post('dietary_softdrink'), 
		// 	'dietary_jus' => $this->input->post('dietary_jus'), 
		// 	'dietary_suplemen' => $this->input->post('dietary_suplemen'), 
		// 	'dietary_lainnya' => $this->input->post('dietary_lainnya'), 
		// 	'lain_lain' => $this->input->post('lain_lain'), 
		// 	'diagnosa' => $this->input->post('diagnosa'),
		// 	'angka_tb_bb' => $this->input->post('angka_tb_bb'),
		// 	'keterangan_tb_bb' => $this->input->post('keterangan_tb_bb'),
		// 	'angka_bb_u' => $this->input->post('angka_bb_u'),
		// 	'keterangan_bb_u' => $this->input->post('keterangan_bb_u'),
		// 	'angka_tb_u' => $this->input->post('angka_tb_u'),
		// 	'keterangan_tb_u' => $this->input->post('keterangan_tb_u'),
		// 	'angka_imt_u' => $this->input->post('angka_imt_u'),
		// 	'keterangan_imt_u' => $this->input->post('keterangan_imt_u'),
		// 	'angka_hc_u' => $this->input->post('angka_hc_u'),
		// 	'keterangan_hc_u' => $this->input->post('keterangan_hc_u'), 
		// 	'energi' => $energi, 
		// 	'keterangan_inter' => $this->input->post('keterangan_inter'),
		// 	'persen_karbohidrat' => $persen_karbohidrat,
		// 	'gram_karbohidrat' => $gram_karbohidrat, 
		// 	'persen_protein' => $persen_protein, 
		// 	'gram_protein' => $gram_protein, 
		// 	'persen_lemak' => $persen_lemak,
		// 	'gram_lemak' => $gram_lemak,
		// 	'mon_date' => $this->input->post('mon_date'),
		// 	'result' => $this->input->post('result')
		// );
		// $result = $this->Nutrition_records_api->addNutritionRecord($data);
		// echo json_encode($result);
	}
	/**
	 * @function getAllArticles()
	 * @return menampilkan seluruh data artikel gizi
	 */
	public function getAllArticles()
	{
		$articles = Article::all();

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
	public function addArticle()
	{
		// $data = array(
		// 	'id' => 0,
		// 	'author' => $this->input->post('author'),
		// 	'type' => $this->input->post('type'),
		// 	'title' => $this->input->post('title'),
		// 	'description' => $this->input->post('description'),
		// 	'source' => $this->input->post('source')
		// );
		// $result = $this->Articles_api->addArticle($data);
		// echo json_encode($result);
	}
	/**
	 * @function deleteArticle(id)
	 * @return menghapus data artikel tertentu
	 */
	public function deleteArticle($id)
	{
		// $result = $this->Articles_api->deleteArticle($id);
		// echo json_encode($result);
	}
	/**
	 * @function updateArticle(id)
	 * @return mengubah data artikel
	 */
	public function updateArticle($id)
	{
		// $data = array(
		// 	'id' => $id,
		// 	'title' => $this->input->post('title'),
		// 	'description' => $this->input->post('description'),
		// 	'source' => $this->input->post('source')
		// );
		// $result = $this->Articles_api->updateArticle($data);
		// echo json_encode($result);
	}
}