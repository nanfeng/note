<?php
//二叉树从右侧观察
function btreeRightView($tree='')
{
	$watch = [];//记录从右侧观察到的数据
	if (null == $tree) {
		return '';
	}
	$queue = enqueue([$tree, 0]);//把根加入队列
	while ($queue) {//层次遍历树
		$node, $level = dequeue($queue);//取出队头数据
		if ($node.leftChild) {//把左孩子加入到队列，同时把层数+1加入到队列
			enqueue([$node.leftChild, $level+1]);
		}
		if ($node.rightChild) {//同上
			enqueue([$node.rightChild, $level+1]);
		}
		$watch[$level] = $node.val;//
	}
	return $watch;
}
