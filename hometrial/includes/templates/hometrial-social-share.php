<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	$idsString = is_array( $products_ids ) ? implode( ',',$products_ids ) : '';

	$share_link = get_the_permalink() . '?hometrialpids='.$idsString;
	$share_title = get_the_title();

	$thumb_id = get_post_thumbnail_id();
	$thumb_url = wp_get_attachment_image_src( $thumb_id, 'thumbnail-size', true );

	$social_button_list = [
		'facebook' => [
			'title' => esc_html__( 'Facebook', 'hometrial' ),
			'url' 	=> 'https://www.facebook.com/sharer/sharer.php?u='.$share_link,
		],
		'twitter' => [
			'title' => esc_html__( 'Twitter', 'hometrial' ),
			'url' 	=> 'https://twitter.com/share?url=' . $share_link.'&amp;text='.$share_title,
		],
		'pinterest' => [
			'title' => esc_html__( 'Pinterest', 'hometrial' ),
			'url' 	=> 'https://pinterest.com/pin/create/button/?url='.$share_link.'&media='.$thumb_url[0],
		],
		'linkedin' => [
			'title' => esc_html__( 'Linkedin', 'hometrial' ),
			'url' 	=> 'https://www.linkedin.com/shareArticle?mini=true&url='.$share_link.'&amp;title='.$share_title,
		],
		'email' => [
			'title' => esc_html__( 'Email', 'hometrial' ),
			'url' 	=> 'mailto:?subject='.esc_html__('Whislist&body=My whislist:', 'hometrial') . $share_link,
		],

		'reddit' => [
			'title' => esc_html__( 'Reddit', 'hometrial' ),
			'url' 	=> 'http://reddit.com/submit?url='.$share_link.'&amp;title='.$share_title,
		],
		'telegram' => [
			'title' => esc_html__( 'Telegram', 'hometrial' ),
			'url' 	=> 'https://telegram.me/share/url?url=' . $share_link,
		],
		'odnoklassniki' => [
			'title' => esc_html__( 'Odnoklassniki', 'hometrial' ),
			'url' 	=> 'https://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=' . $share_link,
		],
		'whatsapp' => [
			'title' => esc_html__( 'WhatsApp', 'hometrial' ),
			'url' 	=> 'https://wa.me/?text=' . $share_link,
		],
		'vk' => [
			'title' => esc_html__( 'VK', 'hometrial' ),
			'url' 	=> 'https://vk.com/share.php?url=' . $share_link,
		],
	];


	$default_buttons = [
        'facebook'   => esc_html__( 'Facebook', 'hometrial' ),
        'twitter'    => esc_html__( 'Twitter', 'hometrial' ),
        'pinterest'  => esc_html__( 'Pinterest', 'hometrial' ),
        'linkedin'   => esc_html__( 'Linkedin', 'hometrial' ),
        'telegram'   => esc_html__( 'Telegram', 'hometrial' ),
    ];
	$button_list = hometrial_get_option( 'social_share_buttons','hometrial_table_settings_tabs', $default_buttons );
	$button_text = hometrial_get_option( 'social_share_button_title','hometrial_table_settings_tabs', 'Share:' );

?>

<div class="hometrial-social-share">
	<span class="hometrial-social-title"><?php esc_html_e( $button_text, 'hometrial' ); ?></span>
	<ul>
		<?php
			foreach ( $button_list as $buttonkey => $button ) {
				?>
				<li>
					<a rel="nofollow" href="<?php echo esc_url( $social_button_list[$buttonkey]['url'] ); ?>" <?php echo ( $buttonkey === 'email' ? '' : 'target="_blank"' ) ?>>
						<span class="hometrial-social-icon">
							<?php echo hometrial_icon_list( $buttonkey ); ?>
						</span>
					</a>
				</li>
				<?php
			}
		?>
	</ul>
</div>