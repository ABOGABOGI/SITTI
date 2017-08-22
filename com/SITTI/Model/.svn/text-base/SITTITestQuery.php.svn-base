<?php
/**
 * 
 * Query Constants untuk Module SITTITest (Uji SITTI)
 * @author ASUS
 *
 */ 
class SITTITestQuery{
	/**
	 * tarik related keyword berdasarkan 1 kata. tanpa filter berdasarkan jenis kata ataupun brand.
	 * @param $param1 keyword inputan
	 * @author Hapsoro Renaldy <hapsoro.renaldy@kamisitti.com>
	 */
	function getRelatedKeywordOverall($param1){
		return " SELECT a.kata1 as input, a.kata2 as kata1, a.jumlah
FROM db_words.tb_sitti_kata2 AS a INNER JOIN db_words.tb_kata_tipe AS b ON (a.kata2 = b.kata) WHERE (a.kata1 = '".$param1."') AND b.kata_tipe <> 5 AND a.kata2 <> 'jakarta' AND a.kata2 <> 'indonesia'
AND a.kata2 NOT IN ( SELECT kata FROM db_words.tbl_out ) ORDER BY a.jumlah DESC, b.kata_tipe ASC LIMIT 50
		";
	}
	
	/**
	 * 
	 * tarik related keyword berdasarkan 2 kata.
	 * @param $param1
	 */
	function getRelatedKeywordOverall2($param1){
		return "SELECT 1 AS id, a.kata2 AS keyword, a.kata1 , a.jumlah
FROM (SELECT a.kata2, a.kata1, a.jumlah, b.kata_tipe
FROM db_words.tb_2kata_1kata a INNER JOIN
db_words.tb_kata_tipe b ON a.kata1 = b.kata
WHERE (a.kata2 = '".$param1."') AND (b.kata_tipe <> 5)
ORDER BY a.jumlah DESC, b.kata_tipe LIMIT 50 ) a LEFT OUTER JOIN
db_words.tbl_out b ON a.kata1 = b.kata WHERE (b.kata IS NULL) LIMIT 40";
	}
	/**
	 * 
	 * tarik related keyword filter berdasarkan jenis kata
	 * 1 -> 
	 * 2 -> 
	 * 3 ->
	 * @param $txt
	 * @param $type
	 */
	function getRelatedByWordType($txt,$type){
		return  "SELECT a.kata1, a.kata2, a.jumlah FROM db_words.tb_sitti_kata2 AS a INNER JOIN db_words.tb_kata_tipe AS b ON (a.kata2 = b.kata) 
		WHERE (a.kata1 = '".$txt."') AND b.kata_tipe = ".$type." AND a.kata2 <> 'jakarta' AND a.kata2 <> 'indonesia'
		AND a.kata2 NOT IN ( SELECT kata FROM db_words.tbl_out ) ORDER BY a.jumlah DESC, b.kata_tipe ASC LIMIT 50
		";
	
	}
	/**
	 * 
	 * tarik related keyword filter berdasarkan jenis kata
	 * input katanya lebih dari 2 contoh, 'sepeda motor'
	 * 1 -> 
	 * 2 -> 
	 * 3 ->
	 * @param $txt
	 * @param $type
	 */
	function getRelatedByWordType2($txt,$type){
		
		return "SELECT 1 AS id, a.kata2 AS keyword, a.kata1 as kata2, a.jumlah FROM (SELECT a.kata2, a.kata1, a.jumlah, b.kata_tipe 
		FROM db_words.tb_2kata_1kata a INNER JOIN db_words.tb_kata_tipe b ON a.kata1 = b.kata WHERE (a.kata2 = '".$txt."') AND (b.kata_tipe = ".$type." )
		ORDER BY a.jumlah DESC, b.kata_tipe LIMIT 50 ) a LEFT OUTER JOIN db_words.tbl_out b ON a.kata1 = b.kata WHERE (b.kata IS NULL)";
	}
	function getRelatedBrand($txt){
		  return "SELECT kata, brand, brand AS kata2, 1 AS jum FROM db_words.tbl_brands
				WHERE (kata = '".$txt."') ORDER BY jum LIMIT 10";
	}
	//** query untuk social media dibawah ini ***/
	
	/**
	 * 
	 * input 1 kata
	 * @param $txt
	 */
	function getRelatedWebKeyword($txt){
		return "SELECT a.kata1 as input, a.kata2 as kata1, a.jumlah
FROM db_words.tb_socmed AS a INNER JOIN db_words.tb_kata_tipe AS b ON (a.kata2 = b.kata) WHERE (a.kata1 = '".$txt."') AND b.kata_tipe <> 5 AND a.kata2 <> 'jakarta' AND a.kata2 <> 'indonesia'
AND a.kata2 NOT IN ( SELECT kata FROM db_words.tbl_out ) ORDER BY a.jumlah DESC, b.kata_tipe ASC LIMIT 50
		";
	}
	/**
	 * 
	 * input 2 kata
	 * @param $txt
	 */
	function getRelatedWebKeyword2($txt){
		
		return "SELECT 1 AS id, a.kata2 AS keyword, a.kata1 , a.jumlah
FROM (SELECT a.kata2, a.kata1, a.jumlah, b.kata_tipe
FROM db_words.tb_2kata_1kata_sm a INNER JOIN
db_words.tb_kata_tipe b ON a.kata1 = b.kata
WHERE (a.kata2 = '".$txt."') AND (b.kata_tipe <> 5)
ORDER BY a.jumlah DESC, b.kata_tipe LIMIT 50 ) a LEFT OUTER JOIN
db_words.tbl_out b ON a.kata1 = b.kata WHERE (b.kata IS NULL)";
	}
	
	function getWebKeywordByType($txt,$type){
		return "SELECT a.kata1 as input, a.kata2 as kata1, a.jumlah 
		FROM db_words.tb_socmed AS a INNER JOIN db_words.tb_kata_tipe AS b ON (a.kata2 = b.kata) WHERE (a.kata1 = '".$txt."') 
		AND b.kata_tipe = ".$type." AND a.kata2 <> 'jakarta' AND a.kata2 <> 'indonesia'
AND a.kata2 NOT IN ( SELECT kata FROM db_words.tbl_out ) ORDER BY a.jumlah DESC, b.kata_tipe ASC LIMIT 50
		";
	}
	function getWebKeywordByType2($txt,$type){
		return "SELECT 1 AS id, a.kata2 AS keyword, a.kata1 as kata2, a.jumlah 
		FROM (SELECT a.kata2, a.kata1, a.jumlah, b.kata_tipe 
		FROM db_words.tb_2kata_1kata_sm a INNER JOIN db_words.tb_kata_tipe b ON a.kata1 = b.kata 
		WHERE (a.kata2 = '".$txt."') AND (b.kata_tipe = ".$type." )
		ORDER BY a.jumlah DESC, b.kata_tipe LIMIT 50 ) a 
		LEFT OUTER JOIN db_words.tbl_out b ON a.kata1 = b.kata 
		WHERE (b.kata IS NULL)";
	}
	function getWebKeywordByBrand($txt,$type){
		
	}
	/**
   * query untuk mendapatkan saran kata
   * @param $txt string kata
   */
	function getSuggestion($txt){
	  return "SELECT * FROM (
          SELECT a.kata2, SUM(a.jumlah) AS jumlah
          FROM db_words.tb_sitti_kata2 AS a
          WHERE  (((a.kata1) IN (".$txt.")))
          GROUP BY a.kata1, a.kata2
          ORDER BY SUM(a.jumlah) DESC
          LIMIT 45 ) a WHERE kata2 NOT IN (".$txt.")
          LIMIT 40";
		/*return "SELECT a.kata2, SUM(a.jumlah) AS jumlah
				FROM db_words.tb_sitti_kata2 AS a
				WHERE  (((a.kata1) IN (".$txt.")))
				GROUP BY a.kata1, a.kata2
				ORDER BY SUM(a.jumlah) DESC
				LIMIT 30";
    */
	}
	/**
	 * 
	 * suggestion key for simulation purpose
	 */
	function getSuggestion2($txt,$start,$total=100){
		if($start==null){
			$start=0;
			
		}
/*		 return "
          SELECT a.kata2, SUM(a.jumlah) AS jum
          FROM db_words.tb_sitti_kata2 AS a
          WHERE a.kata1 IN (".$txt.")
          AND a.kata2 NOT IN (".$txt.")
          GROUP BY a.kata1, a.kata2
          ORDER BY SUM(a.jumlah) DESC
          LIMIT ".$start.",".$total;*/
		 $sql =  "
          SELECT a.kata2,a.jum as jumlah
          FROM db_words.tb_suggestion_words AS a
          WHERE a.kata1 IN (".$txt.")
          AND a.kata2 NOT IN (".$txt.")
          ORDER BY a.jum DESC
          LIMIT ".$start.",".$total;
		// print $sql;
		 return $sql;
	}
	/**
   * mendapatkan saran frase dari input user
   * @param $txt 
   */
	function getPhraseSuggestion($txt){
	  $sql="SELECT kata1, kata2, kata3, jumlah
FROM db_words.tb_k1k2_2kata
WHERE (kata1 IN(".$txt."))
ORDER BY jumlah DESC LIMIT 5";
	  return $sql;
	}
	/**
   * save user input (phrase)
   */
	function savePhraseQuery($txt,$ip,$sittiID){
	  $sql = "INSERT INTO db_words.sitti_saran_kata_user
      ( kata, ipaddress, sittiID )
      VALUES('".mysql_escape_string(strip_tags(stripslashes($txt)))."', '".ip2long($ip)."', '".$sittiID."' )";
      return $sql;
	}
}
?>