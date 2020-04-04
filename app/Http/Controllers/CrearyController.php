<?php


namespace App\Http\Controllers;


use Creary\URLUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CrearyController extends Controller
{

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

        $metas = array(
            $this->buildMeta('property', 'og:url', $request->fullUrl()),
            $this->buildMeta('property', 'og:title', $pageMeta->TITLE),
            $this->buildMeta('property', 'og:image', $language->METADATA->IMAGE),
            $this->buildMeta('property', 'og:description', $pageMeta->DESCRIPTION),
            $this->buildMeta('property', 'og:type', 'website'),
            $this->buildMeta('name', 'twitter:card', 'summary_large_image'),
            $this->buildMeta('name', 'twitter:site', '@crearynet'),
            $this->buildMeta('name', 'twitter:title', $pageMeta->TITLE),
            $this->buildMeta('name', 'twitter:description', $pageMeta->DESCRIPTION),
            $this->buildMeta('name', 'twitter:image',$language->METADATA->IMAGE),
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
        if ($post) {
            $authorName = $author;

            if (array_key_exists('metadata', $post['author'])) {
                $authorMetadata = $post['author']['metadata'];
                $blocked = $authorMetadata['blocked'];
                if (!$blocked) {
                    if (array_key_exists('publicName', $authorMetadata)) {
                        $authorName = $authorMetadata['publicName'];
                    }
                }
            }


            $title = 'Creary - ' . $post['title'];

            if ($blocked) {
                $metas = array();
            } else {
                $metas = array(
                    $this->buildMeta('property', 'og:url', $request->fullUrl()),
                    $this->buildMeta('property', 'og:title', $title),
                    $this->buildMeta('property', 'og:image', $post['metadata']['featuredImage']['url']),
                    $this->buildMeta('property', 'og:description', $post['metadata']['description']),
                    $this->buildMeta('property', 'og:type', 'article'),
                    $this->buildMeta('property', 'article:published_time', $post['created']),
                    $this->buildMeta('property', 'article:modified_time', $post['last_update']),
                    $this->buildMeta('property', 'article:author', $authorName),
                    $this->buildMeta('name', 'twitter:card', 'summary_large_image'),
                    $this->buildMeta('name', 'twitter:site', '@Crearynet'),
                    $this->buildMeta('name', 'twitter:creator', '@' . $author),
                    $this->buildMeta('name', 'twitter:title', $title),
                    $this->buildMeta('name', 'twitter:description', $post['metadata']['description']),
                    $this->buildMeta('name', 'twitter:image', $post['metadata']['featuredImage']['url']),
                    $this->buildMeta('name', 'description', $post['metadata']['description']),
                );
            }

            $tags = $post['metadata']['tags'];
            if ($tags) {
                $metas[] = $this->buildMeta('name', 'keywords', implode(',', $tags));
                foreach ($tags as $t) {
                    $metas[] = $this->buildMeta('property', 'article:tag', $t);
                }
            }

            $renderParams['post'] = $post;
            $renderParams['metas'] = $metas;
            $renderParams['title'] = $title;

            return view('post-view')
                ->withTitle($title)
                ->withMetas($metas);
        }

        //TODO: Return 404
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

        if ($profile) {
            //dd($profile);

            //Blocked == If user has negative reputation, is a blocked user. Metadata must be a default user
            $blocked = $profile['metadata']['blocked'];

            if ($blocked) {
                $publicName = $profile['metadata']['publicName'];
                $title = 'Creary - @' . $profileName;


                $metas = array(
                    $this->buildMeta('property', 'og:url', $request->fullUrl()),
                    $this->buildMeta('property', 'og:title', $title),
                    $this->buildMeta('property', 'og:image', $profile['metadata']['avatar']['url']),
                    $this->buildMeta('property', 'og:description', $profile['metadata']['about']),
                    $this->buildMeta('property', 'og:type', 'profile'),
                    $this->buildMeta('property', 'profile:first_name', $publicName ? $publicName : $profileName),
                    $this->buildMeta('property', 'profile:username', $profileName),
                    $this->buildMeta('name', 'twitter:card', 'summary_large_image'),
                    $this->buildMeta('name', 'twitter:site', '@Crearynet'),
                    $this->buildMeta('name', 'twitter:creator', '@' . $profileName),
                    $this->buildMeta('name', 'twitter:title', $title),
                    $this->buildMeta('name', 'twitter:description', $profile['metadata']['about']),
                    $this->buildMeta('name', 'twitter:image', $profile['metadata']['avatar']['url']),
                    $this->buildMeta('name', 'description', $profile['metadata']['about']),
                );

            } else {
                //dd($profile);
                $publicName = null;
                if (array_key_exists('metadata', $profile)) {
                    if (array_key_exists('publicName', $profile['metadata'])) {
                        $publicName = $profile['metadata']['publicName'];
                    }
                }

                if ($publicName) {
                    $title = "Creary - $publicName (@$profileName)";
                } else {
                    $title = "Creary - @$profileName";
                }

                $metas = array(
                    $this->buildMeta('property', 'og:url', $request->fullUrl()),
                    $this->buildMeta('property', 'og:title', $title),
                    $this->buildMeta('property', 'og:type', 'profile'),
                    $this->buildMeta('property', 'profile:first_name', $publicName ? $publicName : $profileName),
                    $this->buildMeta('property', 'profile:username', $profileName),
                    $this->buildMeta('name', 'twitter:card', 'summary_large_image'),
                    $this->buildMeta('name', 'twitter:site', '@Crearynet'),
                    $this->buildMeta('name', 'twitter:creator', '@' . $profileName),
                    $this->buildMeta('name', 'twitter:title', $title),
                );

                if (array_key_exists('metadata', $profile)) {
                    $metadata = $profile['metadata'];

                    if (array_key_exists('avatar', $metadata)) {
                        $metas[] = $this->buildMeta('property', 'og:image', $metadata['avatar']['url']);
                        $metas[] = $this->buildMeta('name', 'twitter:image', $metadata['avatar']['url']);
                    }

                    if (array_key_exists('about', $metadata)) {
                        $metas[] = $this->buildMeta('property', 'og:description', $metadata['about']);
                        $metas[] = $this->buildMeta('name', 'twitter:description', $metadata['about']);
                        $metas[] = $this->buildMeta('name', 'description', $metadata['about']);
                    }

                    if (array_key_exists('tags', $metadata)) {
                        $tags = $metadata['tags'];
                        $metas[] = $this->buildMeta('name', 'keywords', implode(',', $tags));
                    }

                }
            }



            return view('profile')
                ->withTitle($title)
                ->withMetas($metas)
                ->withProfile($profile);
        }

        //TODO: Return 404
    }

    public function profileSection(Request $request, $user, $section) {
        return $this->profile($request, $user);
    }

    public function search(Request $request) {
        return $this->buildViewResponse($request, 'home');
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
