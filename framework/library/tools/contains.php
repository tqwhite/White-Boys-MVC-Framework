<?php
function contains($pattern, $inString, $noCase='true')
{
		if ($noCase=='true')
		{
				$pattern=strtolower($pattern);
				$inString=strtolower($inString);
		}
		return(is_int(strpos($inString, $pattern)));
}