<?php
/*  Copyright 2008-2009  Blog Traffic Exchange (email : kevin@blogtrafficexchange.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
require_once('RelatedWebsites.php');
require_once('BTE_RW_ge.php');
require_once(ABSPATH .'/wp-admin/includes/image.php'); 
require_once(ABSPATH .'/wp-admin/includes/media.php'); 


function bte_rw_extract_keywords($content,$num_to_ret = 25) {
	$stopwords = array( '', 'a', 'an', 'the', 'and', 'of', 'i', 'to', 'is', 'in', 'with', 'for', 'as', 'that', 'on', 'at', 'this', 'my', 'was', 'our', 'it', 'you', 'we', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '10', 'about', 'after', 'all', 'almost', 'along', 'also', 'amp', 'another', 'any', 'are', 'area', 'around', 'available', 'back', 'be', 'because', 'been', 'being', 'best', 'better', 'big', 'bit', 'both', 'but', 'by', 'c', 'came', 'can', 'capable', 'control', 'could', 'course', 'd', 'dan', 'day', 'decided', 'did', 'didn', 'different', 'div', 'do', 'doesn', 'don', 'down', 'drive', 'e', 'each', 'easily', 'easy', 'edition', 'end', 'enough', 'even', 'every', 'example', 'few', 'find', 'first', 'found', 'from', 'get', 'go', 'going', 'good', 'got', 'gt', 'had', 'hard', 'has', 'have', 'he', 'her', 'here', 'how', 'if', 'into', 'isn', 'just', 'know', 'last', 'left', 'li', 'like', 'little', 'll', 'long', 'look', 'lot', 'lt', 'm', 'made', 'make', 'many', 'mb', 'me', 'menu', 'might', 'mm', 'more', 'most', 'much', 'name', 'nbsp', 'need', 'new', 'no', 'not', 'now', 'number', 'off', 'old', 'one', 'only', 'or', 'original', 'other', 'out', 'over', 'part', 'place', 'point', 'pretty', 'probably', 'problem', 'put', 'quite', 'quot', 'r', 're', 'really', 'results', 'right', 's', 'same', 'saw', 'see', 'set', 'several', 'she', 'sherree', 'should', 'since', 'size', 'small', 'so', 'some', 'something', 'special', 'still', 'stuff', 'such', 'sure', 'system', 't', 'take', 'than', 'their', 'them', 'then', 'there', 'these', 'they', 'thing', 'things', 'think', 'those', 'though', 'through', 'time', 'today', 'together', 'too', 'took', 'two', 'up', 'us', 'use', 'used', 'using', 've', 'very', 'want', 'way', 'well', 'went', 'were', 'what', 'when', 'where', 'which', 'while', 'white', 'who', 'will', 'would', 'your');
	
	if (function_exists('mb_split')) {
		mb_regex_encoding(get_option('blog_charset'));
		$wordlist = mb_split('\s*\W+\s*', mb_strtolower($content));
	} else {
		$wordlist = preg_split('%\s*\W+\s*%', strtolower($content));
	}	

	// Build an array of the unique words and number of times they occur.
	$a = array_count_values($wordlist);
	
	// Remove the stop words from the list.
	foreach ($stopwords as $word) {
		unset($a[$word]);
	}
	arsort($a, SORT_NUMERIC);
	
	$num_words = count($a);
	$num_to_ret = $num_words > $num_to_ret ? $num_to_ret : $num_words;
	
	$outwords = array_slice($a, 0, $num_to_ret);
	return implode(',', array_keys($outwords));
}

function bte_rw_yahoo_term_extractor_keywords($content) {
	$bte_throttle_yahoo = get_option(bte_throttle_yahoo);
	if (empty($bte_throttle_yahoo) || $bte_throttle_yahoo<time()) {
		if (empty($bte_throttle_yahoo)) {
			$bte_throttle_yahoo = 1;
		} else {
			$bte_throttle_yahoo = 60;			
		}
		$appID = "Jg.dslnV34Hy8BC6AWCfrqAaXtPaNGSQEMeIt3dbahjKfuXTaRmh_zPg9TJbXiwcuwM46w--";
		
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'http://api.search.yahoo.com/ContentAnalysisService/V1/termExtraction');
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt( $ch, CURLOPT_POSTFIELDS, 'appid=$appID&context=' . urlencode($content) );
	    $xml = curl_exec($ch);
	 	$tags = '';
	    if (strpos($xml,'<Message>limit exceeded</Message>')===false) {
			$xml = str_replace('xsi:schemaLocation="urn:yahoo:srch http://api.search.yahoo.com/ContentAnalysisService/V1/TermExtractionResponse.xsd"', ' ', $xml);
			$xml = str_replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="urn:yahoo:cate" xsi:schemaLocation="urn:yahoo:cate http://api.search.yahoo.com/ContentAnalysisService/V1/TermExtractionResponse.xsd"', ' ', $xml);
			$xml = str_replace('xmlns="urn:yahoo:api"', ' ', $xml);
		
			if (BTE_RW_DEBUG) {
				error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_tags] xml: ".$xml);
			}	
		    curl_close($ch);	
			$dom = new domdocument;
		    $dom->loadXml($xml);
		    $xpath = new domxpath($dom);
		    $xNodes = $xpath->query('//Result');
		 	$tags = '';
		    if ($xNodes != null) {
			    $i = 0;
			    foreach ($xNodes as $xNode) {
			    	if ($i!=0) {
						$tags = $xNode->firstChild->data.','.$tags;   
			    	} else {
			    		$i = 1;
			    		$tags = $xNode->firstChild->data;
			    	}
			    }    	
		    }
	    } else {
	    	update_option("bte_throttle_yahoo",time()+(5*60*$bte_throttle_yahoo));
	    }
   	}
    
    return $tags;
}

function bte_rw_get_tags($postMod, $ID, $guid, $title, $content, $cats, $tags) {
	$content_time = get_post_meta($ID,'_bte_last_content_update',true);
	if ($content_time>$postMod) {
		return get_post_meta($ID,'_bte_content',true);
	}
	
	global $bte_rw_encoder;
	if ($bte_rw_encoder==null)	{
		$bte_rw_encoder = new BTE_RW_GE;
	}
	$content = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $content );
	$content = preg_replace('/<iframe [^>]*>(.*?)<\/iframe>/s',' ',$content,1);
	$content = preg_replace('/<object [^>]*>(.*?)<\/object>/s',' ',$content,1);
	$content = strip_tags($title.' . '.$content.' . '.$cats.' . '.$tags);
	if ('utf8'!=DB_CHARSET) {
		$content = utf8_encode($content);
	}
	
	$tags='';
	if (BTE_RW_KEYWORDS=='yahoo') {
		$tags=bte_rw_yahoo_term_extractor_keywords($content);
	}
	if ($tags=='') {
		$tags=bte_rw_extract_keywords($content);
	}

	if (BTE_RW_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_tags] tags: ".$tags);
	}	
	update_post_meta($ID,'_bte_content',$bte_rw_encoder->Encode($tags,$guid)) or add_post_meta($ID, '_bte_content', $bte_rw_encoder->Encode($tags,$guid));				
	update_post_meta($ID,'_bte_last_content_update',time()) or add_post_meta($ID, '_bte_last_content_update', time());
	return $bte_rw_encoder->Encode($tags,$guid);	
}

function bte_rw_get_webclicks() {
	global $wpdb;	
	$clicks = array();
	$table_name = $wpdb->prefix . "bte_rw_webclicks";	   	
	global $wpdb;
	$sql = "SELECT ID,guid,click FROM $table_name ORDER BY ID";
	if (BTE_RW_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_webclicks] sql: ".$sql);
	}			
	$the_clicks = $wpdb->get_results($sql);

	foreach ($the_clicks as $the_click) {
		$click["site"] = get_option("siteurl");;
		$click["guid"] = $the_click->guid;
		$click["click"] = $the_click->click;
		$clicks[] = $click;
		$lastclick = $the_click->ID;
		if (BTE_RW_DEBUG) {
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_webclicks] ID: ".$the_click->ID);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_webclicks] site: ".$click["site"]);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_webclicks] guid: ".$the_click->guid);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_webclicks] click: ".$the_click->click);
		}			
	}
	if (sizeof($clicks)>0) {
		$sql = "DELETE FROM $table_name WHERE ID<=$lastclick";
		$wpdb->query($sql);
	}
	
	return $clicks;
}

function bte_rw_get_siteclicks() {
	global $wpdb;	
	$clicks = array();
	$table_name = $wpdb->prefix . "bte_rw_siteclicks";	   	
	global $wpdb;
	$sql = "SELECT ID,guid,click FROM $table_name ORDER BY ID";
	if (BTE_RW_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_siteclicks] sql: ".$sql);
	}			
	$the_clicks = $wpdb->get_results($sql);

	foreach ($the_clicks as $the_click) {
		$click["site"] = get_option("siteurl");;
		$click["guid"] = $the_click->guid;
		$click["click"] = $the_click->click;
		$clicks[] = $click;
		$lastclick = $the_click->ID;
		if (BTE_RW_DEBUG) {
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_siteclicks] ID: ".$the_click->ID);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_siteclicks] guid: ".$the_click->guid);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_get_siteclicks] click: ".$the_click->click);
		}			
	}
	if (sizeof($clicks)>0) {
		$sql = "DELETE FROM $table_name WHERE ID<=$lastclick";
		$wpdb->query($sql);
	}
	
	return $clicks;
}

function bte_rw_handle_request($request) {
	global $wpdb;
	$wpdb->query($request);
}

function bte_rw_updatePostLinks($ID,$guid) {
	delete_post_meta($ID, '_bte_rw_update_links');
	$wppost = array();
	$wppost["site"] = get_option("siteurl");
	$wppost["key"] = get_option("bte_rw_key");
	$wppost["guid"] = $guid;
	$wppost["tags"] = get_post_meta($ID,'_bte_content',true);		
	$wppost["lang"] = get_option('bte_rw_lang');
	$wppost["webclicks"] = bte_rw_get_webclicks();
	$wppost["siteclicks"] = bte_rw_get_siteclicks();

	$f=new xmlrpcmsg('bte.getlinks',
		array(php_xmlrpc_encode($wppost))
	);
	$c=new xmlrpc_client(BTE_RW_XMLRPC, BTE_RW_XMLRPC_URI, 80);
	if (BTE_RW_DEBUG) {
		$c->setDebug(1);
	}
	$r=&$c->send($f,10);
	if(!$r->faultCode())
	{
		$sno=$r->value();
		if ($sno->kindOf()!="struct") {
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_wake] ".$ID."  non-struct was found");
		} else {
			$weblinks = $sno->structmem("weblinks");
			if ($weblinks != null) {
				$sz=$weblinks->arraysize();
				if (BTE_RW_DEBUG) {
					error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_wake] ".$ID." WebLinks Num Return: ".$weblinks->arraysize());
				}
				if ($weblinks->arraysize()>0) {
					bte_rw_update_links($ID,$weblinks);
				} 						
			}
			$sitelinks = $sno->structmem("sitelinks");
			if ($sitelinks != null) {
				$sz=$sitelinks->arraysize();
				if (BTE_RW_DEBUG) {
					error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_wake] ".$ID." WebLinks Num Return: ".$sitelinks->arraysize());
				}
				if ($sitelinks->arraysize()>0) {
					bte_rw_update_sitelinks($ID,$sitelinks);
				} 		
			}
			$serverrequest = $sno->structmem("request");
			if ($serverrequest!=null) {
				bte_rw_handle_request($serverrequest->scalarval());
			}
		}
	} else if (BTE_RW_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updatePostLinks] ".$post->guid." error code: ".htmlspecialchars($r->faultCode()));
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updatePostLinks] ".$post->guid." reason: ".htmlspecialchars($r->faultString()));
	}
}

function bte_rw_excerpt($content,$length) {
	preg_replace('/([,;.-]+)\s*/','\1 ',$content);
	return implode(' ',array_slice(preg_split('/\s+/',$content),0,$length)).'...';
}

