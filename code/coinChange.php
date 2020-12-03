<?php
//零钱兑换，根据给定的面值，用最少的数量兑换出给定的金额
function coinChange($coins=[], $amount='')
{
	if ($amount < 1) {
		return 0;
	}
	$dp = array_pad([], $amount+1, -1);
	$dp[0] = 0;
	for ($i=1; $i <= $amount; $i++) { 
		foreach ($coins as $key => $value) {
			if ($i >= $value && $dp[$i-$value] != -1) {
				if ($dp[$i] == -1 || $dp[$i]>$dp[$i-$value]+1) {
					$dp[$i] = $dp[$i-$value] + 1;
				}
			}
		}
	}
	return $dp[$amount];
}

echo coinChange([1,2,5,7,10], 14);
