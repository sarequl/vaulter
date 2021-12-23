<?php

class Vaulter {
	private $vaulter_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'vaulter_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'vaulter_page_init' ) );
	}

	public function vaulter_add_plugin_page() {
		add_menu_page(
			'Vaulter', // page_title
			'Vaulter', // menu_title
			'manage_options', // capability
			'vaulter', // menu_slug
			array( $this, 'vaulter_create_admin_page' ), // function
			'dashicons-admin-generic', // icon_url
			2 // position
		);
	}

	public function vaulter_create_admin_page() {
		$this->vaulter_options = get_option( 'vaulter_option_name' ); ?>

		<div class="wrap">
			<h2>Vaulter</h2>
			<p>Vaulter Options</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'vaulter_option_group' );
					do_settings_sections( 'vaulter-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function vaulter_page_init() {
		register_setting(
			'vaulter_option_group', // option_group
			'vaulter_option_name', // option_name
			array( $this, 'vaulter_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'vaulter_setting_section', // id
			'Settings', // title
			array( $this, 'vaulter_section_info' ), // callback
			'vaulter-admin' // page
		);

		add_settings_field(
			'vaulter_secret_key', // id
			'Secret Key', // title
			array( $this, 'vaulter_secret_key_callback' ), // callback
			'vaulter-admin', // page
			'vaulter_setting_section' // section
		);
		add_settings_field(
			'vaulter_ip_white_list', // id
			'IP White List', // title
			array( $this, 'vaulter_ip_white_list_callback' ), // callback
			'vaulter-admin', // page
			'vaulter_setting_section' // section
		);
		add_settings_field(
			'vaulter_auth_page', // id
			'Select Auth Page', // title
			array( $this, 'vaulter_auth_page_callback' ), // callback
			'vaulter-admin', // page
			'vaulter_setting_section' // section
		);
	}
	public function create_secret_key($input){
		if(!empty($input)){
			$hashed_key = hash('sha256', $input);
			return $hashed_key;
		}
	}
	public function vaulter_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['vaulter_secret_key'] ) ) {
			
			$this->vaulter_options = get_option( 'vaulter_option_name' );
			
			$get_key = $this->vaulter_options['vaulter_secret_key'];//get current data from db
			$secret_key_input_field = $input['vaulter_secret_key'];//get input field data
			
			if($get_key == $secret_key_input_field){
				$sanitary_values['vaulter_secret_key'] = $get_key;//if matched return existing data
			} else{
				$sanitary_values['vaulter_secret_key'] = $this->create_secret_key( $input['vaulter_secret_key'] );//insert new data
			}
		}
		if ( isset( $input['vaulter_ip_white_list'] ) ) {
			$sanitary_values['vaulter_ip_white_list'] = sanitize_text_field( $input['vaulter_ip_white_list'] );
		}
		if ( isset( $input['vaulter_auth_page'] ) ) {
			$sanitary_values['vaulter_auth_page'] = sanitize_text_field( $input['vaulter_auth_page'] );
		}

		return $sanitary_values;
	}

	public function vaulter_section_info() {
		
	}

	public function vaulter_secret_key_callback() {
		$get_key = $this->vaulter_options['vaulter_secret_key'];
		?>
			<input class="regular-text" type="password" name="vaulter_option_name[vaulter_secret_key]" id="vaulter_secret_key" value="<?php if(!empty($get_key)){ echo $get_key;}?>">
		<?php
	}
	
	public function vaulter_ip_white_list_callback() {
		$ip_white_list_value = $this->vaulter_options['vaulter_ip_white_list'];
		?>
		<input class="regular-text" type="text" name="vaulter_option_name[vaulter_ip_white_list]" id="vaulter_ip_white_list" value="<?php if(!empty($ip_white_list_value)){ echo $ip_white_list_value;}?>"> (Optional)
		<?php
	}

	public function vaulter_auth_page_callback() {
		$vaulter_auth_page_value = $this->vaulter_options['vaulter_auth_page'];
		?>
		<select name="vaulter_option_name[vaulter_auth_page]" id="vaulter_auth_page">
			<option value="">Select</option>
			<?php $pages = get_pages();
				foreach($pages as $page){
					?>
						<option value="<?php echo $page->ID;?>" <?php if($page->ID == $vaulter_auth_page_value){ echo 'selected';}?>><?php echo $page->post_title;?></option>
					<?php
				}
			?>
		</select>
		<?php
	}
}
if ( is_admin() )
	$vaulter = new Vaulter();
