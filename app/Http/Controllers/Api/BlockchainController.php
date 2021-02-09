<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Crea\CrearyClient;
use App\Op;
use App\Tx;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

    public function statistics(Request $request) {
        $validations = array(
            'period' => 'sometimes|string|in:24h,7d,30d',
        );

        $validatedData = Validator::make($request->all(), $validations);
        if ($validatedData->fails()) {
            return response([
                'status' => 'error',
                'message' => $validatedData->errors(),
                'error' => 'invalid_parameter'
            ], 400);
        }

        $period = $request->get('period', '24h');
        switch ($period) {
            case '30d':
                $timePeriod = Carbon::now()->subtract('days', 30);
                break;
            case '7d':
                $timePeriod = Carbon::now()->subtract('days', 1);
                break;
            default:
                $timePeriod = Carbon::now()->subtract('hours', 24);
                break;
        }

        $txsOverPeriod = Tx::query()
            ->where('timestamp', '>=', $timePeriod)
            ->count();

        $opsOverPeriod = Op::query()
            ->where('timestamp', '>=', $timePeriod)
            ->count();

        try {
            $client = new CrearyClient();
            $props = $client->getGlobalProperties();
            $accountCount = $client->getAccountCount();

            $virtualSupply = str_replace(' CREA', '', $props->virtual_supply);
            $currentSupply = str_replace(' CREA', '', $props->current_supply);
            return response([
                'total_accounts' => $accountCount,
                'current_supply' => $currentSupply,
                'total_supply' => $virtualSupply,
                "txs_over_$period" => $txsOverPeriod,
                "ops_over_$period" => $opsOverPeriod
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting global properties', $e->getTrace());;
        }

        return response('Service in Maintenance', 503);
    }
}
