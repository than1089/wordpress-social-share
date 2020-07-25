<?php

class ThanNgo_Social_Share_Frontend {
	public $post_title;

	function __construct() {
		add_action( 'wp', array( $this, 'init_frontend_social_share') );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'tn_social_share', array( $this, 'get_social_share_html') );
	}

	function enqueue_scripts() {
		wp_enqueue_style( 'social-share' , plugin_dir_url( __FILE__ ) . 'social-share.css' );
		wp_enqueue_script( 'social-share' , plugin_dir_url( __FILE__ ) . 'social-share.js', array( 'jquery' ) );
	}

	function init_frontend_social_share() {
		$this->post_title = get_the_title();
		$options = get_option( 'social_share_settings' );

		$enable_post_types = (array)$options['social_share_post_types'];
		if ( !isset($enable_post_types[get_post_type()]) || !is_singular() ) {
			return;
		}

		if ( isset($options['social_share_positions']['below_title']) ) {
			add_filter( 'the_content', array( $this, 'prepend_to_content') );
		}
		if ( isset($options['social_share_positions']['floating_left']) ) {
			add_action( 'wp_footer', array( $this, 'display_floating_left') );
		}
		if ( isset($options['social_share_positions']['after_post_content']) ) {
			add_filter( 'the_content', array( $this, 'append_to_content') );
		}
		if ( isset($options['social_share_positions']['inside_featured_image']) ) {
			add_filter( 'post_thumbnail_html', array( $this, 'append_to_thumbnail') );
		}
	}

	function prepend_to_content( $content ) {
		return $this->get_social_share_html() . $content;
	}

	function append_to_content( $content ) {
		return $content . $this->get_social_share_html();
	}

	function append_to_thumbnail( $html ) {
		return $html . $this->get_social_share_html( 'inside-the-thumbnail' );
	}

	function display_floating_left() {
		echo $this->get_social_share_html( 'floating-left' );
	}

	function get_social_share_html( $position = '' ) {
		$options = get_option( 'social_share_settings' );

		$social_networks = array_keys((array)$options['social_share_networks']);
		if ( empty($social_networks) ) {
			return;
		}

		// Remove Whatsapp on desktop
		$whatsapp_index = array_search( 'Whatsapp', $social_networks );
		if ( !wp_is_mobile() && $whatsapp_index ) {
			unset($social_networks[$whatsapp_index]);
		}

		$setting_size = $options['social_share_icon_size'];
		$using_custom_color = $options['social_share_using_custom_color'];
		$custom_color = $options['social_share_custom_color'];

		$size = $setting_size ? $setting_size : 'small';

		$post_url = urlencode(get_the_permalink());
		$post_thumbnail = urlencode(get_the_post_thumbnail_url());
		$post_title = $this->post_title;

		ob_start();
		?>
		<div class="tn-social-share-wrapper <?php echo $position ?>">
			<ul class="tn-social-share size-<?php echo $size ?><?php echo $using_custom_color ? ' custom-color' : '' ?>"
				<?php if ($using_custom_color) { echo 'style="fill: ' . $custom_color . '; color: ' . $custom_color . '"'; } ?>>
				<?php foreach ($social_networks as $network): ?>
					<li class="share-<?php echo strtolower($network) ?>">
						<?php if ($network == 'Facebook'): ?>
							<a href="https://www.facebook.com/sharer.php?u=<?php echo $post_url; ?>">
								<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Facebook icon</title><path d="M23.9981 11.9991C23.9981 5.37216 18.626 0 11.9991 0C5.37216 0 0 5.37216 0 11.9991C0 17.9882 4.38789 22.9522 10.1242 23.8524V15.4676H7.07758V11.9991H10.1242V9.35553C10.1242 6.34826 11.9156 4.68714 14.6564 4.68714C15.9692 4.68714 17.3424 4.92149 17.3424 4.92149V7.87439H15.8294C14.3388 7.87439 13.8739 8.79933 13.8739 9.74824V11.9991H17.2018L16.6698 15.4676H13.8739V23.8524C19.6103 22.9522 23.9981 17.9882 23.9981 11.9991Z"/></svg>
							</a>
						<?php endif ?>
						<?php if ($network == 'Twitter'): ?>
							<a href="https://twitter.com/share?url=<?php echo $post_url; ?>&text=<?php echo $post_title; ?>">
								<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Twitter icon</title><path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124C7.691 8.094 4.066 6.13 1.64 3.161c-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63.961-.689 1.8-1.56 2.46-2.548l-.047-.02z"/></svg>
							</a>
						<?php endif ?>
						<?php if ($network == 'LinkedIn'): ?>
							<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $post_url; ?>&title=<?php echo $post_title; ?>">
								<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>LinkedIn icon</title><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
							</a>

						<?php endif ?>
						<?php if ($network == 'Pinterest'): ?>
							<a href="https://pinterest.com/pin/create/bookmarklet/?media=<?php echo $post_thumbnail ?>&url=<?php echo $post_url ?>&description=<?php echo $post_title ?>">
								<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>Pinterest icon</title><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>
							</a>
						<?php endif ?>
						<?php if ($network == 'Whatsapp' ): ?>
							<a href="https://api.whatsapp.com/send?text=<?php echo $post_title . ' ' . $post_url; ?>">
								<svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title>WhatsApp icon</title><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
							</a>
						<?php endif ?>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}
}

new ThanNgo_Social_Share_Frontend();