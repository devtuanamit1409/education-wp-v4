<?php
add_action( 'thim_form_login_widget', 'thim_form_login_widget', 10, 1 );
function thim_form_login_widget( $captcha ) { ?>
	<form name="loginpopopform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>"
		  method="post" novalidate>
		<?php do_action( 'thim_before_login_form' ); ?>
		<p class="login-username">
			<input type="text" name="log" placeholder="<?php esc_html_e( 'Username or email', 'eduma' ); ?>"
				   class="input required" size="20"/>
		</p>
		<p class="login-password">
			<input type="password" name="pwd" placeholder="<?php esc_html_e( 'Password', 'eduma' ); ?>"
				   class="input required" value="" size="20"/>
		</p>
		<?php
		/**
		 * Fires following the 'Password' field in the login form.
		 *
		 * @since 2.1.0
		 */
		do_action( 'login_form' );
		do_action( 'login_enqueue_scripts' );
		?>
		<?php if ( $captcha == 'yes' ) : ?>
			<p class="thim-login-captcha">
				<?php
				$value_1 = rand( 1, 9 );
				$value_2 = rand( 1, 9 );
				?>
				<input type="text" data-captcha1="<?php echo esc_attr( $value_1 ); ?>"
					   data-captcha2="<?php echo esc_attr( $value_2 ); ?>"
					   placeholder="<?php echo esc_attr( $value_1 . ' &#43; ' . $value_2 . ' &#61;' ); ?>"
					   class="captcha-result required"/>
			</p>
		<?php endif; ?>
		<?php echo '<a class="lost-pass-link" href="' . thim_get_lost_password_url() . '" title="' . esc_attr__( 'Lost Password', 'eduma' ) . '">' . esc_html__( 'Lost your password?', 'eduma' ) . '</a>'; ?>

		<p class="forgetmenot login-remember">
			<label for="popupRememberme"><input name="rememberme" type="checkbox"
												value="forever"
												id="popupRememberme"/> <?php esc_html_e( 'Remember Me', 'eduma' ); ?>
			</label></p>
		<p class="submit login-submit">
			<input type="submit" name="wp-submit"
				   class="button button-primary button-large"
				   value="<?php esc_attr_e( 'Login', 'eduma' ); ?>"/>
			<input type="hidden" name="redirect_to"
				   value="<?php echo esc_url( thim_eduma_get_current_url() ); ?>"/>
			<input type="hidden" name="testcookie" value="1"/>
			<input type="hidden" name="nonce"
				   value="<?php echo wp_create_nonce( 'thim-loginpopopform' ) ?>"/>
			<input type="hidden" name="eduma_login_user">
		</p>

		<?php do_action( 'thim_after_login_form' ); ?>

	</form>
<?php }

add_action( 'thim_form_register_widget', 'thim_form_register_widget', 10, 3 );
function thim_form_register_widget( $captcha, $term, $redirect_to = 'account' ) { ?>
	<form class="<?php if ( get_theme_mod( 'thim_auto_login', true ) ) {
		echo 'auto_login';
	} ?>" name="registerformpopup"
		  action="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login_post' ) ); ?>"
		  method="post" novalidate="novalidate">

		<input type="hidden" name="register_security" value="<?php echo wp_create_nonce( 'ajax_register_nonce' ) ?>"/>

		<p>
			<input placeholder="<?php esc_attr_e( 'Username', 'eduma' ); ?>"
				   type="text" name="user_login" class="input required"/>
		</p>

		<p>
			<input placeholder="<?php esc_attr_e( 'Email', 'eduma' ); ?>"
				   type="email" name="user_email" class="input required"/>
		</p>

		<?php if ( get_theme_mod( 'thim_auto_login', true ) ) { ?>
			<p>
				<input placeholder="<?php esc_attr_e( 'Password', 'eduma' ); ?>"
					   type="password" name="password" class="input required"/>
			</p>
			<p>
				<input
					placeholder="<?php esc_attr_e( 'Repeat Password', 'eduma' ); ?>"
					type="password" name="repeat_password"
					class="input required"/>
			</p>
		<?php } ?>

		<?php
		if ( is_multisite() && function_exists( 'gglcptch_login_display' ) ) {
			gglcptch_login_display();
		}

		do_action( 'register_form' );
		?>

		<?php if ( $captcha == 'yes' ) : ?>
			<p class="thim-login-captcha">
				<?php
				$value_1 = rand( 1, 9 );
				$value_2 = rand( 1, 9 );
				?>
				<input type="text"
					   data-captcha1="<?php echo esc_attr( $value_1 ); ?>"
					   data-captcha2="<?php echo esc_attr( $value_2 ); ?>"
					   placeholder="<?php echo esc_attr( $value_1 . ' &#43; ' . $value_2 . ' &#61;' ); ?>"
					   class="captcha-result required"/>
			</p>
		<?php endif; ?>

		<?php
		if ( $term ):
			$target = ( isset( $term['is_external'] ) && ! empty( $term['is_external'] ) ) ? '_blank' : '_self';
			$rel = ( isset( $term['nofollow'] ) && ! empty( $term['nofollow'] ) ) ? 'nofollow' : 'dofollow';
			?>
			<p>
				<input type="checkbox" class="required" name="term" id="termFormFieldPopup">
				<label
					for="termFormField"><?php printf( __( 'I accept the <a href="%s" target="%s" rel="%s">Terms of Service</a>', 'eduma' ), esc_url( $term['url'] ), $target, $rel ); ?></label>
			</p>
		<?php endif; ?>
		<?php
		if ( $redirect_to == 'current' ) {
			$register_redirect = esc_url( thim_eduma_get_current_url() );
		} else {
			$register_redirect = get_theme_mod( 'thim_register_redirect', false );
			if ( empty( $register_redirect ) ) {
				$register_redirect = add_query_arg( 'result', 'registered', thim_get_login_page_url() );
			}
		}
		if ( ! empty( $_REQUEST['redirect_to'] ) ) {
			$register_redirect = $_REQUEST['redirect_to'];
		}
		?>
		<input type="hidden" name="redirect_to"
			   value="<?php echo esc_url( $register_redirect ); ?>"/>
		<input type="hidden" name="modify_user_notification" value="1">
		<input type="hidden" name="eduma_register_user">


		<?php do_action( 'signup_hidden_fields', 'create-another-site' ); ?>
		<p class="submit">
			<input type="submit" name="wp-submit" class="button button-primary button-large"
				   value="<?php echo esc_attr_x( 'Sign up', 'Login popup form', 'eduma' ); ?>"/>
		</p>
	</form>
