<?php
class SITTIKloutData extends SQLData{
	function SITTIKloutData(){
		parent::SQLData();
	}
	
	function getAllCategory(){
		$output = array();
		$this->open(2);
		$query = "SELECT id, category FROM klout_data.category WHERE n_status=1";
		$rs = $this->fetch($query,1);
		$this->close();
		for($i=0;$i<sizeof($rs);$i++){
			$output[$i]['id'] = $rs[$i]['id'];
			$output[$i]['category'] = $rs[$i]['category'];
		}
		return $output;
	}
	
	function getDataStream($category_id, $last_id, $brand){
		$this->open(2);
		if ($category_id==0){
			if ($last_id){
				$query = "SELECT id,raw_id,user,tweet,timestamp_ts,avatar
				FROM klout_raw.tweet_feed USE INDEX (timestamp_ts)
				WHERE id>$last_id ORDER BY id DESC LIMIT 20";
			}else{
				$last_id = 0;
				$limit_ts = time()-2;
				$query = "SELECT id,raw_id,user,tweet,timestamp_ts,avatar
				FROM klout_raw.tweet_feed USE INDEX (timestamp_ts)
				WHERE timestamp_ts>=$limit_ts ORDER BY timestamp_ts DESC LIMIT 20";
			}
		}else{
			if ($last_id){
				$query = "SELECT id,cat_id,raw_id,user,tweet,timestamp_ts,avatar
				FROM klout_raw.categorical_tweet_feed USE INDEX (category_id)
				WHERE cat_id=$category_id AND id>$last_id ORDER BY id DESC LIMIT 20";
			}else{
				$last_id = 0;
				$limit_ts = time()-2;
				$query = "SELECT id,cat_id,raw_id,user,tweet,timestamp_ts,avatar
				FROM klout_raw.categorical_tweet_feed USE INDEX (category_ts)
				WHERE cat_id=$category_id AND timestamp_ts>=$limit_ts ORDER BY timestamp_ts DESC LIMIT 20";
			}
		}
		// echo $query;
		$rs = $this->fetch($query,1);
		if (sizeof($rs)>0){
			$last_id = $rs[0]['id'];
		}else{
			if ($last_id==0){
				if ($category_id==0){
					$query = "SELECT id,raw_id,user,tweet,timestamp_ts,avatar
					FROM klout_raw.tweet_feed USE INDEX (timestamp_ts)
					ORDER BY timestamp_ts DESC LIMIT 10";
				}else{
					$query = "SELECT id,cat_id,raw_id,user,tweet,timestamp_ts,avatar
					FROM klout_raw.categorical_tweet_feed USE INDEX (category_ts)
					WHERE cat_id=$category_id
					ORDER BY timestamp_ts DESC LIMIT 10";
				}
				$rs = $this->fetch($query,1);
				if (sizeof($rs)>0){
					$last_id = $rs[0]['id'];
				}
			}
		}
		$data = array();
		for ($i=0;$i<sizeof($rs);$i++){
			$username = $rs[$i]['user'];
			$tweet = $rs[$i]['tweet'];
			$avatar = $rs[$i]['avatar'];
			if ($username && $tweet){
				array_push($data,array("username"=>$username, "tweet"=>$tweet,"avatar"=>$avatar));
			}
		}
		$output = array(array("record"=>$last_id),$data);
		$this->close();
		return json_encode($output);
	}
	
