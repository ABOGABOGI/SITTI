<?php 

require_once('Numbers/Words.php');

/**
* Class NumbersWordsUtil menyediakan method untuk menerjemahkan bilangan ke dalam bentuk abjadnya
* misalnya 1250 menjadi seribu dua ratus lima puluh
* Parent class menyediakan dalam berbagai bahasa tapi dalam wrapper class ini hanya dalam bahasa indonesia
* cara menggunakan :
* $number = 1250;
* $nw = new NumberWordsUtil();
* echo $nw->toWords($number);
*/
class NumbersWordsUtil extends Numbers_Words
{
	function __construct()
	{
		#parent::_construct();
	}

	function toWords($num)
	{
		return parent::toWords($num, "id");
	}
}

?>