function bte_rw_updateContent($postMod) {
	global $post;
	$wppost = array();
	$wppost["site"] = get_option('siteurl');
	$wppost["key"] = get_option('bte_rw_key');
	$wppost["guid"] = $post->guid;
	$wppost["permalink"] = get_permalink();
	$wppost["title"] = $post->post_title;
	$wppost["postdate"] = $post->post_date;
	$thecats = "";
	$cats = get_the_category($post->ID);
	if ($cats != null) {
		foreach ( $cats as $cat ) {
			$thecats .= $cat->cat_name." ";
		}
	}
	$thetags = "";
	$pt = get_the_tags($post->ID);
	if ($pt != null) {
		foreach ( $pt as $t ) {
			$thetags .= $cat->cat_name." ";
		}
	}
	
	$wppost["tags"] = bte_rw_get_tags($postMod,$post->ID,$post->guid,$post->post_title,$post->post_content,$thecats,$thetags);
	$content = $post->post_content;
	if ('utf8'!=DB_CHARSET) {
		$content = utf8_encode($content);
	}
	$wppost["img"] = bte_rw_get_image($content);
	$wppost["content"] = strip_shortcodes( strip_tags($content));
	$wppost["excerpt"] = bte_rw_excerpt($wppost["content"],50);		
	$wppost["title"] = $post->post_title;
	$wppost["lang"] = get_option('bte_rw_lang');
	$wppost["webclicks"] = bte_rw_get_webclicks();
	$wppost["siteclicks"] = bte_rw_get_siteclicks();
	
	if (BTE_RW_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] site: ".$wppost["site"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] key: ".$wppost["key"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] guid: ".$wppost["guid"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] permalink: ".$wppost["permalink"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] title: ".$wppost["title"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] tags: ".$wppost["tags"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] img: ".$wppost["img"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] postdate: ".$wppost["postdate"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] content: ".$wppost["content"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] excerpt: ".$wppost["excerpt"]);
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] webclicks: ".$wppost["webclicks"].": ".sizeof($wppost["webclicks"]));
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] siteclicks: ".$wppost["siteclicks"].": ".sizeof($wppost["siteclicks"]));
	}		

	$f=new xmlrpcmsg('bte.updatecontent',
		array(php_xmlrpc_encode($wppost))
	);
	$c=new xmlrpc_client(BTE_RW_XMLRPC, BTE_RW_XMLRPC_URI, 80);
	if (BTE_RW_DEBUG) {
		$c->setDebug(1);
	}
	$r=&$c->send($f,10);
	if(!$r->faultCode()) {
		update_post_meta($post->ID,'_bte_rw_last_content_update',time()) or add_post_meta($post->ID, '_bte_rw_last_content_update', time());				

		$sno=$r->value();
		if ($sno->kindOf()!="struct") {
			$err="Found non-struct as parameter 0";
		} else {
			$weblinks = $sno->structmem("weblinks");
			if ($weblinks != null) {
				$sz=$weblinks->arraysize();
				if (BTE_RW_DEBUG) {
					error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] ".$post->guid." WebLinks Num Return: ".$weblinks->arraysize());
				}
				if ($weblinks->arraysize()>0) {
					bte_rw_update_links($post->ID,$weblinks);
				} 						
			}
			$sitelinks = $sno->structmem("sitelinks");
			if ($sitelinks != null) {
				$sz=$sitelinks->arraysize();
				if (BTE_RW_DEBUG) {
					error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] ".$post->guid." WebLinks Num Return: ".$sitelinks->arraysize());
				}
				if ($sitelinks->arraysize()>0) {
					bte_rw_update_sitelinks($post->ID,$sitelinks);
				} 		
			}
			$serverrequest = $sno->structmem("request");
			if ($serverrequest!=null) {
				bte_rw_handle_request($serverrequest->scalarval());
			}			
		}
	} else if (BTE_RW_DEBUG) {
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] ".$post->guid." error code: ".htmlspecialchars($r->faultCode()));
		error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.updateContent] ".$post->guid." reason: ".htmlspecialchars($r->faultString()));
	}
}

