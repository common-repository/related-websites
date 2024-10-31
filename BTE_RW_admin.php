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

function bte_rw_head_admin() {
		wp_enqueue_script('jquery-ui-tabs');
		$home = get_settings('siteurl');
		$base = '/'.end(explode('/', str_replace(array('\\','/BTE_RW_admin.php'),array('/',''),__FILE__)));
		$stylesheet = $home.'/wp-content/plugins' . $base . '/css/related_websites.css';
		echo('<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />');
}

function bte_rw_options_setup() {	
	add_options_page('RelatedWebsites', 'Related Websites', 10, basename(__FILE__), 'bte_rw_options');
}

function bte_rw_stats_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('index.php', __('Blog Traffic Exchange Stats'), __('Blog Traffic Exchange Stats'), 'manage_options', 'bte-rw-stats-display', 'bte_rw_stats_display');
}

function bte_rw_stats_display() {
	$bte_rw_key = get_option('bte_rw_key');
	if (!isset($bte_rw_key)) {
		$bte_rw_key = BTE_RW_KEY;
	}
	if ($bte_rw_key!=null && $bte_rw_key!='') {
		$url = 'http://www.blogtrafficexchange.com/stats/?key='.urlencode($bte_rw_key).'&site='.urlencode(get_option('siteurl'));
		?>
		<div class="wrap">
		<iframe src="<?php echo $url; ?>" width="100%" height="600" frameborder="0" id="bte-rw-stats-frame"></iframe>
		</div>
		<?php 
	}
}

function bte_rw_admin_notices() {
	$bte_rw_admin_notice = get_option('bte_rw_admin_notice');
	if (!isset($bte_rw_admin_notice)) {
		$bte_rw_admin_notice = BTE_RW_ADMIN_NOTICE;
	}
	$bte_rw_key = get_option('bte_rw_key');
	if (!isset($bte_rw_key)) {
		$bte_rw_key = BTE_RW_KEY;
	}
	if ($bte_rw_admin_notice && $bte_rw_key!=null && $bte_rw_key!='') {
		echo '<div id="update-nag"><a href="http://www.blogtrafficexchange.com/stats/?key='.urlencode($bte_rw_key).'&site='.urlencode(get_option('siteurl')).'">Blog Traffic Exchange Stats</a></div>';
	}
}

