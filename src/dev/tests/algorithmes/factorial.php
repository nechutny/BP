<?php

function factorial($max)
{
	$sum = 0;

	for($i = 0; $i < $max; $i++)
	{
		$sum *= $i;
	}

	echo $sum."\n";
}
