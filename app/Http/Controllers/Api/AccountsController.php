<?php


namespace App\Http\Controllers\Api;


use App\Accounts;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountsController extends Controller
{

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

        $accounts = Accounts::query()
            ->where('public_name', 'like', "/.*$search.*/")
            ->orderBy('follower_count')
            ->paginate(intval($limit));
        $accounts->appends($request->except('page'));

        return response($accounts);
    }
}
