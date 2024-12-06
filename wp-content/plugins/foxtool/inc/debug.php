<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $foxtool_debug_options;
// class thay doi thuoc tinh
if( ! class_exists( 'foxtool_chandebug' ) ) {
class foxtool_chandebug {
	const ANCHOR_EOF = 'EOF';
	protected $wp_config_path;
	protected $wp_config_src;
	protected $wp_configs = array();
	public function __construct( $wp_config_path ) {
		$basename = basename( $wp_config_path );
		if ( ! file_exists( $wp_config_path ) ) {
			throw new Exception( "{$basename} does not exist." );
		}

		if ( ! is_writable( $wp_config_path ) ) {
			throw new Exception( "{$basename} is not writable." );
		}
		$this->wp_config_path = $wp_config_path;
	}
	public function exists( $type, $name ) {
		$wp_config_src = file_get_contents( $this->wp_config_path );
		if ( ! trim( $wp_config_src ) ) {
			throw new Exception( 'Config file is empty.' );
		}
		$this->wp_config_src = str_replace( array( "\r\n", "\n\r", "\r" ), "\n", $wp_config_src );
		$this->wp_configs    = $this->parse_wp_config( $this->wp_config_src );

		if ( ! isset( $this->wp_configs[ $type ] ) ) {
			throw new Exception( "Config type '{$type}' does not exist." );
		}
		return isset( $this->wp_configs[ $type ][ $name ] );
	}
	public function get_value( $type, $name ) {
		$wp_config_src = file_get_contents( $this->wp_config_path );
		if ( ! trim( $wp_config_src ) ) {
			throw new Exception( 'Config file is empty.' );
		}
		$this->wp_config_src = $wp_config_src;
		$this->wp_configs    = $this->parse_wp_config( $this->wp_config_src );
		if ( ! isset( $this->wp_configs[ $type ] ) ) {
			throw new Exception( "Config type '{$type}' does not exist." );
		}
		return $this->wp_configs[ $type ][ $name ]['value'];
	}
	public function add( $type, $name, $value, array $options = array() ) {
		if ( ! is_string( $value ) ) {
			throw new Exception( 'Config value must be a string.' );
		}
		if ( $this->exists( $type, $name ) ) {
			return false;
		}
		$defaults = array(
			'raw'       => false, 
			'anchor'    => "require_once",
			'separator' => PHP_EOL, 
			'placement' => 'before', 
		);
		list( $raw, $anchor, $separator, $placement ) = array_values( array_merge( $defaults, $options ) );
		$raw       = (bool) $raw;
		$anchor    = (string) $anchor;
		$separator = (string) $separator;
		$placement = (string) $placement;
		if ( self::ANCHOR_EOF === $anchor ) {
			$contents = $this->wp_config_src . $this->normalize( $type, $name, $this->format_value( $value, $raw ) );
		} else {
			if ( false === strpos( $this->wp_config_src, $anchor ) ) {
				return false;
			}
			$existingAnchorPosition = strpos($this->wp_config_src, $anchor);
			if ($existingAnchorPosition !== false) {
				$new_src = $this->normalize( $type, $name, $this->format_value( $value, $raw ) );
				$new_src = $new_src . $separator;
				$contents = substr_replace($this->wp_config_src, $new_src, $existingAnchorPosition, 0);
			} else {
				return false;
			}
		}
		return $this->save( $contents );
	}
	public function update( $type, $name, $value, array $options = array() ) {
		if ( ! is_string( $value ) ) {
			throw new Exception( 'Config value must be a string.' );
		}
		$defaults = array(
			'add'       => true, 
			'raw'       => false, 
			'normalize' => false, 
		);
		list( $add, $raw, $normalize ) = array_values( array_merge( $defaults, $options ) );
		$add       = (bool) $add;
		$raw       = (bool) $raw;
		$normalize = (bool) $normalize;
		if ( ! $this->exists( $type, $name ) ) {
			return ( $add ) ? $this->add( $type, $name, $value, $options ) : false;
		}
		$old_src   = $this->wp_configs[ $type ][ $name ]['src'];
		$old_value = $this->wp_configs[ $type ][ $name ]['value'];
		$new_value = $this->format_value( $value, $raw );
		if ( $normalize ) {
			$new_src = $this->normalize( $type, $name, $new_value );
		} else {
			$new_parts    = $this->wp_configs[ $type ][ $name ]['parts'];
			$new_parts[1] = str_replace( $old_value, $new_value, $new_parts[1] ); 
			$new_src      = implode( '', $new_parts );
		}
		$contents = preg_replace(
			sprintf( '/(?<=^|;|<\?php\s|<\?\s)(\s*?)%s/m', preg_quote( trim( $old_src ), '/' ) ),
			'$1' . str_replace( '$', '\$', trim( $new_src ) ),
			$this->wp_config_src
		);

		return $this->save( $contents );
	}
	public function remove( $type, $name ) {
		if ( ! $this->exists( $type, $name ) ) {
			return false;
		}
		$pattern  = sprintf( '/(?<=^|;|<\?php\s|<\?\s)%s\s*(\S|$)/m', preg_quote( $this->wp_configs[ $type ][ $name ]['src'], '/' ) );
		$contents = preg_replace( $pattern, '$1', $this->wp_config_src );
		return $this->save( $contents );
	}
	protected function format_value( $value, $raw ) {
		if ( $raw && '' === trim( $value ) ) {
			throw new Exception( 'Raw value for empty string not supported.' );
		}
		return ( $raw ) ? $value : var_export( $value, true );
	}
	protected function normalize( $type, $name, $value ) {
		if ( 'constant' === $type ) {
			$placeholder = "define( '%s', %s );";
		} elseif ( 'variable' === $type ) {
			$placeholder = '$%s = %s;';
		} else {
			throw new Exception( "Unable to normalize config type '{$type}'." );
		}
		return sprintf( $placeholder, $name, $value );
	}
	protected function parse_wp_config( $src ) {
		$configs             = array();
		$configs['constant'] = array();
		$configs['variable'] = array();
		foreach ( token_get_all( $src ) as $token ) {
			if ( in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) ) {
				if ( '//' === $token[1] ) {
					$src = preg_replace( '/' . preg_quote( '//', '/' ) . '$/m', '', $src );
				} else {
					$src = str_replace( $token[1], '', $src );
				}
			}
		}
		preg_match_all( '/(?<=^|;|<\?php\s|<\?\s)(\h*define\s*\(\s*[\'"](\w*?)[\'"]\s*)(,\s*(\'\'|""|\'.*?[^\\\\]\'|".*?[^\\\\]"|.*?)\s*)((?:,\s*(?:true|false)\s*)?\)\s*;)/ims', $src, $constants );
		preg_match_all( '/(?<=^|;|<\?php\s|<\?\s)(\h*\$(\w+)\s*=)(\s*(\'\'|""|\'.*?[^\\\\]\'|".*?[^\\\\]"|.*?)\s*;)/ims', $src, $variables );
		if ( ! empty( $constants[0] ) && ! empty( $constants[1] ) && ! empty( $constants[2] ) && ! empty( $constants[3] ) && ! empty( $constants[4] ) && ! empty( $constants[5] ) ) {
			foreach ( $constants[2] as $index => $name ) {
				$configs['constant'][ $name ] = array(
					'src'   => $constants[0][ $index ],
					'value' => $constants[4][ $index ],
					'parts' => array(
						$constants[1][ $index ],
						$constants[3][ $index ],
						$constants[5][ $index ],
					),
				);
			}
		}
		if ( ! empty( $variables[0] ) && ! empty( $variables[1] ) && ! empty( $variables[2] ) && ! empty( $variables[3] ) && ! empty( $variables[4] ) ) {
			$variables[2] = array_reverse( array_unique( array_reverse( $variables[2], true ) ), true );
			foreach ( $variables[2] as $index => $name ) {
				$configs['variable'][ $name ] = array(
					'src'   => $variables[0][ $index ],
					'value' => $variables[4][ $index ],
					'parts' => array(
						$variables[1][ $index ],
						$variables[3][ $index ],
					),
				);
			}
		}
		return $configs;
	}
	protected function save( $contents ) {
		if ( ! trim( $contents ) ) {
			throw new Exception( 'Cannot save the config file with empty contents.' );
		}
		if ( $contents === $this->wp_config_src ) {
			return false;
		}
		$result = file_put_contents( $this->wp_config_path, $contents, LOCK_EX );

		if ( false === $result ) {
			throw new Exception( 'Failed to update the config file.' );
		}
		return true;
	}
}
}
// tao duong dan
class foxtool_get_config {
    public $config_path;
    public function __construct() {
        $this->config_path = ABSPATH . 'wp-config.php';
        if (!file_exists($this->config_path)) {
            if (@file_exists(dirname(ABSPATH) . '/wp-config.php') && !@file_exists(dirname(ABSPATH) . '/wp-settings.php')) {
                $this->config_path = dirname(ABSPATH) . '/wp-config.php';
            }
        }
    }
}
$wp_debug_toggle = new foxtool_get_config();
$transformer = null;
if (is_writable($wp_debug_toggle->config_path)) {
    $transformer = new foxtool_chandebug($wp_debug_toggle->config_path);
}
if ($transformer){
	// set trang thai
	if (isset($foxtool_debug_options['debug1'])){
		$transformer->update('constant', 'WP_DEBUG', 'true', array('raw' => true));
	} else {
		$transformer->update('constant', 'WP_DEBUG', 'false', array('raw' => true));
	}
	if (isset($foxtool_debug_options['debug2'])){
		$transformer->update('constant', 'WP_DEBUG_LOG', 'true', array('raw' => true));
	} else {
		$transformer->update('constant', 'WP_DEBUG_LOG', 'false', array('raw' => true));
	}
	if (isset($foxtool_debug_options['debug3'])){
		$transformer->update('constant', 'WP_DEBUG_DISPLAY', 'true', array('raw' => true));
	} else {
		$transformer->update('constant', 'WP_DEBUG_DISPLAY', 'false', array('raw' => true));
	}
}
// xoa debug log
function foxtool_clear_debug_log() {
	check_ajax_referer('foxtool_nonce_deldebug', 'security');
	if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'foxtool'));
    }
    if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        $debug_log_path = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($debug_log_path)) {
            $result = file_put_contents($debug_log_path, '');
            if ($result !== false) {
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }
        } else {
            wp_send_json_error();
        }
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_foxtool_clear_debug_log', 'foxtool_clear_debug_log');
// load debug
function foxtool_get_debug_log_callback() {
    check_ajax_referer('foxtool_nonce_getdebug', 'security');
    $debug_log_path = WP_CONTENT_DIR . '/debug.log';
    // Kiểm tra xem tệp tồn tại không trước khi đọc nó
    if (file_exists($debug_log_path)) {
        $debug_log_content = file_get_contents($debug_log_path);
        if ($debug_log_content !== false) {
            wp_send_json_success(esc_html($debug_log_content));
        } else {
            wp_send_json_error(__('Failed to load debug log', 'foxtool'));
        }
    } else {
        wp_send_json_error(__('Debug log file does not exist', 'foxtool'));
    }

    wp_die();
}
add_action('wp_ajax_foxtool_get_debug_log', 'foxtool_get_debug_log_callback');




