<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request) {
        $validations = array(
            'limit' => 'sometimes|numeric',
        );

        $validatedData = Validator::make($request->all(), $validations);
        if ($validatedData->fails()) {
            return response([
                'status' => 'error',
                'message' => $validatedData->errors(),
                'error' => 'invalid_parameter'
            ], 400);
        }

        $limit = $request->get('limit', 20);

        $tags = Tags::query()
            ->orderByDesc('comments_count')
            ->paginate(intval($limit));

        return response($tags);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function search(Request $request) {
        $validations = array(
            'limit' => 'sometimes|numeric',
            'search' => 'required|string|min:3'
        );

        $validatedData = Validator::make($request->all(), $validations);
        if ($validatedData->fails()) {
            return response([
                'status' => 'error',
                'message' => $validatedData->errors(),
                'error' => 'invalid_parameter'
            ], 400);
        }

        $limit = $request->get('limit', 20);
        $search = strtolower($request->get('search'));

        $tags = Tags::query()
            ->where('name', 'like', "%$search%")
            ->orderBy('comments_count', 'desc')
            ->paginate(intval($limit));

        return response($tags);
    }
}
