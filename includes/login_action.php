<?php
function vaulter_login_function($vaulter_auth_page,$secret_key_option,$white_ip,$user_login,$secret_input,$current_ip){
	$get_vaulter = new Vaulter;
	$secret_hash = $get_vaulter->create_secret_key($secret_input);
	
	$user = get_user_by('login',$user_login);
	
	if($current_ip === $white_ip){
		if(!empty($user->ID)){
			if($secret_hash == $secret_key_option){
				wp_set_current_user($user->ID, $user_login);
				wp_set_auth_cookie($user->ID); 
				do_action('wp_login', $user_login);	//login action
				//redirect
				if(current_user_can('edit_posts') == true){
					$home_url = home_url().'/admin';
					wp_redirect($home_url);
				}
			} else{
				$redirect = get_page_link($vaulter_auth_page).'?error_msg=secret';
				wp_redirect($redirect);
				//echo 'Secret Not matched!';
			}
		} else{
			$redirect = get_page_link($vaulter_auth_page).'?error_msg=user';
			wp_redirect($redirect);
			//echo 'User Not Found!';
		}
	} else {//ip white list
		$redirect = get_page_link($vaulter_auth_page).'?error_msg=ip';
		wp_redirect($redirect);
		//echo 'IP not Whitelisted!';
	}
}

	//function exucitaion
function login_action_action(){
	$vaulter_options = get_option( 'vaulter_option_name' ); // Array of All Options
	$secret_key_option = $vaulter_options['vaulter_secret_key']; // Secret Key from db
	$white_ip = $vaulter_options['vaulter_ip_white_list']; //set at db
	$vaulter_auth_page = $vaulter_options['vaulter_auth_page']; //set at db

	if(isset($_GET) && isset($_GET['user']) && isset($_GET['secret']) ){
		$user_login = $_GET['user'];
		$secret_input	= $_GET['secret'];
		$current_ip = $_SERVER['REMOTE_ADDR'];

		vaulter_login_function($vaulter_auth_page,$secret_key_option,$white_ip,$user_login,$secret_input,$current_ip);

	} elseif(isset($_GET) && isset($_GET['error_msg'])){
		$error_message = $_GET['error_msg'];
		if($error_message == 'secret'){
			echo 'Secret Not matched!';
		} elseif($error_message ==	'user'){
			echo 'User Not Found!';
		}elseif($error_message == 'ip'){
			echo 'IP not Whitelisted!';
		}
	}else{
		wp_redirect(home_url());
	};
}

//run funtion in selected page
add_filter('the_content','login_action_page_content');
function login_action_page_content($content){
	global $post;

	$vaulter_options = get_option( 'vaulter_option_name' ); // Array of All Options
	$vaulter_auth_page = $vaulter_options['vaulter_auth_page']; //set at db
	if($post->ID == $vaulter_auth_page){
		login_action_action();
	} else{
		return $content;
	}
}