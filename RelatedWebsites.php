<?php
/*
Plugin Name: Related Websites (by BTE)
Plugin URI: http://www.blogtrafficexchange.com/related-websites
Description: Add your posts to the Blog Traffic Exchange and add Links to related websites throughout the blogosphere. <a href="options-general.php?page=BTE_RW_admin.php">Configuration options are here.</a>
Version: 2.8.5
Author: Blog Traffic Exchange
Author URI: http://www.blogtrafficexchange.com/
License: GPL
*/
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

require_once('BTE_RW_core.php');
require_once('BTE_RW_admin.php');
require_once('BTE_RW_ge.php');
if (!class_exists('xmlrpcmsg')) {
	require_once('lib/xmlrpc.inc');
}		

// Play nice to PHP 5 installations with REGISTER_LONG_ARRAYS off
if(!isset($HTTP_POST_VARS) && isset($_POST))
{
	$HTTP_POST_VARS = $_POST;
}

define ('BTE_RW_KEY', ''); 
define ('BTE_RW_KEYWORDS', 'freq'); 
define ('BTE_RW_DEBUG', false); 
define ('BTE_RW_DB_SCHEMA', '1.0'); 

/*
define ('BTE_RW_XMLRPC_URI', 'localweb'); 
define ('BTE_RW_XMLRPC', 'BTE/bte2.php'); 
*/

define ('BTE_RW_XMLRPC_URI', 'bteservice.com'); 
define ('BTE_RW_XMLRPC', 'bte2.php'); 


define ('BTE_RW_1_HOUR', 60*60); 
define ('BTE_RW_4_HOURS', 4*BTE_RW_1_HOUR); 
define ('BTE_RW_6_HOURS', 6*BTE_RW_1_HOUR); 
define ('BTE_RW_12_HOURS', 12*BTE_RW_1_HOUR); 
define ('BTE_RW_24_HOURS', 24*BTE_RW_1_HOUR); 
define ('BTE_RW_48_HOURS', 48*BTE_RW_1_HOUR); 
define ('BTE_RW_72_HOURS', 72*BTE_RW_1_HOUR); 
define ('BTE_RW_168_HOURS', 168*BTE_RW_1_HOUR); 
define ('BTE_RW_LINK_INTERVAL', BTE_RW_24_HOURS); 
define ('BTE_RW_ADMIN_NOTICE', true); 
define ('BTE_RW_LINKS', 5); 
define ('BTE_RW_LINKS_ICON', '24x24.png'); 
define ('BTE_RW_POSTS_ICON', '24x24.png'); 
define ('BTE_RW_LINKS_IMG', true); 
define ('BTE_RW_POSTS_IMG', true); 
define ('BTE_RW_LINKS_LINKTITLE', true); 
define ('BTE_RW_LINKS_TITLE', '<strong>Related Websites</strong>'); 
define ('BTE_RW_LINKS_HEADER', '<ul>'); 
define ('BTE_RW_LINKS_FOOTER', '</ul>'); 
define ('BTE_RW_LINK_HEADER', '<li style="clear: both;">'); 
define ('BTE_RW_LINK_FOOTER', '</li>'); 
define ('BTE_RW_LINK_EXCERPT',50);
define ('BTE_RW_LINK_EXCERPT_HEADER', '<small>'); 
define ('BTE_RW_LINK_EXCERPT_FOOTER', '</small>'); 
define ('BTE_RW_POSTS_LINKTITLE', true); 
define ('BTE_RW_POSTS_TITLE', '<strong>Related Posts</strong>'); 
define ('BTE_RW_POSTS_HEADER', '<ul>'); 
define ('BTE_RW_POSTS_FOOTER', '</ul>'); 
define ('BTE_RW_POST_HEADER', '<li style="clear: both;">'); 
define ('BTE_RW_POST_FOOTER', '</li>'); 
define ('BTE_RW_POST_EXCERPT',50);
define ('BTE_RW_POST_EXCERPT_HEADER', '<small>'); 
define ('BTE_RW_POST_EXCERPT_FOOTER', '</small>'); 
define ('BTE_RW_ADD', '1'); 