	function getSNAData($category_id, $method){
		// get sna data
		$query = "";
		if ($method=="keyword"){
			$query = "SELECT brand AS kata1, keyword AS kata2, num AS size
					  FROM klout_report.cat_brand_keyword_sna
					  WHERE cat_id = " . $category_id . "
					  ";
		}else if ($method=="user"){
			$query = "SELECT brand AS kata1, tweep AS kata2, num AS size
					  FROM klout_report.cat_tweep_brand_sna
					  WHERE cat_id = " . $category_id . "
					  ";
		}else if ($method=="brand"){
			$query = "SELECT brand1 AS kata1, brand2 AS kata2, num AS size
					  FROM klout_report.cat_brand_brand_sna
					  WHERE cat_id = " . $category_id . "
					  ";
		}
		$this->open(2);
		$rs = $this->fetch($query,1);
		$this->close();
		if ($method=="brand"){
			$rs = $this->getBrandEntityId($rs);
		}else{
			$rs = $this->getEntityId($rs);
		}
		$wordNodeSize = $this->getEachNodeSize($rs);
		
		if ($method=="brand"){
			$sna_data = $this->constructBrandSNAData($rs,$wordNodeSize);
		}else{
			$sna_data = $this->constructSNAData($rs,$wordNodeSize);
		}
		
		return json_encode($sna_data);
	}
	
	function getBrandEntityId($data){
		$entities = array();
		$ret_array = array();
		$idx = 0;
		for ($i=0;$i<sizeof($data);$i++){
			$kata1 = $data[$i]['kata1'];
			if (!array_key_exists($kata1, $entities)) {
				$entities[ $kata1 ] = $idx;
				$idx++;
			}
			$kata2 = $data[$i]['kata2'];
			if (!array_key_exists($kata2, $entities)) {
				$entities[ $kata2 ] = $idx;
				$idx++;
			}
			$ret_array[$i]['kata1'] = $data[$i]['kata1'];
			$ret_array[$i]['brand_id1'] = $entities[ $kata1 ];
			$ret_array[$i]['kata2'] = $data[$i]['kata2'];
			$ret_array[$i]['brand_id2'] = $entities[ $kata2 ];
			$ret_array[$i]['size'] = $data[$i]['size'];
		}
		return $ret_array;
	}
	
	function getEntityId($data){
		$entities = array();
		$ret_array = array();
		$idx = 0;
		for ($i=0;$i<sizeof($data);$i++){
			$kata1 = $data[$i]['kata1'];
			if (!array_key_exists($kata1, $entities)) {
				$entities[ $kata1 ] = $idx;
				$idx++;
			}
			$kata2 = $data[$i]['kata2'];
			if (!array_key_exists($kata2, $entities)) {
				$entities[ $kata2 ] = $idx;
				$idx++;
			}
			$ret_array[$i]['kata1'] = $data[$i]['kata1'];
			$ret_array[$i]['brand_id'] = $entities[ $kata1 ];
			$ret_array[$i]['kata2'] = $data[$i]['kata2'];
			$ret_array[$i]['keyword_id'] = $entities[ $kata2 ];
			$ret_array[$i]['size'] = $data[$i]['size'];
		}
		return $ret_array;
	}
	
	function getEachNodeSize($data){
		$wordNodeSize = array();
		for ($i=0;$i<sizeof($data);$i++){
			$wordNodeSize[$data[$i]['kata1']] = $data[$i]['size'];
		}
		
		return $wordNodeSize;
	}
	