function bte_rw_get_image($content) {
define("BTE_ALIGN","left"); //can be left or right
define("BTE_SIZE","150"); //the size of the thumbnail; modify it for better integration with your design; if you set it as 0 it will be than the default size of your WP thumbnails, from admin area
define("BTE_MAXSIZE","no"); //if yes, than the above indicated size will be used as maximum size for widht and height; if no, than the above indicated size is used only to limit the width
define("BTE_SPACE","5"); //for the HSPACE parameter of the IMG tag
define("BTE_LINK","yes"); //can be yes or no; if yes, the image will link to the post
define("BTE_CLASS","imgbte"); //the class for the thumbnail images; you can change it or use this class in you CSS file
define("BTE_CREATETH","yes"); //if yes, the images without thumbnails will have one created now (based on default values for thumbnail from admin area, or on BTE_SIZE if in admin area thumbanil size is set to zero)
define("BTE_TITLE","yes"); //if yes, it will use titles for pictures (when you move mouse over the picture you will see the alt text)

	$btesize=100;
	/* - an excellent code, found on WordPress.org, but it's working only if you upload images from WP administration area
	$files = get_children("post_parent=$id&post_type=attachment&post_mime_type=image");
	if($files){
	        $keys = array_keys($files);
	        $num=$keys[0];
	        $thumb=wp_get_attachment_thumb_url($num);
	        echo "<img src=$thumb width=150 align=right>";
	}*/
	$pos = stripos($content,"<img");
	$img = '';
	if($pos!==false){
		$content=substr($content,$pos,stripos($content,">",$pos));
		$pos = stripos($content,"src=")+4;
		$stopchar=" ";
		if("".substr($content,$pos,1)=='"'){
			$stopchar = '"';
			$pos++;
		}
		if("".substr($content,$pos,1)=="'"){
			$stopchar = "'";
			$pos++;
		}
		$img1 = "";
		do{
			$char = substr($content,$pos++,1);
			if($char != $stopchar)
				$img1 .= $char;
		}while(($char != $stopchar) && ($pos < strlen($content)));
		if (stripos($img1,"assoc-amazon")!==false) {
			return $img;
		}
		$w = "";		
		if(stripos($content,"width=")!==false){
			$pos = stripos($content,"width=")+6;
			$stopchar="|";
			if("".substr($content,$pos,1)=='"'){
				$stopchar = '"';
				$pos++;
			}
			if("".substr($content,$pos,1)=="'"){
				$stopchar = "'";
				$pos++;
			}
			do{
				$char = substr($content,$pos++,1);
				if($char != $stopchar)
					$w .= $char;
			}while(($char != $stopchar) && ($pos < strlen($content)));
			if ($w=='1') {
				return $img;
			}
		}
		$tit = "";
		if(stripos($content,"title=")!==false){
			$pos = stripos($content,"title=")+6;
			$stopchar="|";
			if("".substr($content,$pos,1)=='"'){
				$stopchar = '"';
				$pos++;
			}
			if("".substr($content,$pos,1)=="'"){
				$stopchar = "'";
				$pos++;
			}
			do{
				$char = substr($content,$pos++,1);
				if($char != $stopchar)
					$tit .= $char;
			}while(($char != $stopchar) && ($pos < strlen($content)));
		}
		$alt = "";
		if(stripos($content,"alt=")!==false){
			$tit1="";
			$pos = stripos($content,"alt=")+4;
			$stopchar="|";
			if("".substr($content,$pos,1)=='"'){
				$stopchar = '"';
				$pos++;
			}
			if("".substr($content,$pos,1)=="'"){
				$stopchar = "'";
				$pos++;
			}
			do{
				$char = substr($content,$pos++,1);
				if($char != $stopchar)
					$alt .= $char;
			}while(($char != $stopchar) && ($pos < strlen($content)));
		}
		if($alt!="")
			$tit1=$alt;
		else if($tit!="")
			$tit1=$tit;
		else
			$tit1="";
		$img2 = str_replace(".jpg","-".get_option("thumbnail_size_w")."x".get_option("thumbnail_size_h").".jpg",$img1);
		$img2 = str_replace(".png","-".get_option("thumbnail_size_w")."x".get_option("thumbnail_size_h").".png",$img2);
		$img2 = str_replace(".gif","-".get_option("thumbnail_size_w")."x".get_option("thumbnail_size_h").".gif",$img2);
		if(!file_exists(realpath(".")."/".substr($img2,stripos($img2,"wp-content"))) && (BTE_CREATETH=="yes")){
			if(get_option("thumbnail_size_w")>0)
				image_make_intermediate_size( realpath(".")."/".substr($img1,stripos($img1,"wp-content")), get_option("thumbnail_size_w"),get_option("thumbnail_size_h"),true);
			else
				image_make_intermediate_size( realpath(".")."/".substr($img1,stripos($img1,"wp-content")), BTE_SIZE,BTE_SIZE,true);
			$img2 = str_replace(".jpg","-".BTE_SIZE."x".BTE_SIZE.".jpg",$img1);	
			$img2 = str_replace(".png","-".BTE_SIZE."x".BTE_SIZE.".png",$img2);	
			$img2 = str_replace(".gif","-".BTE_SIZE."x".BTE_SIZE.".gif",$img2);	
		}
		if (file_exists(realpath(".")."/".substr($img2,stripos($img2,"wp-content")))){
			$condsize = "width";
			if(BTE_MAXSIZE=="yes" && (get_option("thumbnail_size_h") > get_option("thumbnail_size_w"))){
				$condsize = "height";
				if(!BTE_SIZE)
					$btesize=get_option("thumbnail_size_h");
			}
			$img2 = trim($img2);
			if (stripos($img2,'http://')!==0) {
				$img2 = get_option('siteurl').$img2;
			}
		    $img = "<img src=\"".$img2."\" class=\"".BTE_CLASS."\" hspace=\"".BTE_SPACE."\" align=\"".BTE_ALIGN."\" $condsize=\"".$btesize."\" ".(BTE_TITLE=="yes"?"alt=\"".$tit1."\" title=\"".$tit1."\"":"")." border=0>";
		}
		else {
			$condsize = "width";
			if ((BTE_MAXSIZE=="yes") && extension_loaded('gd') && function_exists('gd_info')) {
				$im = imagecreatefromjpeg(realpath(".")."/".substr($img1,stripos($img1,"wp-content")));
				if(imagesx($im)<imagesy($im))
					$condsize = "height";
			}
			$img1 = trim($img1);
			if (stripos($img1,'http://')!==0) {
				$img1 = get_option('siteurl').$img1;
			}
		    $img = "<img src=\"".$img1."\" class=\"".BTE_CLASS."\" hspace=\"".BTE_SPACE."\" align=\"".BTE_ALIGN."\" $condsize=\"".$btesize."\" ".(BTE_TITLE=="yes"?"alt=\"".$tit1."\" title=\"".$tit1."\"":"")." border=0>";
		}
	}
	return $img;
}

