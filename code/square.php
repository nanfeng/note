<?php
//给定一个整数数组，检查是否可以拼接成正方形
function isSquare($arr=[])
{
	if (count($arr) < 4) {//个数不足4，不能组成正方形
		return false;
	}
	$sum = array_sum($arr);
	if ($sum % 4 != 0) {//不能被4整除，不能组成正方形
		return false;
	}
	$bucket = [0,0,0,0];//初始化4个桶，用来存放
	return backTrack(0, $arr, $sum/4, $bucket);//回溯法
}

function backTrack($idx, $arr, $edge, &$bucket)
{
	if ($idx >= count($arr)) {//遍历完了
		return true;
	}
	for ($i=0; $i < 4; $i++) { //循环遍历4个桶
		if ($arr[$idx] + $bucket[$i] > $edge) {//检查桶是否已满
			continue;
		}
		$bucket[$i] += $arr[$idx];//加入桶
		if (backTrack($idx+1, $arr, $edge, $bucket)) {//检查下一个
			return true;
		}
		$bucket[$i] -= $arr[$idx];//取出
	}
	return false;
}

var_dump(isSquare([1,5,2,2,2,]));
