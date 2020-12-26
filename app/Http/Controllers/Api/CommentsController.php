<?php


namespace App\Http\Controllers\Api;


use App\Comments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{

    public function feed(Request $request) {
        $validations = array(
            'following' => 'required|array',
            'following.*' => 'sometimes|string|distinct',
            'search' => 'sometimes|string',
            'adult' => 'sometimes|boolean',
            'download' => 'sometimes|string',
            'license' => 'sometimes|numeric',
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

        $limit = intval($request->get('limit', 20));
        $adult = boolval($request->get('adult', 1));
        $download = $request->get('download', false);
        $license = intval($request->get('license', 0));
        $search = $request->get('search', false);
        $following = $request->get('following');

        $query = Comments::query();

        //Only avoid return adult content if field $adult = false (Not show adult content)
        if (!$adult) {
            $query->where('adult', false);
        }

        if ($download) {
            $query->where('download', $download);
        }

        if ($license) {
            $query->where('license', $license);
        }

        if ($search) {
            $query->whereHas('tags', function ($query) use ($search) {
                return $query->where('name', 'like', "%$search%");
            });
        }

        $query->where(function ($query) use ($following){
            return $query->where('reblogged_by', 'elemMatch', ['$in' => $following])
                ->orWhereIn('author', $following);
        });

        $comments = $query
            ->orderByDesc('created_at')
            ->paginate($limit)
            ->appends($request->except('page'));

        return response($comments);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function searchByReward(Request $request) {
        $validations = array(
            'search' => 'required|string',
            'download' => 'sometimes|string',
            'license' => 'sometimes|numeric',
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

        $search = strtolower($request->get('search'));
        $download = $request->get('download');
        $license = $request->get('license', false);
        $limit = $request->get('limit', 20);

        $query = Comments::query();
        if ($download) {
            $query->where('download', $download);
        }

        if ($license) {
            $query->where('license', $license);
        }

        $comments = $query->where('is_paid', true)
            ->where('title', 'like', "%$search%")
            ->orWhereHas('tags', function ($query) use ($search) {
                return $query->where('name', 'like', "%$search%");
            })
            ->orWhere('description', 'like', "%$search%")
            ->orderByDesc('reward')
            ->paginate($limit)
            ->appends($request->except('page'));

        return response($comments);
    }

    /**
     * @param Request $request
     * @param $user
     * @param $permlink
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Request $request, $user, $permlink) {
        $comment = Comments::query()
            ->where('author', $user)
            ->where('permlink', $permlink)
            ->first();

        if (!$comment) {
            abort(404 ,'Comment not found');
        }

        return response($comment);
    }

    /**
     * @param Request $request
     * @param $user
     * @param $permlink
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function showMultiple(Request $request) {
        $validations = array(
            'comments' => 'required|array'
        );

        $validatedData = Validator::make($request->all(), $validations);
        if ($validatedData->fails()) {
            return response([
                'status' => 'error',
                'message' => $validatedData->errors(),
                'error' => 'invalid_parameter'
            ], 400);
        }

        $comments = $request->get('comments');

        $commentsQuery = Comments::query();

        $first = true;
        foreach ($comments as $cl) {
            $author = explode('/', $cl)[0];
            $permlink = explode('/', $cl)[1];
            if ($first) {
                $commentsQuery->where(function ($q) use ($author, $permlink) {
                    return $q->where('author', $author)
                        ->where('permlink', $permlink);
                });
                $first = false;
            } else {
                $commentsQuery->orWhere(function ($q) use ($author, $permlink) {
                    return $q->where('author', $author)
                        ->where('permlink', $permlink);
                });
            }

        }

        $commentsData = $commentsQuery->get();


        return response($commentsData);
    }
}
