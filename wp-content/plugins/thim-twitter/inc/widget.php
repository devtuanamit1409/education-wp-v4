<?php

/**
 * Adds ThimTwitter_Widget widget.
 */
class ThimTwitter_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'thimtwitter',
			__( 'Thim Twitter', 'thim-twitter' ),
			array( 'description' => __( 'Get Feed from Twitter', 'thim-twitter' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		$default      = array(
			'consumer_key'        => 'fCuXeJBzIhikOjNFmh7FC7Cpz',
			'consumer_secret'     => 'tLefeE8nyARq1aIAJF7GSIhAoAxQiAMU9aX0RE79F6IVAcfA7J',
			'access_token'        => '3546925700-hzs7KwBYCqsZxP6sYRtjIS4V1TIMgh0zY0Hlhb5',
			'access_token_secret' => 'TmI0MW7QH7KTfdePVX1Swsie7i2K1RziunVc46y0wOALn'
		);
		$thim_twitter = get_option( 'thim_twitter', $default );

		$twitter_id          = $instance['username'];
		$number              = $instance['number'];
		$consumer_key        = $thim_twitter['consumer_key'];
		$consumer_secret     = $thim_twitter['consumer_secret'];
		$access_token        = $thim_twitter['access_token'];
		$access_token_secret = $thim_twitter['access_token_secret'];

		if ( $twitter_id && $number && $consumer_key && $consumer_secret && $access_token && $access_token_secret ) {
			$transName = 'list_tweets_' . $twitter_id;
			$cacheTime = 10;

			$twitterData = get_transient( $transName );
			@$twitter = json_decode( get_transient( $transName ), true );

			if ( false === $twitterData || isset( $twitter['errors'] ) ) {
				$twitter_token = 'twitter_token_' . $twitter_id;
				$token         = !empty( $thim_twitter[$twitter_token] ) ? $thim_twitter[$twitter_token] : false;
				if ( !$token ) {
					// preparing credentials
					$credentials = $consumer_key . ':' . $consumer_secret;
					$toSend      = base64_encode( $credentials );
					// http post arguments
					$args_twitter = array(
						'method'      => 'POST',
						'httpversion' => '1.1',
						'blocking'    => true,
						'headers'     => array(
							'Authorization' => 'Basic ' . $toSend,
							'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8'
						),
						'body'        => array( 'grant_type' => 'client_credentials' )
					);

					add_filter( 'https_ssl_verify', '__return_false' );
					$response = wp_remote_post( 'https://api.twitter.com/oauth2/token', $args_twitter );

					$keys = json_decode( wp_remote_retrieve_body( $response ) );

					if ( $keys ) {
						// saving token to wp_options table
						$token                        = $keys->access_token;
						$thim_twitter[$twitter_token] = $token;
						update_option( 'thim_twitter', $thim_twitter );
					}
				}
				// we have bearer token wether we obtained it from API or from options
				$args_twitter = array(
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(
						'Authorization' => "Bearer $token"
					)
				);

				add_filter( 'https_ssl_verify', '__return_false' );
				$api_url  = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $twitter_id . '&count=' . $number;
				$response = wp_remote_get( $api_url, $args_twitter );
				set_transient( $transName, wp_remote_retrieve_body( $response ), 60 * $cacheTime );
			}
		}
		@$twitter = json_decode( get_transient( $transName ), true );
		include( ThimTwitter::getTemplate( 'default' ) );
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$username = !empty( $instance['username'] ) ? $instance['username'] : 'themeforest';
		$number   = !empty( $instance['number'] ) ? $instance['number'] : 2;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Tweets Display:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['username'] = ( !empty( $new_instance['username'] ) ) ? $new_instance['username'] : 'themeforest';
		$instance['number']   = ( !empty( $new_instance['number'] ) ) ? $new_instance['number'] : 2;

		return $instance;
	}

} // class ThimTwitter_Widget

function register_twitterfeed_widget() {
	register_widget( 'ThimTwitter_Widget' );
}

add_action( 'widgets_init', 'register_twitterfeed_widget' );