function bte_rw_options() {		
	$message = null;
	$message_updated = __("Related Websites Options Updated.", 'bte_rw_online_stores');
	if (!empty($_POST['bte_rw_action'])) {
		$message = $message_updated;
		if (isset($_POST['bte_rw_links'])) {
			update_option('bte_rw_links',$_POST['bte_rw_links']);
		}
		if (isset($_POST['bte_rw_admin_notice'])) {
			update_option('bte_rw_admin_notice',$_POST['bte_rw_admin_notice']);
		}
		if (isset($_POST['bte_rw_posts_icon'])) {
			update_option('bte_rw_posts_icon',$_POST['bte_rw_posts_icon']);
		}
		if (isset($_POST['bte_rw_links_icon'])) {
			update_option('bte_rw_links_icon',$_POST['bte_rw_links_icon']);
		}
		if (isset($_POST['bte_rw_posts_img'])) {
			update_option('bte_rw_posts_img',$_POST['bte_rw_posts_img']);
		}
		if (isset($_POST['bte_rw_links_img'])) {
			update_option('bte_rw_links_img',$_POST['bte_rw_links_img']);
		}
		if (isset($_POST['bte_rw_links_img_default'])) {
			update_option('bte_rw_links_img_default',$_POST['bte_rw_links_img_default']);
		}
		if (isset($_POST['bte_rw_posts_img_default'])) {
			update_option('bte_rw_posts_img_default',$_POST['bte_rw_posts_img_default']);
		}
		if (isset($_POST['bte_rw_links_title'])) {
			update_option('bte_rw_links_title',$_POST['bte_rw_links_title']);
		}
		if (isset($_POST['bte_rw_links_linktitle'])) {
			update_option('bte_rw_links_linktitle',$_POST['bte_rw_links_linktitle']);
		}
		if (isset($_POST['bte_rw_links_header'])) {
			update_option('bte_rw_links_header',$_POST['bte_rw_links_header']);
		}
		if (isset($_POST['bte_rw_links_footer'])) {
			update_option('bte_rw_links_footer',$_POST['bte_rw_links_footer']);
		}
		if (isset($_POST['bte_rw_link_header'])) {
			update_option('bte_rw_link_header',$_POST['bte_rw_link_header']);
		}
		if (isset($_POST['bte_rw_link_footer'])) {
			update_option('bte_rw_link_footer',$_POST['bte_rw_link_footer']);
		}
		if (isset($_POST['bte_rw_link_excerpt'])) {
			update_option('bte_rw_link_excerpt',$_POST['bte_rw_link_excerpt']);
		}
		if (isset($_POST['bte_rw_link_excerpt_header'])) {
			update_option('bte_rw_link_excerpt_header',$_POST['bte_rw_link_excerpt_header']);
		}
		if (isset($_POST['bte_rw_link_excerpt_footer'])) {
			update_option('bte_rw_link_excerpt_footer',$_POST['bte_rw_link_excerpt_footer']);
		}
		if (isset($_POST['bte_rw_links_add'])) {
			update_option('bte_rw_links_add',$_POST['bte_rw_links_add']);
		}
		if (isset($_POST['bte_rw_links_so'])) {
			update_option('bte_rw_links_so',$_POST['bte_rw_links_so']);
		}
		if (isset($_POST['bte_rw_posts_so'])) {
			update_option('bte_rw_posts_so',$_POST['bte_rw_posts_so']);
		}
		if (isset($_POST['bte_rw_posts_title'])) {
			update_option('bte_rw_posts_title',$_POST['bte_rw_posts_title']);
		}
		if (isset($_POST['bte_rw_posts_linktitle'])) {
			update_option('bte_rw_posts_linktitle',$_POST['bte_rw_posts_linktitle']);
		}
		if (isset($_POST['bte_rw_posts_header'])) {
			update_option('bte_rw_posts_header',$_POST['bte_rw_posts_header']);
		}
		if (isset($_POST['bte_rw_posts_footer'])) {
			update_option('bte_rw_posts_footer',$_POST['bte_rw_posts_footer']);
		}
		if (isset($_POST['bte_rw_post_header'])) {
			update_option('bte_rw_post_header',$_POST['bte_rw_post_header']);
		}
		if (isset($_POST['bte_rw_post_footer'])) {
			update_option('bte_rw_post_footer',$_POST['bte_rw_post_footer']);
		}
		if (isset($_POST['bte_rw_post_excerpt'])) {
			update_option('bte_rw_post_excerpt',$_POST['bte_rw_post_excerpt']);
		}
		if (isset($_POST['bte_rw_post_excerpt_header'])) {
			update_option('bte_rw_post_excerpt_header',$_POST['bte_rw_post_excerpt_header']);
		}
		if (isset($_POST['bte_rw_post_excerpt_footer'])) {
			update_option('bte_rw_post_excerpt_footer',$_POST['bte_rw_post_excerpt_footer']);
		}
		
		if (isset($_POST['bte_rw_posts_add'])) {
			update_option('bte_rw_posts_add',$_POST['bte_rw_posts_add']);
		}
		if (isset($_POST['bte_rw_lang'])) {
			update_option('bte_rw_lang',$_POST['bte_rw_lang']);
		}
		if (isset($_POST['bte_rw_key'])) {
			$bte_rw_key = get_option('bte_rw_key');
			if (!isset($bte_rw_key)) {
				$bte_rw_key = BTE_RW_KEY;
			}
			if ($bte_rw_key != $_POST['bte_rw_key']) {
				global $wpdb;
				update_option('bte_rw_key',$_POST['bte_rw_key']);				
			   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_rw_last_content_update';";
				$res = $wpdb->query($sql);
			   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_rw_last_link_update';";
				$res = $wpdb->query($sql);
			}
		}
		
		print('
			<div id="message" class="updated fade">
				<p>'.__('Related Websites Options Updated.', 'RelatedWebsites').'</p>
			</div>');
	}
	$bte_rw_links = get_option('bte_rw_links');
	if (!isset($bte_rw_links)) {
		$bte_rw_links = BTE_RW_LINKS;
	}
	$bte_rw_admin_notice = get_option('bte_rw_admin_notice');
	if (!isset($bte_rw_admin_notice)) {
		$bte_rw_admin_notice = BTE_RW_ADMIN_NOTICE;
	}
	$bte_rw_links_linktitle = get_option('bte_rw_links_linktitle');
	if (!isset($bte_rw_links_linktitle)) {
		$bte_rw_links_linktitle = BTE_RW_LINKS_LINKTITLE;
	}
	$bte_rw_links_icon = get_option('bte_rw_links_icon');
	if (!isset($bte_rw_links_icon)) {
		$bte_rw_links_icon = BTE_RW_LINKS_ICON;
	}
	$bte_rw_posts_icon = get_option('bte_rw_posts_icon');
	if (!isset($bte_rw_posts_icon)) {
		$bte_rw_posts_icon = BTE_RW_POSTS_ICON;
	}
	$bte_rw_links_img = get_option('bte_rw_links_img');
	if (!isset($bte_rw_links_img)) {
		$bte_rw_links_img = BTE_RW_LINKS_IMG;
	}
	$bte_rw_posts_img = get_option('bte_rw_posts_img');
	if (!isset($bte_rw_posts_img)) {
		$bte_rw_posts_img = BTE_RW_POSTS_IMG;
	}
	$bte_rw_links_img_default = get_option('bte_rw_links_img_default');
	$bte_rw_posts_img_default = get_option('bte_rw_posts_img_default');
	
	$bte_rw_links_title = get_option('bte_rw_links_title');
	if (!isset($bte_rw_links_title)) {
		$bte_rw_links_title = BTE_RW_LINKS_TITLE;
	}
	$bte_rw_links_header = get_option('bte_rw_links_header');
	if (!isset($bte_rw_links_header)) {
		$bte_rw_links_header = BTE_RW_LINKS_HEADER;
	}
	$bte_rw_links_footer = get_option('bte_rw_links_footer');
	if (!isset($bte_rw_links_footer)) {
		$bte_rw_links_footer = BTE_RW_LINKS_FOOTER;
	}
	$bte_rw_link_header = get_option('bte_rw_link_header');
	if (!isset($bte_rw_link_header)) {
		$bte_rw_link_header = BTE_RW_LINK_HEADER;
	}
	$bte_rw_link_footer = get_option('bte_rw_link_footer');
	if (!isset($bte_rw_link_footer)) {
		$bte_rw_link_footer = BTE_RW_LINK_FOOTER;
	}
	$bte_rw_link_excerpt = get_option('bte_rw_link_excerpt');
	if (!isset($bte_rw_link_excerpt)) {
		$bte_rw_link_excerpt = BTE_RW_LINK_EXCERPT;
	}
	$bte_rw_link_excerpt_header = get_option('bte_rw_link_excerpt_header');
	if (!isset($bte_rw_link_excerpt_header)) {
		$bte_rw_link_excerpt_header = BTE_RW_LINK_EXCERPT_HEADER;
	}
	$bte_rw_link_excerpt_footer = get_option('bte_rw_link_excerpt_footer');
	if (!isset($bte_rw_link_excerpt_footer)) {
		$bte_rw_link_excerpt_footer = BTE_RW_LINK_EXCERPT_FOOTER;
	}
	$bte_rw_links_add = get_option('bte_rw_links_add');
	if (!isset($bte_rw_links_add)) {
		$bte_rw_links_add = BTE_RW_ADD;
	}
	$bte_rw_links_so = get_option('bte_rw_links_so');
	if (!isset($bte_rw_links_so)) {
		$bte_rw_links_so = false;
	}
	$bte_rw_posts_so = get_option('bte_rw_posts_so');
	if (!isset($bte_rw_posts_so)) {
		$bte_rw_posts_so = false;
	}
	$bte_rw_posts_linktitle = get_option('bte_rw_posts_linktitle');
	if (!isset($bte_rw_posts_linktitle)) {
		$bte_rw_posts_linktitle = BTE_RW_POSTS_LINKTITLE;
	}
	$bte_rw_posts_title = get_option('bte_rw_posts_title');
	if (!isset($bte_rw_posts_title)) {
		$bte_rw_posts_title = BTE_RW_POSTS_HEADER;
	}
	$bte_rw_posts_header = get_option('bte_rw_posts_header');
	if (!isset($bte_rw_posts_header)) {
		$bte_rw_posts_header = BTE_RW_POSTS_HEADER;
	}
	$bte_rw_posts_footer = get_option('bte_rw_posts_footer');
	if (!isset($bte_rw_posts_footer)) {
		$bte_rw_posts_footer = BTE_RW_POSTS_FOOTER;
	}
	$bte_rw_post_header = get_option('bte_rw_post_header');
	if (!isset($bte_rw_post_header)) {
		$bte_rw_post_header = BTE_RW_POST_HEADER;
	}
	$bte_rw_post_footer = get_option('bte_rw_post_footer');
	if (!isset($bte_rw_post_footer)) {
		$bte_rw_post_footer = BTE_RW_POST_FOOTER;
	}
	$bte_rw_post_excerpt = get_option('bte_rw_post_excerpt');
	if (!isset($bte_rw_post_excerpt)) {
		$bte_rw_post_excerpt = BTE_RW_POST_EXCERPT;
	}
	$bte_rw_post_excerpt_header = get_option('bte_rw_post_excerpt_header');
	if (!isset($bte_rw_post_excerpt_header)) {
		$bte_rw_post_excerpt_header = BTE_RW_POST_EXCERPT_HEADER;
	}
	$bte_rw_post_excerpt_footer = get_option('bte_rw_post_excerpt_footer');
	if (!isset($bte_rw_post_excerpt_footer)) {
		$bte_rw_post_excerpt_footer = BTE_RW_POST_EXCERPT_FOOTER;
	}
	$bte_rw_posts_add = get_option('bte_rw_posts_add');
	if (!isset($bte_rw_posts_add)) {
		$bte_rw_posts_add = BTE_RW_ADD;
	}
	$bte_rw_key = get_option('bte_rw_key');
	if (!isset($bte_rw_key)) {
		$bte_rw_key = BTE_RW_KEY;
	}
	$bte_rw_lang = get_option('bte_rw_lang');
	if (!isset($bte_rw_lang)) {
		if (WPLANG=='') {
			$bte_rw_lang = "en";		
		} else {
			$bte_rw_lang = WPLANG;		
		}
	}
	
	print('
			<div class="wrap">
				<h2>'.__('Related Websites by', 'RelatedWebsites').' <a href="http://www.blogtrafficexchange.com">Blog Traffic Exchange</a></h2>
				<form id="bte_rw" name="bte_rw_onlinestores" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=BTE_RW_admin.php" method="post">
					<input type="hidden" name="bte_rw_action" value="bte_rw_update_settings" />
					<fieldset class="options">
						<div class="option">
							<p><a href="http://www.blogtrafficexchange.com/signup">Signup at Blog Traffic Exchange</a> to get your site key.  With a valid active site key your content pages will be served by the Blog Traffic Exchange.</p><br/>
							<label for="bte_rw_key">'.__('Blog Traffic Exchange key: ', 'RelatedWebsites').'</label>
							<input size="32" name="bte_rw_key" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_key)).'" /><br/>
						</div>');
	if ($bte_rw_key!=null && $bte_rw_key!='') {
	print('
						<div class="option">
							<label for="bte_rw_admin_notice">'.__('Show BTE Stats Link in Admin Header? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_admin_notice" id="bte_rw_admin_notice">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_admin_notice).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_admin_notice).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<iframe width="100%" height=200 src="http://www.blogtrafficexchange.com/stats/?admin=true&key='.urlencode($bte_rw_key).'&site='.urlencode(get_option('siteurl')).'"></iframe>
						</div>');
	}
	print('
						<div class="option">
							<label for="bte_rw_links">'.__('Number of Links: ', 'RelatedWebsites').'</label>
							<select name="bte_rw_links" id="bte_rw_links">
									<option value="2" '.bte_rw_optionselected(2,$bte_rw_links).'>'.__('2', 'RelatedWebsites').'</option>
									<option value="3" '.bte_rw_optionselected(3,$bte_rw_links).'>'.__('3', 'RelatedWebsites').'</option>
									<option value="4" '.bte_rw_optionselected(4,$bte_rw_links).'>'.__('4', 'RelatedWebsites').'</option>
									<option value="5" '.bte_rw_optionselected(5,$bte_rw_links).'>'.__('5', 'RelatedWebsites').'</option>
									<option value="6" '.bte_rw_optionselected(6,$bte_rw_links).'>'.__('6', 'RelatedWebsites').'</option>
									<option value="7" '.bte_rw_optionselected(7,$bte_rw_links).'>'.__('7', 'RelatedWebsites').'</option>
									<option value="8" '.bte_rw_optionselected(8,$bte_rw_links).'>'.__('8', 'RelatedWebsites').'</option>
									<option value="9" '.bte_rw_optionselected(9,$bte_rw_links).'>'.__('9', 'RelatedWebsites').'</option>
									<option value="10" '.bte_rw_optionselected(10,$bte_rw_links).'>'.__('10', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_lang">'.__('Website Lang: ', 'RelatedWebsites').'</label>
							<select name="bte_rw_lang" id="bte_rw_add">
									<option value="en" '.bte_rw_optionselected("en",$bte_rw_lang).'>'.__('en', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_links_title">'.__('Related Websites Title: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_links_title" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_links_title)).'" /><br/>
							<label for="bte_rw_links_header">'.__('Link Block Header: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_links_header" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_links_header)).'" /><br/>
							<label for="bte_rw_link_header">'.__('Individual Link Header: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_link_header" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_link_header)).'" /><br/>
							<label for="bte_rw_link_footer">'.__('Individual Link Footer: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_link_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_link_footer)).'" /><br/>
							<label for="bte_rw_links_footer">'.__('Link Block Footer: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_links_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_links_footer)).'" />
						</div>
						<div class="option">
							<label for="bte_rw_link_excerpt">'.__('Link Excerpt Length? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_link_excerpt" id="bte_rw_link_excerpt">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_link_excerpt).'>'.__('No Excerpt', 'RelatedWebsites').'</option>
									<option value="5" '.bte_rw_optionselected(5,$bte_rw_link_excerpt).'>'.__('Up to 5 Words', 'RelatedWebsites').'</option>
									<option value="10" '.bte_rw_optionselected(10,$bte_rw_link_excerpt).'>'.__('Up to 10 Words', 'RelatedWebsites').'</option>
									<option value="15" '.bte_rw_optionselected(15,$bte_rw_link_excerpt).'>'.__('Up to 15 Words', 'RelatedWebsites').'</option>
									<option value="20" '.bte_rw_optionselected(20,$bte_rw_link_excerpt).'>'.__('Up to 20 Words', 'RelatedWebsites').'</option>
									<option value="25" '.bte_rw_optionselected(25,$bte_rw_link_excerpt).'>'.__('Up to 25 Words', 'RelatedWebsites').'</option>
									<option value="30" '.bte_rw_optionselected(30,$bte_rw_link_excerpt).'>'.__('Up to 30 Words', 'RelatedWebsites').'</option>
									<option value="35" '.bte_rw_optionselected(35,$bte_rw_link_excerpt).'>'.__('Up to 35 Words', 'RelatedWebsites').'</option>
									<option value="40" '.bte_rw_optionselected(40,$bte_rw_link_excerpt).'>'.__('Up to 40 Words', 'RelatedWebsites').'</option>
									<option value="45" '.bte_rw_optionselected(45,$bte_rw_link_excerpt).'>'.__('Up to 45 Words', 'RelatedWebsites').'</option>
									<option value="50" '.bte_rw_optionselected(50,$bte_rw_link_excerpt).'>'.__('Up to 50 Words', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">				
							<label for="bte_rw_link_excerpt_header">'.__('Link Excerpt Header: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_link_excerpt_header" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_link_excerpt_header)).'" /><br/>
							<label for="bte_rw_link_excerpt_footer">'.__('Link Excerpt Footer: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_link_excerpt_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_link_excerpt_footer)).'" /><br/>
						</div>
						<div class="option">
							<label for="bte_rw_links_img">'.__('Show Website Post Thumbnail? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_links_img" id="bte_rw_links_img">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_links_img).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_links_img).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_links_img_default">'.__('URL to Image if No Post Thumbnail? ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_links_img_default" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_links_img_default)).'" /><br/>
						</div>
						<div class="option">
							<label for="bte_rw_links_icon">'.__('Which Icon? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_links_icon" id="bte_rw_links_icon">
									<option value="24x24.png" '.bte_rw_optionselected('24x24.png',$bte_rw_links_icon).'>'.__('Black background with white X', 'RelatedWebsites').'</option>
									<option value="24x24-white.png" '.bte_rw_optionselected('24x24-white.png',$bte_rw_links_icon).'>'.__('White background with black X', 'RelatedWebsites').'</option>
									<option value="" '.bte_rw_optionselected('',$bte_rw_links_icon).'>'.__('No Icon', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_links_linktitle">'.__('Link Title to Related Websites plugin page? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_links_linktitle" id="bte_rw_links_linktitle">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_links_linktitle).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_links_linktitle).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_links_add">'.__('Automatically add Related Websites to the content: ', 'RelatedWebsites').'</label>
							<select name="bte_rw_links_add" id="bte_rw_links_add">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_links_add).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_links_add).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_links_so">'.__('Automatically add to single post page only: ', 'RelatedWebsites').'</label>
							<select name="bte_rw_links_so" id="bte_rw_links_so">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_links_so).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_links_so).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<p>To manually insert the pre-formatted Related Website links within your template tags, add this code with "The Loop."  It will enable you to use the links from within an excerpt section.</p>
							<p><strong><code>&lt;?php if (function_exists(\'bte_rw_links\')) { bte_rw_links(); } ?&gt;</code></strong></p>
						</div>
						<div class="option">
							<p>As an added bonus for claiming your site on the <a href="http://www.blogtrafficexchange.com/">Blog Traffic Exchange</a>, you can utilize our cutting edge matching and randomizing technology to display related posts from within your own site. Related posts are always listed prior to related websites.</p>
						</div>
						<div class="option">
							<label for="bte_rw_posts_title">'.__('Related Posts Title: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_posts_title" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_posts_title)).'" /><br/>
							<label for="bte_rw_posts_header">'.__('Post Block Header: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_posts_header" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_posts_header)).'" /><br/>
							<label for="bte_rw_post_header">'.__('Individual Post Header: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_post_header" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_post_header)).'" /><br/>
							<label for="bte_rw_post_footer">'.__('Individual Post Footer: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_post_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_post_footer)).'" /><br/>
							<label for="bte_rw_posts_footer">'.__('Post Block Footer: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_posts_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_posts_footer)).'" />
						</div>
						<div class="option">
							<label for="bte_rw_post_excerpt">'.__('Post Excerpt Length? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_post_excerpt" id="bte_rw_post_excerpt">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_post_excerpt).'>'.__('No Excerpt', 'RelatedWebsites').'</option>
									<option value="5" '.bte_rw_optionselected(5,$bte_rw_post_excerpt).'>'.__('Up to 5 Words', 'RelatedWebsites').'</option>
									<option value="10" '.bte_rw_optionselected(10,$bte_rw_post_excerpt).'>'.__('Up to 10 Words', 'RelatedWebsites').'</option>
									<option value="15" '.bte_rw_optionselected(15,$bte_rw_post_excerpt).'>'.__('Up to 15 Words', 'RelatedWebsites').'</option>
									<option value="20" '.bte_rw_optionselected(20,$bte_rw_post_excerpt).'>'.__('Up to 20 Words', 'RelatedWebsites').'</option>
									<option value="25" '.bte_rw_optionselected(25,$bte_rw_post_excerpt).'>'.__('Up to 25 Words', 'RelatedWebsites').'</option>
									<option value="30" '.bte_rw_optionselected(30,$bte_rw_post_excerpt).'>'.__('Up to 30 Words', 'RelatedWebsites').'</option>
									<option value="35" '.bte_rw_optionselected(35,$bte_rw_post_excerpt).'>'.__('Up to 35 Words', 'RelatedWebsites').'</option>
									<option value="40" '.bte_rw_optionselected(40,$bte_rw_post_excerpt).'>'.__('Up to 40 Words', 'RelatedWebsites').'</option>
									<option value="45" '.bte_rw_optionselected(45,$bte_rw_post_excerpt).'>'.__('Up to 45 Words', 'RelatedWebsites').'</option>
									<option value="50" '.bte_rw_optionselected(50,$bte_rw_post_excerpt).'>'.__('Up to 50 Words', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">				
							<label for="bte_rw_post_excerpt_header">'.__('Post Excerpt Header: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_post_excerpt_header" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_post_excerpt_header)).'" /><br/>
							<label for="bte_rw_post_excerpt_footer">'.__('Post Excerpt Footer: ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_post_excerpt_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_post_excerpt_footer)).'" /><br/>
						</div>
						<div class="option">
							<label for="bte_rw_posts_img">'.__('Show Post Thumbnail? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_posts_img" id="bte_rw_posts_img">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_posts_img).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_posts_img).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_posts_img_default">'.__('URL to Image if No Post Thumbnail? ', 'RelatedWebsites').'</label>
							<input size="80" name="bte_rw_posts_img_default" type="text" value="'.htmlspecialchars(stripslashes($bte_rw_posts_img_default)).'" /><br/>
						</div>
						<div class="option">
							<label for="bte_rw_posts_icon">'.__('Which Icon? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_posts_icon" id="bte_rw_posts_icon">
									<option value="24x24.png" '.bte_rw_optionselected('24x24.png',$bte_rw_posts_icon).'>'.__('Black background with white X', 'RelatedWebsites').'</option>
									<option value="24x24-white.png" '.bte_rw_optionselected('24x24-white.png',$bte_rw_posts_icon).'>'.__('White background with black X', 'RelatedWebsites').'</option>
									<option value="" '.bte_rw_optionselected('',$bte_rw_posts_icon).'>'.__('No Icon', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_posts_linktitle">'.__('Link Title to Related Posts plugin page? ', 'RelatedWebsites').'</label>
							<select name="bte_rw_posts_linktitle" id="bte_rw_posts_linktitle">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_posts_linktitle).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_posts_linktitle).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>								
						<div class="option">
							<label for="bte_rw_posts_add">'.__('Automatically add Related Posts to the content: ', 'RelatedWebsites').'</label>
							<select name="bte_rw_posts_add" id="bte_rw_posts_add">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_posts_add).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_posts_add).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<label for="bte_rw_posts_so">'.__('Automatically add to single post page only: ', 'RelatedWebsites').'</label>
							<select name="bte_rw_posts_so" id="bte_rw_posts_so">
									<option value="0" '.bte_rw_optionselected(0,$bte_rw_posts_so).'>'.__('No', 'RelatedWebsites').'</option>
									<option value="1" '.bte_rw_optionselected(1,$bte_rw_posts_so).'>'.__('Yes', 'RelatedWebsites').'</option>
							</select>
						</div>
						<div class="option">
							<p>To manually place the formatted Related Post links within your template tags add this code with "The Loop".  This is how to use the links within an excerpt section.</p>
							<p><strong><code>&lt;?php if (function_exists(\'bte_rw_posts\')) { bte_rw_posts(); } ?&gt;</code></strong></p>
						</div>
					</fieldset>
					<p class="submit">
						<input type="submit" name="submit" value="'.__('Update Related Websites Options', 'RelatedWebsites').'" />
					</p>
						<div class="option">
							<h4>Other Blog Traffic Exchange <a href="http://www.blogtrafficexchange.com/wordpress-plugins/">Wordpress Plugins</a></h4>
							<ul>
							<li><a href="http://www.blogtrafficexchange.com/related-websites/">Related Websites</a></li>
							<li><a href="http://www.blogtrafficexchange.com/related-tweets/">Related Tweets</a></li>
							<li><a href="http://www.blogtrafficexchange.com/wordpress-backup/">Wordpress Backup</a></li>
							<li><a href="http://www.blogtrafficexchange.com/blog-copyright/">Blog Copyright</a></li>
							<li><a href="http://www.blogtrafficexchange.com/old-post-promoter/">Old Post Promoter</a></li>
							<li><a href="http://www.blogtrafficexchange.com/related-posts/">Related Posts</a></li>
														</ul>
						</div>
				</form>' );

}

function bte_rw_js_header() {
} 

function bte_rw_optionselected($opValue, $value) {
	if($opValue==$value) {
		return 'selected="selected"';
	}
	return '';
}
?>