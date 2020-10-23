<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Crea\CrearyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class BlockchainController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getCurrentSupply(Request $request) {

        try {
            $client = new CrearyClient();
            $props = $client->getGlobalProperties();

            $currentSupply = str_replace(' CREA', '', $props->current_supply);
            return response($currentSupply, 200, ['Content-Type' => 'text/plain']);
        } catch (\Exception $e) {
            Log::error('Error getting global properties', $e->getTrace());;
        }

        return response('Service in Maintenance', 503);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getTotalSupply(Request $request) {


        try {
            $client = new CrearyClient();
            $props = $client->getGlobalProperties();

            $totalSupply = str_replace(' CREA', '', $props->virtual_supply);
            return response($totalSupply, 200, ['Content-Type' => 'text/plain']);
        } catch (\Exception $e) {
            Log::error('Error getting global properties', $e->getTrace());;
        }

        return response('Service in Maintenance', 503);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getSupply(Request $request) {

        try {
            $client = new CrearyClient();
            $props = $client->getGlobalProperties();

            $virtualSupply = str_replace(' CREA', '', $props->virtual_supply);
            $currentSupply = str_replace(' CREA', '', $props->current_supply);
            return response([
                'current_supply' => $currentSupply,
                'total_supply' => $virtualSupply,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting global properties', $e->getTrace());;
        }

        return response('Service in Maintenance', 503);
    }

    public function markRead(Request $request, $creaUser) {
        Gate::authorize('crea-notification', $creaUser);

        return response('yeah');
    }
}
