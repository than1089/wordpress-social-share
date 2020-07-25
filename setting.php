<?php

class ThanNgo_Social_Share_Setting {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'social_share_add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'social_share_settings_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_jquery_ui_scripts' ) );
	}

	function enqueue_jquery_ui_scripts() {
        wp_enqueue_script( 'jquery-ui-sortable' );

	}

	function social_share_add_admin_menu(  ) { 

		add_menu_page( 'Social Share', 'Social Share', 'manage_options', 'social_share', array( $this, 'social_share_options_page' ) );

	}


	function social_share_settings_init(  ) { 

		register_setting( 'pluginPage', 'social_share_settings' );

		add_settings_section(
			'social_share_pluginPage_section', 
			__( 'Social Share settings', 'tn-social-share' ), 
			array( $this, 'social_share_settings_section_callback' ), 
			'pluginPage'
		);

		add_settings_field( 
			'social_share_post_types', 
			__( 'Show on these post types', 'tn-social-share' ), 
			array( $this, 'social_share_post_types_render' ), 
			'pluginPage', 
			'social_share_pluginPage_section' 
		);

		add_settings_field( 
			'social_share_networks', 
			__( 'Social Networks', 'tn-social-share' ), 
			array( $this, 'social_share_networks_render' ), 
			'pluginPage', 
			'social_share_pluginPage_section' 
		);

		add_settings_field( 
			'social_share_icon_size', 
			__( 'Icon Size', 'tn-social-share' ), 
			array( $this, 'social_share_icon_size_render' ), 
			'pluginPage', 
			'social_share_pluginPage_section' 
		);

		add_settings_field( 
			'social_share_using_custom_color', 
			__( 'Using Custom Color', 'tn-social-share' ), 
			array( $this, 'social_share_using_custom_color_render' ), 
			'pluginPage', 
			'social_share_pluginPage_section' 
		);

		add_settings_field( 
			'social_share_custom_color', 
			__( 'Custom Color', 'tn-social-share' ), 
			array( $this, 'social_share_custom_color_render' ), 
			'pluginPage', 
			'social_share_pluginPage_section' 
		);

		add_settings_field( 
			'social_share_positions', 
			__( 'Display Positions', 'tn-social-share' ), 
			array( $this, 'social_share_positions_render' ), 
			'pluginPage', 
			'social_share_pluginPage_section' 
		);

	}


	function social_share_post_types_render(  ) { 

		$options = get_option( 'social_share_settings' );
		$post_types = get_post_types_by_support(array('title', 'editor', 'thumbnail'));
		?>
		<?php foreach ($post_types as $post_type): ?>
			<label><?php echo ucfirst($post_type) ?>
				<input type='checkbox' name='social_share_settings[social_share_post_types][<?php echo $post_type ?>]'
					<?php checked( $options['social_share_post_types'][$post_type], 1 ); ?> value='1'>
			</label>
		<?php endforeach ?>

		<?php
	}

	function social_share_networks_render(  ) { 

		$options = get_option( 'social_share_settings' );
		$social_networks = array( 'Facebook', 'Twitter', 'Pinterest', 'LinkedIn', 'Whatsapp' );

		$merged_social_networks = array_merge( $options['social_share_networks'], array_combine( $social_networks, $social_networks ) );
		$social_networks = array_keys($merged_social_networks);
		?>
		<div class="sortable-networks">
		<?php foreach ($social_networks as $network): ?>
			<label><?php echo ucfirst($network) ?>
				<input type='checkbox' name='social_share_settings[social_share_networks][<?php echo $network ?>]'
					<?php checked( $options['social_share_networks'][$network], 1 ); ?> value='1'>
			</label>
		<?php endforeach ?>
		</div>
		<p><em>Drag to change the order of the items displaying.</em></p>

		<?php

	}

	function social_share_icon_size_render(  ) { 

		$options = get_option( 'social_share_settings' );
		$sizes = array(
			'small'  => __( 'Small', 'tn-social-share' ),
			'medium' => __( 'Medium', 'tn-social-share' ),
			'large'  => __( 'Large', 'tn-social-share' ),
		);
		?>
		<?php foreach ($sizes as $key => $value): ?>
			<label><?php echo $value ?>
				<input type='radio' name='social_share_settings[social_share_icon_size]'
					<?php checked( $options['social_share_icon_size'], $key ); ?> value='<?php echo $key ?>'>
			</label>
		<?php endforeach ?>

		<?php
	}

	function social_share_using_custom_color_render(  ) { 

		$options = get_option( 'social_share_settings' );

		?>
		<input type='checkbox' name='social_share_settings[social_share_using_custom_color]' id="using-custom-color"
			<?php checked( $options['social_share_using_custom_color'], 1 ); ?> value='1'>

		<?php
	}

	function social_share_custom_color_render(  ) { 

		$options = get_option( 'social_share_settings' );

		?>
		<input type='color' name='social_share_settings[social_share_custom_color]'
			value='<?php echo $options['social_share_custom_color'] ?>'>
		<?php
	}

	function social_share_positions_render(  ) { 

		$options = get_option( 'social_share_settings' );
		$positions = array(
			'below_title'            => __( 'Below Title', 'tn-social-share' ),
			'floating_left'          => __( 'Floating Left', 'tn-social-share' ),
			'after_post_content'     => __( 'After Post Content', 'tn-social-share' ),
			'inside_featured_image'  => __( 'Inside The Feature Image', 'tn-social-share' ),
		);
		?>
		<?php foreach ($positions as $key => $value): ?>
			<label><?php echo $value ?>
				<input type='checkbox' name='social_share_settings[social_share_positions][<?php echo $key ?>]'
					<?php checked( $options['social_share_positions'][$key], 1 ); ?> value='1'>
			</label>
		<?php endforeach ?>

		<?php
	}


	function social_share_settings_section_callback(  ) { 

		echo __( 'Change your settings below.<br/>You can also use this shortcode for flexible use: [tn_social_share]', 'tn-social-share' );

	}


	function social_share_options_page(  ) { 

		?>
		<form action='options.php' method='post' id='social-share-settings'>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

		</form>
		<script type="text/javascript">
			jQuery( function($) {
			    $( ".sortable-networks" ).sortable({
			    	placeholder: "ui-state-highlight"
			    }).disableSelection();

			    $('#using-custom-color').change(function() {
			    	var input = $(this);
			    	if (input.prop('checked')) {
			    		input.parents('tr').next().show();
			    	} else {
			    		input.parents('tr').next().hide();
			    	}
			    }).trigger('change');
			 } );
		</script>
		<style type="text/css">
			.ui-state-highlight {
				border: 1px dashed #ccc;
				width: 100px;
				height: 1em;
				display: inline-block;
				visibility: visible;
			}
			#social-share-settings label {
				margin-right: 5px;
			}
		</style>
		<?php
	}
}

new ThanNgo_Social_Share_Setting();


