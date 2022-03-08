<?php


namespace App\Http\Controllers;


use App\Utils\CreaUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CrearyController extends Controller
{

/*    public function testVotes(Request $request, $creaUser) {
        return CreaUtils::calculateVoteValue($creaUser, 10000);
    }*/

    /**
     * @param Request $request
     * @param $view
     * @return mixed
     */
    private function buildViewResponse(Request $request, $view) {
        $language = $this->getLanguage();
        $page = $request->path();
        if ($page !== '/') {
            $page = "/" . $page;
        }
        //dd($page, $language);
        try {
            $pageMeta = $language->METADATA->{ $page };
        } catch (\Exception $e) {
            $pageMeta = $language->METADATA->{ '/' };
        }

        $appUrl = config('app.url');

        $metas = array(
            $this->buildMeta('property', 'og:url', $request->fullUrl()),
            $this->buildMeta('property', 'og:title', $pageMeta->TITLE),
            $this->buildMeta('property', 'og:image', "$appUrl/" . $language->METADATA->IMAGE),
            $this->buildMeta('property', 'og:description', $pageMeta->DESCRIPTION),
            $this->buildMeta('property', 'og:type', 'website'),
            $this->buildMeta('name', 'twitter:card', 'summary_large_image'),
            $this->buildMeta('name', 'twitter:site', '@crearynet'),
            $this->buildMeta('name', 'twitter:title', $pageMeta->TITLE),
            $this->buildMeta('name', 'twitter:description', $pageMeta->DESCRIPTION),
            $this->buildMeta('name', 'twitter:image', "$appUrl/" . $language->METADATA->IMAGE),
            $this->buildMeta('name', 'description', $pageMeta->DESCRIPTION),
        );

        return view($view)
            ->withMetas($metas)
            ->withTitle($pageMeta->TITLE);
    }

    public function home(Request $request) {
        return $this->buildViewResponse($request, 'home');
    }

    public function post(Request $request, $user, $permlink) {
        //Get author and permlink;
        $author = Str::replaceFirst('@', '', $user);

        $client = $this->getCrearyClient();
        $post = $client->getPost($author, $permlink);
        $blocked = false;

        $metas = array(
            $this->buildMeta('property', 'og:url', $request->fullUrl()),
            $this->buildMeta('property', 'og:type', 'article'),
            $this->buildMeta('name', 'twitter:card', 'summary_large_image'),
            $this->buildMeta('name', 'twitter:site', '@Crearynet'),
        );

        //dd($post->metadata);
        if ($post && $post->title) {
            $authorName = $author;
            $metadata = $post->metadata;
            $authorMetadata = $post->author->metadata;

            if ($authorMetadata) {
                $blocked = $authorMetadata->blocked;
                if (!$blocked && $authorMetadata->publicName) {
                    $authorName = $authorMetadata->publicName;
                }
            }

            if (!$blocked) {
                //User is not blocked
                $metas = array(
                    $this->buildMeta('property', 'og:url', $request->fullUrl()),
                    $this->buildMeta('property', 'og:type', 'article'),
                    $this->buildMeta('name', 'twitter:card', 'summary_large_image'),
                    $this->buildMeta('name', 'twitter:site', '@Crearynet'),
                );

                $metas[] = $this->buildMeta('property', 'article:author', $authorName);

                //Set title
                $metas[] = $this->buildMeta('property', 'og:title', "Creary - $post->title");
                $metas[] = $this->buildMeta('property', 'article:published_time', $post->created);
                $metas[] = $this->buildMeta('property', 'article:modified_time', $post->last_update);
                $metas[] = $this->buildMeta('name', 'twitter:creator', '@' . $author);

                if ($metadata) {
                    //Set post preview
                    if ($metadata->sharedImage && $metadata->sharedImage->url) {
                        $metas[] = $this->buildMeta('property', 'og:image', $post->metadata->sharedImage->url);
                        $metas[] = $this->buildMeta('name', 'twitter:image', $post->metadata->sharedImage->url);
                    } else if ($metadata->featuredImage && $metadata->featuredImage->url) {
                        $metas[] = $this->buildMeta('property', 'og:image', $post->metadata->featuredImage->url);
                        $metas[] = $this->buildMeta('name', 'twitter:image', $post->metadata->featuredImage->url);
                    }

                    //Set description
                    if ($metadata->description) {
                        $metas[] = $this->buildMeta('property', 'og:description', $post->metadata->description);
                        $metas[] = $this->buildMeta('name', 'twitter:description', $post->metadata->description);
                        $metas[] = $this->buildMeta('name', 'description', $post->metadata->description);
                    } else {
                        $pageMeta = $this->getLanguage()->METADATA->{ '/' };
                        $metas[] = $this->buildMeta('property', 'og:description', $pageMeta->DESCRIPTION);
                        $metas[] = $this->buildMeta('name', 'twitter:description', $pageMeta->DESCRIPTION);
                        $metas[] = $this->buildMeta('name', 'description', $pageMeta->DESCRIPTION);
                    }

                    $tags = $post->metadata->tags;
                    if ($tags) {
                        $metas[] = $this->buildMeta('name', 'keywords', implode(',', $tags));
                        foreach ($tags as $t) {
                            $metas[] = $this->buildMeta('property', 'article:tag', $t);
                        }
                    }
                }

            }

            return view('post-view')
                ->withTitle($post->title)
                ->withMetas($metas);
        }

        abort(404, "Post $user/$permlink not found");
    }

    public function postCategory(Request $request, $category, $user, $permlink) {
        return $this->post($request, $user, $permlink);
    }

    public function profile(Request $request, $user) {
        //Get author
        //Skip '/@' chars
        $profileName = $user;
        $profileName = substr($profileName, 1, strlen($profileName));

        $client = $this->getCrearyClient();
        $profile = $client->getAccount($profileName);

        $metas = array(
            $this->buildMeta('property', 'og:url', $request->fullUrl()),
            $this->buildMeta('property', 'og:type', 'profile'),
            $this->buildMeta('property', 'profile:username', $profileName),
            $this->buildMeta('name', 'twitter:card', 'summary_large_image'),
            $this->buildMeta('name', 'twitter:site', '@Crearynet'),
            $this->buildMeta('name', 'twitter:creator', '@' . $profileName)
        );

        if ($profile) {
            //dd($profile);

            $metadata = $profile->metadata;

            //Set page title and user's name
            if ($metadata->publicName) {
                $title = "Creary - $metadata->publicName (@$profileName)";
            } else {
                $title = "Creary - @$profileName";
            }

            $metas[] = $this->buildMeta('property', 'og:title', $title);
            $metas[] = $this->buildMeta('property', 'profile:first_name', $metadata->publicName ? $metadata->publicName : $profileName);

            //Set avatar
            if ($metadata && $metadata->avatar && $metadata->avatar->url) {
                $metas[] = $this->buildMeta('property', 'og:image', $metadata->avatar->url);
                $metas[] = $this->buildMeta('name', 'twitter:image', $metadata->avatar->url);
            } else {
                $metas[] = $this->buildMeta('property', 'og:image', $request->getSchemeAndHttpHost() . CreaUtils::getDefaultAvatar($profileName));
                $metas[] = $this->buildMeta('name', 'twitter:image', $request->getSchemeAndHttpHost() . CreaUtils::getDefaultAvatar($profileName));
            }

            //Set user description
            if ($metadata->about) {
                $metas[] = $this->buildMeta('property', 'og:description', $metadata->about);
                $metas[] = $this->buildMeta('name', 'twitter:description', $metadata->about);
            } else {
                $pageMeta = $this->getLanguage()->METADATA->{ '/' };
                $metas[] = $this->buildMeta('property', 'og:description', $pageMeta->DESCRIPTION);
                $metas[] = $this->buildMeta('name', 'twitter:description', $pageMeta->DESCRIPTION);
            }

            //Blocked == If user has negative reputation, is a blocked user. Metadata must be a default user
            $blocked = $profile->metadata->blocked;

            return view('profile')
                ->withTitle($title)
                ->withMetas($metas)
                ->withProfile($profile);
        }

        abort(404, "User $user not found.");
    }

    public function profileSection(Request $request, $user, $section) {
        return $this->profile($request, $user);
    }

    public function search(Request $request) {
        return $this->buildViewResponse($request, 'home');
    }

    public function accountsSearch(Request $request) {
        return $this->buildViewResponse($request, 'account-search');
    }

    public function welcome(Request $request) {
        return $this->buildViewResponse($request, 'welcome');
    }

    public function witnesses(Request $request) {
        return $this->buildViewResponse($request, 'witnesses');
    }

    public function publish(Request $request) {
        return $this->buildViewResponse($request, 'publish');
    }

    public function explore(Request $request) {
        return $this->buildViewResponse($request, 'explore');
    }

    public function market(Request $request) {
        return $this->buildViewResponse($request, 'market');
    }

    public function faq(Request $request) {
        return $this->buildViewResponse($request, 'faq');
    }

    public function recoverAccount(Request $request) {
        return $this->buildViewResponse($request, 'recover-account');
    }

    public function terms(Request $request) {
        return $this->buildViewResponse($request, 'terms_and_conditions');
    }

    public function privacy(Request $request) {
        return $this->buildViewResponse($request, 'privacy_policy');
    }
}