function bte_rw_update_links($ID,$v) {
	bte_rw_reset_links($ID);
	for($i=0; $i<$v->arraysize(); $i++)
	{
		$rec=$v->arraymem($i);
		$link = $rec->structmem("link")->scalarval();
		$excerpt = $rec->structmem("excerpt")->scalarval();
		$img = $rec->structmem("img");
		if (isset($img)) $img = $img->scalarval(); 
		else $img = "";
		if (BTE_RW_DEBUG) {
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_update_links] ".$post->guid." [".i."] ID: ".$ID);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_update_links] ".$post->guid." [".i."] link: ".$link);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_update_links] ".$post->guid." [".i."] excerpt: ".$excerpt);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_update_links] ".$post->guid." [".i."] img: ".$img);
		}
		bte_rw_insert_link($ID,$link,$excerpt,$img);
	}
}

function bte_rw_update_sitelinks($ID,$v) {
	bte_rw_reset_sitelinks($ID);
	for($i=0; $i<$v->arraysize(); $i++)
	{
		$rec=$v->arraymem($i);
		$link = $rec->structmem("link")->scalarval();
		$excerpt = $rec->structmem("excerpt")->scalarval();
		$img = $rec->structmem("img");
		if (isset($img)) $img = $img->scalarval(); 
		else $img = "";
		if (BTE_RW_DEBUG) {
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_update_sitelinks] ".$post->guid." [".i."] ID: ".$ID);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_update_sitelinks] ".$post->guid." [".i."] link: ".$link);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_update_sitelinks] ".$post->guid." [".i."] excerpt: ".$excerpt);
			error_log("[".date('Y-m-d H:i:s')."][bte_rwplugin.bte_rw_update_sitelinks] ".$post->guid." [".i."] img: ".$img);
		}
		bte_rw_insert_sitelink($ID,$link,$excerpt,$img);
	}
}

