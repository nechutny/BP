<?php

function main($c = 12)
{
	for($i = 0; $i < 15; $i += 1)
	{
		echo $i;

		if($i === 5)
		{
			for($j = $i; $j < 4**5; $j *=2)
			{
				echo $i+$j;
			}
		}
		elseif($i < 12)
		{
			echo $i or 5 or $c;
		}
		else
		{
			for($j = $i; $j < 4**5; $j *=2)
			{
				echo $i+$j;
			}
		}
	}

	echo $i;
}
