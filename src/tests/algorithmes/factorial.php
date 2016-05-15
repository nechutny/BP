<?php

function factorial($max)
{
	$sum = 0;

	for($i = 0; $i < $max; $i = $i + 1)
	{
		$sum *= $i;
	}

	echo $sum."\n";
}