register_activation_hook(__FILE__, 'bte_rw_activate');
register_deactivation_hook(__FILE__, 'bte_rw_deactivate');
add_filter('the_content', 'bte_rw_the_content', 1099);
add_filter('the_excerpt', 'bte_rw_the_excerpt', 1099);
add_action('init','bte_rw_wake');
add_action('admin_menu', 'bte_rw_options_setup');
add_action('admin_menu', 'bte_rw_stats_page');
add_action('admin_head', 'bte_rw_head_admin');
add_action('wp_head', 'bte_rw_js_header' );
add_action('admin_notices', 'bte_rw_admin_notices' );
add_filter('plugin_action_links', 'bte_rw_plugin_action_links', 10, 2);

function bte_rw_plugin_action_links($links, $file) {
	$plugin_file = basename(__FILE__);
	if (basename($file) == $plugin_file) {
		$settings_link = '<a href="options-general.php?page=BTE_RW_admin.php">'.__('Settings', 'RelatedWebsites').'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}

function bte_rw_links($num=0) {
	echo bte_rw_get_links($num);
}

function bte_rw_posts($num=0) {
	echo bte_rw_get_posts($num);
}

function bte_rw_deactivate() {
	global $wpdb;

	delete_option('bte_rw_admin_notice');
	
	$web_table_name = $wpdb->prefix . "bte_rw_sites";
	$site_table_name = $wpdb->prefix . "bte_rw_posts";
   	$webclicks_table_name = $wpdb->prefix . "bte_rw_webclicks";
   	$siteclicks_table_name = $wpdb->prefix . "bte_rw_siteclicks";

	$sql = "DROP TABLE $web_table_name;";
	$res = $wpdb->query($sql);	
	$sql = "DROP TABLE $site_table_name;";
	$res = $wpdb->query($sql);	
	$sql = "DROP TABLE $webclicks_table_name;";
	$res = $wpdb->query($sql);	
	$sql = "DROP TABLE $siteclicks_table_name;";
	$res = $wpdb->query($sql);	
   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_rw_last_content_update';";
	$res = $wpdb->query($sql);
   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_rw_last_link_update';";
	$res = $wpdb->query($sql);
   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_content';";
	$res = $wpdb->query($sql);
   	$sql = "DELETE FROM $wpdb->postmeta WHERE meta_key='_bte_last_content_update';";
	$res = $wpdb->query($sql);
}

function bte_rw_activate() {
	bte_rw_deactivate();
	global $wpdb;
	add_option('bte_rw_admin_notice', BTE_RW_ADMIN_NOTICE);
	add_option('bte_rw_links_linktitle',BTE_RW_LINKS_LINKTITLE);	
	add_option('bte_rw_posts_linktitle',BTE_RW_POSTS_LINKTITLE);	
	add_option('bte_rw_link_interval',BTE_RW_LINK_INTERVAL);
	add_option('bte_rw_key',BTE_RW_KEY);
   	add_option('bte_rw_links',BTE_RW_LINKS);
   	add_option('bte_rw_links_icon',BTE_RW_LINKS_ICON);
   	add_option('bte_rw_posts_icon',BTE_RW_POSTS_ICON);
   	add_option('bte_rw_links_img',BTE_RW_LINKS_IMG);
   	add_option('bte_rw_posts_img',BTE_RW_POSTS_IMG);
   	add_option('bte_rw_links_title',BTE_RW_LINKS_TITLE);
   	add_option('bte_rw_links_header',BTE_RW_LINKS_HEADER);
   	add_option('bte_rw_links_footer',BTE_RW_LINKS_FOOTER);
   	add_option('bte_rw_link_header',BTE_RW_LINK_HEADER);
   	add_option('bte_rw_link_footer',BTE_RW_LINK_FOOTER);
   	add_option('bte_rw_link_excerpt',BTE_RW_LINK_EXCERPT);
   	add_option('bte_rw_link_excerpt_header',BTE_RW_LINK_EXCERPT_HEADER);
   	add_option('bte_rw_link_excerpt_footer',BTE_RW_LINK_EXCERPT_FOOTER);
   	add_option('bte_rw_posts_title',BTE_RW_POSTS_TITLE);
   	add_option('bte_rw_posts_header',BTE_RW_POSTS_HEADER);
   	add_option('bte_rw_posts_footer',BTE_RW_POSTS_FOOTER);
   	add_option('bte_rw_post_header',BTE_RW_POST_HEADER);
   	add_option('bte_rw_post_footer',BTE_RW_POST_FOOTER);
   	add_option('bte_rw_post_excerpt',BTE_RW_POST_EXCERPT);
   	add_option('bte_rw_post_excerpt_header',BTE_RW_POST_EXCERPT_HEADER);
   	add_option('bte_rw_post_excerpt_footer',BTE_RW_POST_EXCERPT_FOOTER);
	$home = get_settings('siteurl');
	$base = '/'.end(explode('/', str_replace(array('\\','/RelatedWebsites.php'),array('/',''),__FILE__)));		
   	add_option('bte_rw_links_img_default',$home.'/wp-content/plugins' . $base.'/BTE_125x125_2.jpg');
   	add_option('bte_rw_posts_img_default',$home.'/wp-content/plugins' . $base.'/BTE_125x125_2.jpg');
   	add_option('bte_rw_clicks',0);	
   	add_option('bte_rw_links_add',BTE_RW_ADD);	
   	add_option('bte_rw_posts_add',BTE_RW_ADD);	
   	add_option('bte_rw_links_so',false);	
   	add_option('bte_rw_posts_so',false);	
	if (WPLANG=='')	{
		add_option('bte_rw_lang','en');	
	} else {
		add_option('bte_rw_lang',WPLANG);			
	}
	$result = mysql_list_tables(DB_NAME);
	$tables = array();
	while ($row = mysql_fetch_row($result)) {
		$tables[] = $row[0];
	}
		
   	$table_name = $wpdb->prefix . "bte_rw_sites";
	if (!in_array($table_name, $tables)) {
	   	$sql = "CREATE TABLE $table_name (
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				post_id bigint(20) NOT NULL,
				link text NOT NULL,
				excerpt text NOT NULL,
				img text NOT NULL,
				UNIQUE KEY id (id)
				);";
		$res = $wpdb->query($sql);
		$sql = "CREATE INDEX bte_rw_sites_posts_post_id_idx ON $table_name(post_id);";
		$res = $wpdb->query($sql);
	}
   	$table_name = $wpdb->prefix . "bte_rw_posts";
	if (!in_array($table_name, $tables)) {
	   	$sql = "CREATE TABLE $table_name (
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				post_id bigint(20) NOT NULL,
				link text NOT NULL,
				excerpt text NOT NULL,
				img text NOT NULL,
				UNIQUE KEY id (id)
				);";
		$res = $wpdb->query($sql);
		$sql = "CREATE INDEX bte_rw_posts_posts_post_id_idx ON $table_name(post_id);";
		$res = $wpdb->query($sql);
	}	
	$webclicks_table_name = $wpdb->prefix . "bte_rw_webclicks";
	if (!in_array($webclicks_table_name, $tables)) {
	   	$sql = "CREATE TABLE $webclicks_table_name (
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				guid text NOT NULL,
				click text NOT NULL,
				UNIQUE KEY id (id)
				);";
		$res = $wpdb->query($sql);
	}
	$siteclicks_table_name = $wpdb->prefix . "bte_rw_siteclicks";
	if (!in_array($siteclicks_table_name, $tables)) {
	   	$sql = "CREATE TABLE $siteclicks_table_name (
				ID bigint(20) NOT NULL AUTO_INCREMENT,
				guid text NOT NULL,
				click text NOT NULL,
				UNIQUE KEY id (id)
				);";
		$res = $wpdb->query($sql);
	}
	add_option("bte_rw_db_version", BTE_RW_DB_SCHEMA);
	if (function_exists('wp_cache_flush')) {
		wp_cache_flush();
	}					
}
?>