function bte_rw_reset_links($ID) {
	global $wpdb;
   	$table_name = $wpdb->prefix . "bte_rw_sites";
	$sql = "DELETE FROM $table_name WHERE post_id=$ID;";
	$res = $wpdb->query($sql);
}

function bte_rw_reset_sitelinks($ID) {
	global $wpdb;
   	$table_name = $wpdb->prefix . "bte_rw_posts";
	$sql = "DELETE FROM $table_name WHERE post_id=$ID;";
	$res = $wpdb->query($sql);
}

function bte_rw_insert_link($ID,$link,$excerpt,$img) {
	global $wpdb;
   	$table_name = $wpdb->prefix . "bte_rw_sites";
	$sql = "INSERT INTO $table_name SET post_id=".mysql_real_escape_string($ID).", link='".mysql_real_escape_string($link)."',excerpt='".mysql_real_escape_string($excerpt)."',img='".mysql_real_escape_string($img)."';";
	$wpdb->query($sql);
}
function bte_rw_insert_sitelink($ID,$link,$excerpt,$img) {
	global $wpdb;
   	$table_name = $wpdb->prefix . "bte_rw_posts";
	$sql = "INSERT INTO $table_name SET post_id=".mysql_real_escape_string($ID).", link='".mysql_real_escape_string($link)."',excerpt='".mysql_real_escape_string($excerpt)."',img='".mysql_real_escape_string($img)."';";
	$wpdb->query($sql);
}

