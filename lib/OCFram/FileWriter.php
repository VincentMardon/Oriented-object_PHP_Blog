<?php
namespace OCFram;

trait FileWriter
{
	public static function write($file, $content)
	{
		$f = fopen($file, 'w');
		fwrite($f, $content);
		fclose($f);
	}
}