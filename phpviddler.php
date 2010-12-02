<?php
/* Viddler PHP Wrapper for Viddler's API 
  Version 2.0
  Released: December 2010.
*/

/* Viddler Class
  use $var = new Viddler_V2(API KEY); */
class Viddler_V2 {
	public $api_key = NULL;
	
	// Construct! Like the Matrix.
	public function __construct($api_key)	{	$this->api_key = $api_key;	}

	/**
	Can be called like such:
	$__api = new Viddler_API("YOUR KEY");
	$array = $__api->viddler_users_getProfile(array("user"=>"phpfunk"));
	**/
	public function __call($method, $args) { return self::call($method, $args, "object");	}
	
	protected function call($method, $args, $call)
	{ 
		/**
		Format the Method
		Accepted Formats:
		
		$viddler->viddler_users_auth();
		**/
		$method = str_replace("_", ".", $method);
		
		//If the method exists here, call it
		if (method_exists($this, $method)) { return $this->$method($args[0]); }
		
		$query = array();
		
		// Which methods should we require 
		// a secure call for?
		$secure_methods = array(
			'viddler.users.auth'
		);
		
		// Which methods should we require
		// a POST for?
		$post_methods = array(
		  'viddler.encoding.cancel',
			'viddler.encoding.encode',
			'viddler.encoding.setOptions',
			'viddler.groups.addVideo',
			'viddler.groups.join',
 	 	 	'viddler.groups.leave',
			'viddler.groups.removeVideo',
			'viddler.playlists.addVideo',
			'viddler.playlists.create',
			'viddler.playlists.delete',
			'viddler.playlists.removeVideo',
			'viddler.playlists.moveVideo',
			'viddler.playslists.setDetails',
			'viddler.users.setSettings',
			'viddler.users.setProfile',
			'viddler.users.setOptions',
			'viddler.users.acceptFriendRequest',
			'viddler.users.ignoreFriendRequest',
			'viddler.users.sendFriendRequest',
			'viddler.users.subscribe',
			'viddler.users.unsubscribe',
			'viddler.videos.setDetails',
			'viddler.videos.setPermalink',
			'viddler.videos.comments.add',
			'viddler.videos.comments.remove',
			'viddler.videos.upload',
			'viddler.videos.delete',
			'viddler.videos.delFile',
			'viddler.videos.favorite',
			'viddler.videos.unfavorite',
			'viddler.videos.setPermalink',
			'viddler.videos.setThumbnail',
			'viddler.videos.setDetails',
			'viddler.videos.enableAds',
			'viddler.videos.disableAds'
		);
		
		// Which methods should we require
		// binary transfer for?
		$binary_methods = array(
			'viddler.videos.setThumbnail',
			'viddler.videos.upload'
		);
		
		$binary = (in_array($method, $binary_methods)) ? TRUE : FALSE;
		$post = (in_array($method, $post_methods)) ? TRUE : FALSE;
		
		// Figure protocol http:// or https://
		$protocol = (in_array($method, $secure_methods)) ? "https" : "http";
		
		// Build API endpoint URL
		$url = $protocol . "://api.viddler.com/api/v2/" . $method . ".php";
		
		if ($post === TRUE) { // Is a post method
				array_push($query, "key=" . $this->api_key); // Adds API key to the POST arguments array
		} else {
		  $url .= "?key=" . $this->api_key;
		}
		
		//Figure the query string
		if (@count($args[0]) > 0 && is_array($args[0])) {
			foreach ($args[0] as $k => $v) {
				if ($k != "response_type" && $k != "api_key") {
					array_push($query, "$k=$v");
				}
			}
			$query_arr = $query;
			$query = implode("&", $query);
			if ($post === FALSE) {
				$url .= (!empty($query)) ? "&" . $query : "";
			}
		}
		else {
			$query = NULL;
			$args[0] = array();
		}
		
		// Custruct the cURL call
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, TRUE);
		curl_setopt ($ch, CURLOPT_HEADER, FALSE);
		curl_setopt ($ch, CURLOPT_TIMEOUT, FALSE);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		
		// Figure POST vs. GET
		if ($post == TRUE) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			if ($binary === TRUE) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $args[0]);
			}
			else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
			}
		}
		else {
			curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
		}
		
		//G et the response
		$response = curl_exec($ch);
		
		if (!$response) {
			$response = $error = curl_error($ch);
			
			return $response;
		}
		else {
			$response = unserialize($response);
		}
		
		curl_close($ch);
		return $response;
	}
}

?>