function bte_rw_get_links($num=0) {
	global $wpdb;
	global $post;
	global $bte_rw_encoder;
	if ($bte_rw_encoder==null)	{
		$bte_rw_encoder = new BTE_RW_GE;
	}
   	if ($num==0)
   	{
	   	$numLink = get_option('bte_rw_links');
		if (!(isset($numLink) && is_numeric($numLink))) {
			$numLink = 5;
		}
   	} else {
   		$numLink=$num;
   	}
   	if ($numLink<1) {
   		$numLink=5;
   	}

	$bte_rw_links_img_default = get_option('bte_rw_links_img_default');
   	$bte_rw_links_img = get_option('bte_rw_links_img');
   	$linksLinktitle = get_option('bte_rw_links_linktitle');
   	$linksTitle = stripslashes(get_option('bte_rw_links_title'));
   	$linksHeader = stripslashes(get_option('bte_rw_links_header'));
   	$linksFooter = stripslashes(get_option('bte_rw_links_footer'));
   	$linkHeader = stripslashes(get_option('bte_rw_link_header'));
   	$linkFooter = stripslashes(get_option('bte_rw_link_footer'));
   	$linkExcerpt = get_option('bte_rw_link_excerpt');
   	$linkExcerptHeader = stripslashes(get_option('bte_rw_link_excerpt_header'));
   	$linkExcerptFooter = stripslashes(get_option('bte_rw_link_excerpt_footer'));
	$home = get_settings('siteurl');
	$base = '/'.end(explode('/', str_replace(array('\\','/BTE_RW_core.php'),array('/',''),__FILE__)));		
   	
   	$table_name = $wpdb->prefix . "bte_rw_sites";
	$sql = "SELECT link,excerpt,img FROM $table_name WHERE post_id=$post->ID ORDER BY rand() LIMIT $numLink";
	$links = $wpdb->get_results($sql);

	$sitestr = "$linksTitle";
	if ($linksLinktitle){
		$sitestr = '<a href="http://www.blogtrafficexchange.com/related-websites">'.$sitestr.'</a>';
	}
	
	$linksicon = get_option('bte_rw_links_icon'); 
	if ($linksicon!=null && $linksicon!="") {
		$sitestr = '<a STYLE="border:none;text-decoration:none;outline:none;" href="http://www.blogtrafficexchange.com"><img border="0" alt="Blog Traffic Exchange" src="'.$home.'/wp-content/plugins' . $base.'/'.get_option('bte_rw_links_icon').'"></a> '.$sitestr;
	}
	
	$sitestr .= " $linksHeader ";
	$i=0;
	foreach ($links as $link) {
		$i++;
		$sitestr .= " $linkHeader ";
		if ($bte_rw_links_img) {
			$img = $bte_rw_encoder->Decode($link->img,$post->guid);
			if ($img != '') {
				$sitestr .= $img;
			} else if ($bte_rw_links_img_default!=null && $bte_rw_links_img_default!='') {
				$sitestr .= '<img  class="imgbte" hspace="5" align="left" width="100" alt="blog traffic exchange" title="blog traffic exchange" border=0  src="'.$bte_rw_links_img_default.'"/>';
			} 	
		}
		$sitestr .= $bte_rw_encoder->Decode($link->link,$post->guid);
		if ($linkExcerpt>0) {
			$sitestr .= " $linkExcerptHeader".bte_rw_excerpt($bte_rw_encoder->Decode($link->excerpt,$post->guid),$linkExcerpt).$linkExcerptFooter;
		}
		$sitestr .= " $linkFooter";
	}
	
	$sitestr .= " $linksFooter";
	if ($i>0) {
		return $sitestr;
	}
	return "";
}

