<?
//////////////////////////////////////////////////////////////////////////////////////////////////////
// extract post data - get everything between start_data_fields and end_data_fields
//////////////////////////////////////////////////////////////////////////////////////////////////////
function extract_post_data ($data,$borders="_data_fields"){
	$keys = array_keys($data);
	$start = array_search("start".$borders,$keys);
	$end = array_search("end".$borders,$keys);
	if($start === false ||$end === false){
		return; // no match for borders so return emtpy data set
		} else {
		return array_slice($data,$start+1,$end-$start-1,true);
		}
	}
?>