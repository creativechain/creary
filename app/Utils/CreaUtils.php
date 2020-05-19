<?php


namespace App\Utils;


use App\Http\Crea\CrearyClient;

class CreaUtils
{
    const DEFAULT_AVATAR = ['/img/avatar/avatar1.png', '/img/avatar/avatar2.png', '/img/avatar/avatar3.png', '/img/avatar/avatar4.png', '/img/avatar/avatar5.png'];

    public static function getDefaultAvatar($username) {
        $hexStr = bin2hex($username);
        $i = hexdec($hexStr);

        $avatar = $i % count(self::DEFAULT_AVATAR);
        $avatar = self::DEFAULT_AVATAR[$avatar];

        return $avatar;
    }

    /**
     * @param $voter
     * @param $voteWeight
     * @return float|int
     */
    public static function calculateVoteValue($voter, $voteWeight) {
        $creaClient = new CrearyClient();
        $voterState = $creaClient->getAccountState($voter);
        $rewardFund = $creaClient->getRewardFund();

        $voterAccount = $voterState->accounts->{ $voter };

        $vestingShares = floatval(StringUtils::evalRegexp('/(\d+\.\d+)/m', $voterAccount->vesting_shares));
        $receivedVestingShares = floatval(StringUtils::evalRegexp('/(\d+\.\d+)/m', $voterAccount->received_vesting_shares));
        $delegatedVestingShares = floatval(StringUtils::evalRegexp('/(\d+\.\d+)/m', $voterAccount->delegated_vesting_shares));

        $totalVests = $vestingShares + $receivedVestingShares - $delegatedVestingShares;
        $totalVests *= 1000000;

        $recentClaims = intval($rewardFund->recent_claims);

        $x1 = $totalVests / $recentClaims;

        $rewardBalance = floatval(StringUtils::evalRegexp('/(\d+\.\d+)/m', $rewardFund->reward_balance));

        $x2 = $x1 * $rewardBalance;

        $base = floatval(StringUtils::evalRegexp('/(\d+\.\d+)/m', $voterState->feed_price->base));
        $quote = floatval(StringUtils::evalRegexp('/(\d+\.\d+)/m', $voterState->feed_price->quote));
        $price = $base / $quote;

        $x3 = $x2 * $price;

        $maxVoteValue = $x3 * 0.02;

        $voteWeight = $voteWeight / 10000;
        return $maxVoteValue * $voteWeight;

    }
}
