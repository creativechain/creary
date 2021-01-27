<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 28/03/19
 * Time: 9:29
 */

namespace App\Http\Crea;


use App\Utils\Obj;
use GuzzleHttp\Client;

class CrearyClient
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * CrearyClient constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * @param array $requestBody
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function callRequest(array $requestBody) {
        $response = $this->httpClient->post(env('CREA_NODE'), array( 'json' => $requestBody));
        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     * @throws \Exception
     */
    private function buildRpcData(string $method, array $params = []) {
        return array(
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => random_int(0, 999999999)
        );
    }

    /**
     * @param bool $parse
     * @return Obj|array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGlobalProperties($parse = true) {
        $rpcData = $this->buildRpcData('condenser_api.get_dynamic_global_properties');

        $response = $this->callRequest($rpcData);

        if ($parse) {
            return Obj::parse($response['result']);
        }
        return $response['result'];
    }

    /**
     * @param string $author
     * @param string $permlink
     * @param bool $asObject
     * @return null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPost(string $author, string $permlink, $asObject = true) {
        $rpcData = $this->buildRpcData('tags_api.get_discussion', array('author' => $author, 'permlink' => $permlink));

        $response = $this->callRequest($rpcData);

        if ($response['result']) {
            $post = $response['result'];
            $reblogs = $this->getReblogs($author, $permlink);
            if ($asObject) {
                $post = Obj::parse($post);
                $post->metadata = Obj::parse(json_decode($post->json_metadata));
                $post->author = $this->getAccount($post->author);
                $post->reblogged_by = $reblogs;
            } else {
                $post['metadata'] = json_decode($post['json_metadata'], true);
                $post['author'] = $this->getAccount($post['author'], false);
                $post['reblogged_by'] = $reblogs;
            }

            return $post;
        }

        return null;
    }

    /**
     * @param string $accountName
     * @param bool $parse
     * @return null
     * @throws \Exception
     */
    public function getAccount(string $accountName, $parse = true) {
        $rpcData = $this->buildRpcData('condenser_api.get_accounts', array(
            array(
                $accountName
            )
        ));

        $response = $this->callRequest($rpcData);
        if ($response['result']) {
            $account = $response['result'][0];
            if ($parse) {
                $account = Obj::parse($account);
                $account->metadata = Obj::parse(json_decode($account->json_metadata));
                if (!$account->metadata) {
                    $account->metadata = new Obj();
                }

                $account->metadata->blocked = intval($account->reputation) < 0;
            } else {
                $account['metadata'] = json_decode($account['json_metadata'], true);

                $account['metadata']['blocked'] = intval($account['reputation']) < 0;
            }


            return $account;
        }

        return null;
    }

    /**
     * @param $accountName
     * @param string $what
     * @param int $limit
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccountFollowings($accountName, $what = 'blog', $limit = 1000) {
        $rpcData = $this->buildRpcData('condenser_api.get_following', array($accountName, '', $what, $limit));

        $response = $this->callRequest($rpcData);

        if ($response['result']) {
            $followings = $response['result'];
            $parsedFollowings = array();

            foreach ($followings as $following) {
                $what = $following['what'][0];
                $user = $following['following'];
                if (!isset($parsedFollowings[$what])) {
                    $parsedFollowings[$what] = array();
                }

                $parsedFollowings[$what][] = $user;
            }

            return $parsedFollowings;
        }

        return null;
    }

    /**
     * @param $height
     * @param bool $parse
     * @return Obj|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBlock($height, $parse = true) {
        $rpcData = $this->buildRpcData('condenser_api.get_block', array( $height ));

        $response = $this->callRequest($rpcData);

        if ($parse) {
            return Obj::parse($response['result']);
        }

        return $response['result'];
    }

    /**
     * @param string $accountName
     * @param bool $parse
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccountState(string $accountName, $parse = true) {
        $rpcData = $this->buildRpcData('condenser_api.get_state', array( "@$accountName"));

        $response = $this->callRequest($rpcData);
        if ($parse) {
            return Obj::parse($response['result']);
        }
        return $response['result'];
    }

    /**
     * @param string $name
     * @param bool $parse
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRewardFund($name = 'post', $parse = true) {
        $rpcData = $this->buildRpcData('condenser_api.get_reward_fund', array( $name ));

        $response = $this->callRequest($rpcData);
        if ($parse) {
            return Obj::parse($response['result']);
        }
        return $response['result'];
    }

    /**
     * @param string $author
     * @param string $permlink
     * @param bool $parse
     * @return Obj|array|bool|int|mixed|string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getReblogs(string $author, string $permlink, $parse = true) {
        $rpcData = $this->buildRpcData('condenser_api.get_reblogged_by', array($author, $permlink));

        $response = $this->callRequest($rpcData);
        if (array_key_exists('result', $response)) {
            $result = $response['result'];

            $index = array_search($author, $result);
            if($index !== FALSE){
                array_splice($result, $index, 1);
            }

            if ($parse) {
                return Obj::parse($result);
            }

            return $result;
        }

        return array();

    }
}