	function constructBrandSNAData($data,$wordNodeSize){
		$prev_brand = "";
		$brands = array();
		$obj_rel = array();
		$leaves = array();
		foreach ($data as $row){
			if (strcasecmp ($prev_brand,$row['kata1']) == 0) {
				$adjacent_obj["nodeFrom"] = "node" . $row['brand_id1'];
				$adjacent_obj["nodeTo"] = "node" . $row['brand_id2'];
				$adjacent_obj["data"]["\$color"] = "#FFFFFF";
				$adjacent_obj["data"]["\$lineWidth"] = "1";

				array_push($obj['adjacencies'], $adjacent_obj);
			} else {
				if ($prev_brand != ""){
					array_push($obj_rel,$obj);
				}
				$obj['id'] = "node" . $row['brand_id1'];
				$obj['name'] = $row['kata1'];
				$obj['data']["\$color"] = "#83548B";
				$obj['data']["\$type"] = "circle";
				$obj['data']["\$dim"] = $wordNodeSize[$row['kata1']];
				
				$obj['adjacencies'] = array();
				
				$adjacent_obj["nodeFrom"] = "node" . $row['brand_id1'];
				$adjacent_obj["nodeTo"] = "node" . $row['brand_id2'];
				$adjacent_obj["data"]["\$color"] = "#FFFFFF";
				$adjacent_obj["data"]["\$lineWidth"] = "1";

				$brands[$row['kata1']] = $row['brand_id1'];
				array_push($obj['adjacencies'], $adjacent_obj);
			}
			$prev_brand = $row['kata1'];
			if (!array_key_exists( $row['kata2'] , $leaves )) {
				$leaves[ $row['kata2'] ] = $row['brand_id2'];
			}
		}
		array_push($obj_rel,$obj);

		if (sizeof($leaves) > 0) {
			foreach (array_keys($leaves) as $key) {
				if (!array_key_exists( $key , $brands )) {
					$obj['id'] = "node" . $leaves[$key];
					$obj['name'] = $key;
					$obj['data']["\$color"] = "#83548B";
					$obj['data']["\$type"] = "circle";
					$obj['data']["\$dim"] = 5;
					$obj['adjacencies'] = array();
					array_push($obj_rel,$obj);
				}
			}
		}
		
		return $obj_rel;
	}
	
	function constructSNAData($data,$wordNodeSize){
		$prev_brand = "";
		$obj_rel = array();
		$leaves = array();
		foreach ($data as $row){
			if (strcasecmp ($prev_brand,$row['kata1']) == 0) {
				$adjacent_obj["nodeFrom"] = "node" . $row['brand_id'];
				$adjacent_obj["nodeTo"] = "node" . $row['keyword_id'];
				$adjacent_obj["data"]["\$color"] = "#FFFFFF";
				$adjacent_obj["data"]["\$lineWidth"] = "1";

				array_push($obj['adjacencies'], $adjacent_obj);
			} else {
				if ($prev_brand != ""){
					array_push($obj_rel,$obj);
				}
				
				$obj['id'] = "node" . $row['brand_id'];
				$obj['name'] = $row['kata1'];
				$obj['data']["\$color"] = "#AB499C";
				$obj['data']["\$type"] = "circle";
				$obj['data']["\$dim"] = $wordNodeSize[$row['kata1']];
				
				$obj['adjacencies'] = array();

				$adjacent_obj["nodeFrom"] = "node" . $row['brand_id'];
				$adjacent_obj["nodeTo"] = "node" . $row['keyword_id'];
				$adjacent_obj["data"]["\$color"] = "#FFFFFF";
				$adjacent_obj["data"]["\$lineWidth"] = "1";

				array_push($obj['adjacencies'], $adjacent_obj);
			}
			$prev_brand = $row['kata1'];
			if (!array_key_exists( $row['kata2'] , $leaves )) {
				$leaves[ $row['kata2'] ] = $row['keyword_id'];
			}
		}
		array_push($obj_rel,$obj);

		if (sizeof($leaves) > 0) {
			foreach (array_keys($leaves) as $key) {
				$obj['id'] = "node" . $leaves[$key];
				$obj['name'] = $key;
				$obj['data']["\$color"] = "#FE0000";
				$obj['data']["\$type"] = "circle";
				$obj['data']["\$dim"] = 5;
				$obj['adjacencies'] = array();
				array_push($obj_rel,$obj);
			}
		}
		
		return $obj_rel;
	}
	
	function getKeywordCloudData($category_id, $brand){
		// get keyword cloud data
		if ($brand){
			
		}else{
			$query = "SELECT keyword AS text, num AS weight
					FROM klout_report.cat_keyword_wordcloud
					WHERE cat_id = ".$category_id."
					ORDER BY num DESC
					LIMIT 100;";
		}
		$this->open(2);
		$rs = $this->fetch($query,1);
		$this->close();
		if(is_array($rs)){
			$word_list = $rs;
		}
		
		$n = sizeof($word_list);
		
		for($i=0;$i<$n;$i++){
			// $word_list[$i]['title'] = $word_list[$i]['weight'];
			$word_list[$i]['url'] = "#";
			$word_list[$i]['title'] = "Beriklan di SITTI dengan keyword berikut";
		}
		
		return json_encode($word_list);
	}
	
