<?php 
/*
	Plugin Name: WB Embed Code
	Plugin URI: https://bitbucket.org/khosroblog/wb-embed-code/
	Description: A simple plugin created by the WP Bucket plugin for embed bitbucket codes in the wordpress posts with shortcode.
	Author: Hadi Khosrojerdi
	Version: 0.1.0
	Author URI: http://khosroblog.com
	License: GNU General Public License v2 or later 
*/


	/*
	* Set http arguments.
	* 
	* @since 0.1.0
	* @param array $args An array of http arguments.
	*/
	function set_http_args( $http_args ){
		// To avoid error display :
		// WARNING: C:\wamp\www\wordpress\wp-includes\class-http.php:1765 - gzinflate(): data error
		$http_args['decompress'] = false;
		return $http_args;
	}
	add_action("wprest_http_request", "set_http_args");
	
	
	/*
	* Embed bitbucket code in wordpress posts.
	* 
	* @since 0.1.0
	* @param array $args An array of arguments like repo_slug, class, is_private.
	* @usage e.g : [bitbucket revision="v0.1.2" repo_slug="wp_bucket" owner="khosroblog" path="readme.txt" is_private="0" ]
	* @return html | null .
	*/
	function wb_embed_code( $args=array() ){
		global $authordata, $WP_Bucket;
		if( !is_a( $WP_Bucket, "WP_Bucket") ){ return; }
		
		$defaults = array(
			"repo_slug"		=>	"", // repository slug
			"owner"			=>	"", // username
			"revision"		=>	"master", //A SHA1 value for commit. also you can determine a branch , bookmark or tag name for that.
			"path"			=>	"", // a file or directory exists in the repository
			"is_private"	=>	0
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args );
		
		$author_id = (int) $authordata->ID;
		// for public reposities you don't need to user authenticate, so tokens should be null.
		if( !$is_private ){
			$WP_Bucket->config(array(
				"oauth_token"		 => null,
				"oauth_token_secret" => null,
			));
		
		// for private repositories author should be authenticate. 
		// so click on the "login by bitbucket" button in the "wp-bucket-test" page. ( http://yoursite/wp-bucket-test ) 
		}else{
			$WP_Bucket->config( "wp_user_id=" . $author_id );
		}
		
		$repository = $WP_Bucket->api("/1.0/repositories/$owner/$repo_slug/raw/$revision/$path");
		if( !is_wp_error( $repository ) ){
			echo "<pre>" . esc_html( $repository ) . "</pre>" ;
		}else{
			//var_dump( $repository  );
		}
	}
	add_shortcode("bitbucket", "wb_embed_code");
	
?>
