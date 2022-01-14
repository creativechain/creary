<?php


namespace App\Http\Controllers\Api;


use App\Comments;
use App\Http\Controllers\Controller;
use App\Http\Crea\CrearyClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{

    public function feed(Request $request) {
        $validations = array(
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

        $username = $request->cookie('creary_username');
        if ($username) {
            $creaClient = new CrearyClient();
            $followings = $creaClient->getAccountFollowings($username);
            $following = $followings['blog'];
            //dd($following);

            $query = Comments::query();

            //Only avoid return adult content if field $adult = false (Not show adult content)
            if (!$adult) {
                $query->where('adult', false);
            }

            if ($download) {
                $query->where('download', $download);
            }

            //Filter removed posts
            $query->where(function (Builder $query) {
                return $query->where('is_visible', 'exists', false)
                    ->orWhere('is_visible', true);
            });

            if ($license) {
                if ($license === 1) {
                    //Select all Creative Commons license flags
                    $license = [3, 7, 11, 15, 19, 27];
                } else {
                    $license = [$license];
                }

                $query->whereIn('license', $license);
            }

            if ($search) {
                $query->where(function (Builder $query) use ($search) {
                    return $query->orWhereHas('tags', function (Builder $query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                        ->orWhere('title', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%");
                });

            }

            $query->where(function ($query) use ($following){
                return $query->orWhere('reblogged_by', 'elemMatch', ['$in' => $following])
                    ->orWhereIn('author', $following);
            });

            //dd($query->toSql());

            $comments = $query
                ->with('tags')
                ->orderByDesc('created_at')
                ->paginate($limit)
                ->appends($request->except('page'));

            return response($comments);
        } else {
            return response([
                'status' => 'error',
                'message' => 'No user detected',
                'error' => 'guest_user'
            ], 403);
        }


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
            'isPaid' => 'sometimes|boolean'
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
        $limit = intval($request->get('limit', 20));

        $query = Comments::query();

        //Filter removed posts
        $query->where(function (Builder $query) {
            return $query->where('is_visible', 'exists', false)
                ->orWhere('is_visible', true);
        });

        if ($download) {
            $query->where('download', $download);
        }

        if ($license) {
            $query->where('license', $license);
        }

        if ($request->has('isPaid')) {
            $isPaid = boolval($request->get('isPaid'));
            $query->where('is_paid', $isPaid);
        }

        $comments = $query
            ->where(function (Builder $query) use ($search) {
                return $query->orWhere('title', 'like', "%$search%")
                    ->orWhereHas('tags', function ($query) use ($search) {
                        return $query->where('name', 'like', "%$search%");
                    })
                    ->orWhere('description', 'like', "%$search%");
            })
            ->with('tags')
            ->orderByDesc('reward')
            ->paginate($limit)
            ->appends($request->except('page'));

        return response($comments);
    }

    /**
     * @param Request $request
     * @param $author
     * @param $permlink
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Request $request, $author, $permlink) {
        $author = str_replace('@', '', $author);
        $comment = Comments::query()
            ->where('author', $author)
            ->where('permlink', $permlink)
            ->where(function (Builder $query) {
                return $query->where('is_visible', 'exists', false)
                    ->orWhere('is_visible', true);
            })
            ->first();

        if (!$comment) {
            abort(404 ,'Comment not found');
        }

        return response($comment);
    }

    /**
     * @param Request $request
     * @param $author
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function portfolio(Request  $request, $author) {
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

        $limit = intval($request->get('limit', 20));
        $author = str_replace('@', '', $author);
        return Comments::query()
            ->where('author', $author)
            ->where(function (Builder $query) {
                return $query->where('is_visible', 'exists', false)
                    ->orWhere('is_visible', true);
            })
            ->orderByDesc('created_at')
            ->paginate($limit)
            ->appends($request->except('page'));
    }

    /**
     * @param Request $request
     * @param $user
     * @param $permlink
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function showMultiple(Request $request) {
        $validations = array(
            'comments' => 'required|string'
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
        $comments = explode(',', $comments);

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

        //Filter removed posts
        $commentsQuery->where(function (Builder $query) {
            return $query->where('is_visible', 'exists', false)
                ->orWhere('is_visible', true);
        });


        $commentsData = $commentsQuery->get();
        Log::debug("Retrieved comments", $commentsData->toArray());

        $nonExistentComments = [];

        //Check if any comment not exists;
        foreach ($commentsData as $c) {
            /** @var Comments $c */
            $permlink = $c->author . '/' . $c->permlink;
            $index = array_search($permlink, $comments);
            if (!$index) {
                $nonExistentComments[] = $permlink;
            }
        }

        Log::debug("NonExistentComments", $nonExistentComments);

        foreach ($nonExistentComments as $cl) {
            $author = explode('/', $cl)[0];
            $permlink = explode('/', $cl)[1];
            $cc = new CrearyClient();
            $content = $cc->getPost($author, $permlink);
            $c = Comments::query()
                ->where('permlink', $permlink)
                ->where('author', $author)
                ->first();

            if (!$c) {
                $c = new Comments();
            }

            $c->applyData($content);

            $commentsData->add($c);
        }


        return response($commentsData);
    }
}