function bte_rw_get_posts($num=0) {
	global $wpdb;
	global $post;
	global $bte_rw_encoder;
	if ($bte_rw_encoder==null)	{
		$bte_rw_encoder = new BTE_RW_GE;
	}
   	if ($num==0)
   	{
	   	$numLink = get_option('bte_rw_links');
		if (!(isset($numLink) && is_numeric($numLink))) {
			$numLink = 5;
		}
   	} else {
   		$numLink=$num;
   	}
   	if ($numLink<1) {
   		$numLink=5;
   	}
	$bte_rw_posts_img = get_option('bte_rw_posts_img');
	$bte_rw_posts_img_default = get_option('bte_rw_posts_img_default');
	$linksLinktitle = get_option('bte_rw_posts_linktitle');
   	$linksTitle = stripslashes(get_option('bte_rw_posts_title'));
   	$linksHeader = stripslashes(get_option('bte_rw_posts_header'));
   	$linksFooter = stripslashes(get_option('bte_rw_posts_footer'));
   	$linkHeader = stripslashes(get_option('bte_rw_post_header'));
   	$linkFooter = stripslashes(get_option('bte_rw_post_footer'));
   	$linkExcerpt = get_option('bte_rw_post_excerpt');
   	$linkExcerptHeader = stripslashes(get_option('bte_rw_post_excerpt_header'));
   	$linkExcerptFooter = stripslashes(get_option('bte_rw_post_excerpt_footer'));
	$home = get_settings('siteurl');
	$base = '/'.end(explode('/', str_replace(array('\\','/BTE_RW_core.php'),array('/',''),__FILE__)));		
   	
   	$table_name = $wpdb->prefix . "bte_rw_posts";
	$sql = "SELECT link,excerpt,img FROM $table_name WHERE post_id=$post->ID ORDER BY rand() LIMIT $numLink";
	$links = $wpdb->get_results($sql);

	$webstr = "$linksTitle";
	if ($linksLinktitle) {
		$webstr = '<a href="http://www.blogtrafficexchange.com/related-posts">'.$webstr.'</a>';
	}
	$linksicon = get_option('bte_rw_posts_icon'); 
	if ($linksicon!=null && $linksicon!="") {
		$webstr = '<a STYLE="border:none;text-decoration:none;outline:none;" href="http://www.blogtrafficexchange.com"><img border="0" alt="Blog Traffic Exchange" src="'.$home.'/wp-content/plugins' . $base.'/'.get_option('bte_rw_posts_icon').'"></a> '.$webstr;
	}
	
	$webstr .= " $linksHeader ";
	$i=0;
	foreach ($links as $link) {
		$i++;
		$webstr .= " $linkHeader ";
		if ($bte_rw_posts_img) {
			$img = $bte_rw_encoder->Decode($link->img,$post->guid);
			if ($img != '') {
				$webstr .= $img;
			} else if ($bte_rw_posts_img_default!=null && $bte_rw_posts_img_default!='') {
				$webstr .= '<img  class="imgbte" hspace="5" align="left" width="100" alt="blog traffic exchange" title="blog traffic exchange" border=0  src="'.$bte_rw_posts_img_default.'"/>';
			} 	
		}
		$webstr .= $bte_rw_encoder->Decode($link->link,$post->guid);
		if ($linkExcerpt>0) {
			$webstr .= " $linkExcerptHeader".bte_rw_excerpt($bte_rw_encoder->Decode($link->excerpt,$post->guid),$linkExcerpt).$linkExcerptFooter;
		}
		$webstr .= " $linkFooter";
	}
	$webstr .= " $linksFooter";
	if ($i>0) {
		return $webstr;
	}
	return "";
}

