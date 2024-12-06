<div class="twitter-wrapper">
	<div class="twitter-inner">
	<?php
	if ( $instance['title'] <> '' ) {
		echo ent2ncr( $args['before_title'] . $instance['title'] . '<i class="fa fa-twitter"></i>' . $args['after_title'] );
	}

	if ( $twitter && is_array( $twitter ) ) : ?>
		<div class="thim-tweets">
			<div class="twitter-item-wrapper">
			<?php foreach ( $twitter as $tweet ):
				$twitterTime = strtotime( $tweet['created_at'] );
				$avatar = $tweet['user']['profile_image_url'];
				$username = $tweet['user']['screen_name'];
				$location = $tweet['user']['location'];
				$latestTweet = $tweet['text'];
				$latestTweet = preg_replace( '/http:\/\/([a-z0-9_\.\-\+\&\!\#\~\/\,]+)/i', '&nbsp;<a href="http://$1" target="_blank">http://$1</a>&nbsp;', $latestTweet );
				$latestTweet = preg_replace( '/@([a-z0-9_]+)/i', '&nbsp;<a href="http://twitter.com/$1" target="_blank">@$1</a>&nbsp;', $latestTweet );
				
				?>
				<div class="tweet-item">
					<div class="content">
						<?php echo ent2ncr( $latestTweet ); ?>
					</div>
					<div class="user">
						<div class="avatar">
							<img src="<?php echo esc_attr($avatar); ?>" alt="">
						</div>
						<div class="info">
							<div class="name"><?php echo esc_attr($username); ?></div>
							<div class="location"><?php echo esc_attr($location); ?></div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
	</div>
</div>
<div class="link-follow"><a href="https://twitter.com/<?php echo esc_attr($username); ?>"><?php _e('Follow Us', 'thim-twitter'); ?></a></div>
