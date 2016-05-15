<?php

function fibonaci($n)
{
	if ($n < 3)
	{
		return $n;
	}
	else
	{
		return fibonaci($n - 1) + fibonaci($n - 2);
	}
}