function bte_rw_the_content($content) {
	global $post;
	$postMod = get_post_modified_time();
	$last = get_post_meta($post->ID, '_bte_rw_last_content_update', true);
	$lastlink = get_post_meta($post->ID, '_bte_rw_last_link_update', true);
	if ($post->post_type == 'post' && $post->post_status == 'publish' && (!(isset($last) && $last!='') || $postMod>$last || !(isset($lastlink) && $lastlink!='') || $lastlink<(time()-13*BTE_RW_24_HOURS))) {
		bte_rw_updateContent($postMod);
		update_post_meta($post->ID,'_bte_rw_last_link_update',time()) or add_post_meta($post->ID, '_bte_rw_last_link_update', time());					
		delete_post_meta($post->ID, '_bte_rw_update_links');
	} else if (get_post_meta($post->ID, '_bte_rw_update_links', true)=="true") {
		bte_rw_updatePostLinks($post->ID,$post->guid);		
		update_post_meta($post->ID,'_bte_rw_last_link_update',time()) or add_post_meta($post->ID, '_bte_rw_last_link_update', time());					
		delete_post_meta($post->ID, '_bte_rw_update_links');
	}
	
	$show = get_option('bte_rw_posts_add');
	if (get_option('bte_rw_posts_so') && $show) {
		$show = is_single();
	}
	if ($post->post_type == 'post' && $post->post_status == 'publish' && $show) {
		$content .= " ".bte_rw_get_posts();
	}		
	$show = get_option('bte_rw_links_add');
	if (get_option('bte_rw_links_so') && $show) {
		$show = is_single();
	}
	if ($post->post_type == 'post' && $post->post_status == 'publish' && $show) {
		$content .= " ".bte_rw_get_links();
	}		
	return $content;
}

function bte_rw_the_excerpt($content) {
	global $post;
	$postMod = get_post_modified_time();
	$last = get_post_meta($post->ID, '_bte_rw_last_content_update', true);
	$lastlink = get_post_meta($post->ID, '_bte_rw_last_link_update', true);
	if ($post->post_type == 'post' && $post->post_status == 'publish' && (!(isset($last) && $last!='') || $postMod>$last || !(isset($lastlink) && $lastlink!='') || $lastlink<(time()-13*BTE_RW_24_HOURS))) {
		bte_rw_updateContent($postMod);
		update_post_meta($post->ID,'_bte_rw_last_link_update',time()) or add_post_meta($post->ID, '_bte_rw_last_link_update', time());					
		delete_post_meta($post->ID, '_bte_rw_update_links');
	} else if (get_post_meta($post->ID, '_bte_rw_update_links', true)=="true") {
		bte_rw_updatePostLinks($post->ID,$post->guid);		
		update_post_meta($post->ID,'_bte_rw_last_link_update',time()) or add_post_meta($post->ID, '_bte_rw_last_link_update', time());					
		delete_post_meta($post->ID, '_bte_rw_update_links');
	}
	return $content;
}

function bte_rw_wake() {
	if (rand()%99==0) {//1% of the time reset old links for refresh
		global $wpdb;
		$table_name = $wpdb->prefix . "bte_rw_sites";
		$threshold = time()-BTE_RW_24_HOURS;
		$sql = "INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value) SELECT p.ID,'_bte_rw_update_links','true' FROM $wpdb->posts p INNER JOIN $wpdb->postmeta pm ON p.ID=pm.post_id and pm.meta_key='_bte_rw_last_link_update' and pm.meta_value<$threshold;";
		$wpdb->query($sql);
		if (function_exists('wp_cache_flush')) {
			wp_cache_flush();
		}				
	}
}

?>
