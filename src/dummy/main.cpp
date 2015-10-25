#include <phpcpp.h>
#include <iostream>



/*
 * PHP: 73s
 *
 * C++: 0.35s
 *
 * -> zrychlení: 20 000%
 *
  function php_test($max)
  {
  	$sum = 0;
  	for($i = 0; $i < $max; $i++)
  	{
  		$sum += $i;
  	}
  	echo $sum."\n";
  }

 */
Php::Value compiled_test(Php::Parameters &parameters)
{
	long max = parameters[0];
	long long sum = 0;

	for(long i = 0; i < max; i++)
	{
		sum += i;
	}
	Php::out << sum << std::endl;

	return NULL;
}


/*
 * PHP: 3.42s
 *
 * C++: 0.24s
 *
 * -> zrychlení: 1 425%
 *
 * $so = []; for($i = 0; $i < 50000; $i++) { $so[] = rand(0,200000); }

  function bubble_sort($arr)
  {
  	$size = count($arr);
	for ($i=0; $i<$size; $i++)
	{
		for ($j=0; $j<$size-1-$i; $j++)
		{
			if ($arr[$j+1] < $arr[$j])
			{
				$tmp = $arr[j];
				$arr[j] = $arr[j+1];
				$arr[j+1] = $tmp;
			}
		}
	}
	print_r($arr);
   }
 */
Php::Value compiled_sort(Php::Parameters &parameters)
{
	Php::Value arrP = parameters[0];

	std::vector<int> arr = arrP;

	int tmp;

	long size = Php::count(arrP);

	for(int i = 0; i < size; i++)
	{
		for(int j = 0; j < size-1-i; j++)
		{
			if(arr[j+1] < arr[j])
			{
				//std::swap(arr[j],arr[j+1]);

				tmp = arr[j];
				arr[j] = arr[j+1];
				arr[j+1] = tmp;
			}
		}
	}

	Php::call("print_r", arr);

	return NULL;
}





extern "C"
{
	PHPCPP_EXPORT void *get_module()
	{
		static Php::Extension extension("TestExtension", "1.0");

		extension.add("compiled_test", compiled_test);
		extension.add("compiled_sort", compiled_sort);

		return extension;
	}
}