<?php }

if ( ! function_exists( "thim_sub_info_login_popup" ) ) {
	function thim_sub_info_login_popup( $text_profile = '', $text_logout = '' ) {
		$user = wp_get_current_user();
		if ( 0 == $user->ID ) {
			return;
		}
		$user_profile_edit = get_edit_user_link( $user->ID );
		$user_avatar       = get_avatar( $user->ID );
		$html              = $menu_items_output = '';

		if ( ! class_exists( 'LearnPress' ) ) {
			$html .= '<a href="' . esc_url( $user_profile_edit ) . '" class="profile"><span class="author">' . wp_kses_post( $text_profile ) . ' ' . $user->display_name . '</span>' . ( $user_avatar ) . '</a>';
		} else {
			$profile   = LP_Profile::instance();
			$user_info = '<a href="' . esc_url( $profile->get_tab_link( 'settings', true ) ) . '" class="profile">' . ( $user_avatar ) . '<span class="author">' . wp_kses_post( $text_profile ) . ' ' . $user->display_name . '</span></a>';
			$html      .= $user_info;
			$html      .= '<ul class="user-info">';

			$html .= '<li class="menu-item-user"> ' . $user_info . '</li>';

			$items = apply_filters( 'thim_menu_profile_items', array( 'courses', 'orders', 'become_a_teacher', 'certificates', 'wishlist', 'settings' ) );

			if ( is_array( $items ) && count( $items ) > 0 ) {
				for ( $index = 0; $index < count( $items ); $index ++ ) {
					switch ( $items[$index] ) {
						case 'courses':
							$menu_items_output .= '<li class="menu-item-courses"><a href="' . esc_url( $profile->get_tab_link( 'courses', true ) ) . '"><i class="lp-icon-my-courses"></i>' . esc_html__( 'My Courses', 'eduma' ) . '</a></li>';
							break;
						case 'orders':
							$menu_items_output .= '<li class="menu-item-orders"><a href="' . esc_url( $profile->get_tab_link( 'orders', true ) ) . '"><i class="lp-icon-shopping-cart"></i>' . esc_html__( 'My Orders', 'eduma' ) . '</a></li>';
							break;
						case 'become_a_teacher':
							if ( learn_press_get_page_link( 'become_a_teacher' ) && ! array_intersect( array( 'administrator', 'lp_teacher', 'instructor' ), $user->roles ) ) {
								$menu_items_output .= '<li class="menu-item-become-a-teacher"><a href="' . learn_press_get_page_link( 'become_a_teacher' ) . '"><i class="tk tk-user"></i>' . esc_html__( 'Become An Instructor', 'eduma' ) . '</a></li>';
							}
							break;
						case 'certificates':
							if ( class_exists( 'LP_Addon_Certificates' ) ) {
								$menu_items_output .= '<li class="menu-item-certificates"><a href="' . esc_url( $profile->get_tab_link( 'certificates', true ) ) . '"><i class="lp-icon-certificate"></i>' . esc_html__( 'My Certificates', 'eduma' ) . '</a></li>';
							}
							break;
						case 'wishlist':
							if ( class_exists( 'LP_Addon_Wishlist' ) ) {
								$menu_items_output .= '<li class="menu-item-certificates"><a href="' . esc_url( $profile->get_tab_link( 'wishlist', true ) ) . '"><i class="tk tk-heart"></i>' . esc_html__( 'Wishlist', 'eduma' ) . '</a></li>';
							}
							break;
						case 'settings':
							$menu_items_output .= '<li class="menu-item-settings"><a href="' . esc_url( $profile->get_tab_link( 'settings', true ) ) . '"><i class="tk tk-cog"></i>' . esc_html__( 'Edit Profile', 'eduma' ) . '</a></li>';
							break;
						default:
							break;
					}
				}
			}

			$html .= apply_filters( 'thim_menu_profile_items_extend', $menu_items_output );
			$html .= '<li class="menu-item-log-out">' . '<a href="' . wp_logout_url( home_url() ) . '"><i class="lp-icon-sign-out"></i>' . wp_kses_post( $text_logout ) . '</a></li>';
			$html .= '</ul>';
		}

		return $html;
	}
}