	function getTopKeyword($category_id, $brand){
		// get top keyword
		if ($brand){
			
		}else{
			$query = "SELECT keyword, num
						FROM klout_report.cat_keyword_wordcloud
						WHERE cat_id = ".$category_id."
						ORDER BY num DESC
						LIMIT 10;";
		}
		$this->open(2);
		$rs = $this->fetch($query,1);
		$this->close();
		
		return json_encode($rs);
	}
	
	function getTopUser($category_id, $brand){
		// get top user
		if ($brand){
			
		}else{
			$query = "SELECT tweep, score, avatar
						FROM klout_report.cat_tweep_score
						WHERE cat_id = ".$category_id."
						ORDER BY score DESC
						LIMIT 10;";
		}
		$this->open(2);
		$rs = $this->fetch($query,1);
		$this->close();
		
		return json_encode($rs);
	}
	
	function getStatsData($category_id, $brand){
		$output;
		// get stats data
		if ($brand){
			
		}else{
			$query = "SELECT users_num, conversation, min_ts, max_ts
						FROM klout_report.cat_stat
						WHERE cat_id = ".$category_id.";";
			$this->open(2);
			$rs = $this->fetch($query);
			$this->close();
			$output['total_users'] = $rs['users_num'];
			$output['total_conversations'] = $rs['conversation'];
			$output['date'] = date("j M Y",$rs['min_ts'])."-".date("j M Y",$rs['max_ts']);
			
			$query = "SELECT brand, user_num, conversation
						FROM klout_report.cat_brand_stat
						WHERE cat_id = ".$category_id." AND brand <> 'others'
						UNION
						SELECT brand, user_num, conversation
						FROM klout_report.cat_brand_stat
						WHERE cat_id = ".$category_id." AND brand = 'others'
						;";
			$this->open(2);
			$rs = $this->fetch($query,1);
			$this->close();
			$brands = array();
			$users = array();
			$conversations = array();
			for ($i=0;$i<sizeof($rs);$i++){
				array_push($brands, $rs[$i]['brand']);
				array_push($users, intval($rs[$i]['user_num']));
				array_push($conversations, intval($rs[$i]['conversation']));
			}
			$output['brands'] = $brands;
			$output['users'] = $users;
			$output['conversations'] = $conversations;
		}
		$query = "SELECT category
					FROM klout_data.category
					WHERE id = ".$category_id.";";
		$this->open(2);
		$rs = $this->fetch($query);
		$this->close();
		$output['category'] = $rs['category'];
		
		return json_encode(array($output));
	}
	
	function insertFeedback($name, $telp, $email, $feedback){
		$query = "INSERT INTO klout_raw.visitor_comment (nama, telp, email, komentar)
					VALUES ('".$name."', '".$telp."', '".$email."', '".$feedback."')
					;";
		$this->open(2);
		$q = $this->query($query);
		$this->close();
	}
	
	function insertBrandContact($brand, $name, $telp, $email, $message){
		$query = "INSERT INTO klout_raw.brand_contact_message (brand, nama, telp, email, pesan)
					VALUES ('".$brand."', '".$name."', '".$telp."', '".$email."', '".$message."')
					;";
		$this->open(2);
		$q = $this->query($query);
		$this->close();
	}
	
	function insertCategoryContact($category, $name, $telp, $email, $message){
		$query = "INSERT INTO klout_raw.category_contact_message (category, nama, telp, email, pesan)
					VALUES ('".$category."', '".$name."', '".$telp."', '".$email."', '".$message."')
					;";
		$this->open(2);
		$q = $this->query($query);
		$this->close();
	}
}
?>