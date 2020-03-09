<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
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
	 * @function getArticleByTitle(title)
	 * @return menampilkan seluruh data artikel gizi berdasarkan judul
	 */
	public function getArticleByTitle($title)
	{
		$articles = Article::select('*')
		->where('title','LIKE',"%".$title."%")
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