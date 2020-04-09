<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 28/03/19
 * Time: 9:29
 */

namespace App\Http\Crea;


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
     */
    private function callRequest(array $requestBody) {
        $response = $this->httpClient->post(env('CREA_NODE'), array( 'json' => $requestBody));
        return json_decode((string) $response->getBody());
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     */
    private function buildRpcData(string $method, array $params) {
        return array(
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => random_int(0, 999999999)
        );
    }

    /**
     * @param string $author
     * @param string $permlink
     * @return null
     * @throws \Exception
     */
    public function getPost(string $author, string $permlink) {
        $rpcData = $this->buildRpcData('tags_api.get_discussion', array('author' => $author, 'permlink' => $permlink));

        $response = $this->callRequest($rpcData);

        if ($response->result) {
            $post = $response->result;
            $post->metadata = json_decode($post->json_metadata);
            $post->author = $this->getAccount($post->author);
            return $post;
        }

        return null;
    }

    /**
     * @param string $accountName
     * @return null
     */
    public function getAccount(string $accountName) {
        $rpcData = $this->buildRpcData('condenser_api.get_accounts', array(
            array(
                $accountName
            )
        ));

        $response = $this->callRequest($rpcData);
        if ($response->result) {
            $account = $response->result[0];
            $account->metadata = json_decode($account->json_metadata);
            $account->metadata->blocked = intval($account->reputation) < 0;

            return $account;
        }

        return null;
    }


}
