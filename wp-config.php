<?php
define( 'WP_CACHE', false ); // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mindjour_wp480' );

/** Database username */
define( 'DB_USER', 'mindjour_wp480' );

/** Database password */
define( 'DB_PASSWORD', '2KS!1)pR4M' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'p9c763zxxvjgdzlwbvujgvlijmwmxmzhl4scmrqg3kvjhwapx4swuo7f65ar74lk' );
define( 'SECURE_AUTH_KEY',  'iqau1ojgonjna3lc7dhnqeeo94f1y8skowfknljw3kbnerrtvhfgjcx2zhineui2' );
define( 'LOGGED_IN_KEY',    'on04mukslb1rdd1nreqpzmgj42frvgdzilfl5ysziopxu6kkaqrrb0prssvh08dx' );
define( 'NONCE_KEY',        'eo4glamwq0ic5x7q6wl92piu9ynbzcwq3gs8czp5v9fuynso2apa6fchxvh9qu2m' );
define( 'AUTH_SALT',        '9oqmvm9o2qf4nvgndfaetbillf0omcwpbeujlwgwai1wi78menl563v65dyaakgq' );
define( 'SECURE_AUTH_SALT', 'q5olo00swmcmc757w3kkytldz2775ftmjdvmhtgjoxnorllgpbep68ipwmjvho75' );
define( 'LOGGED_IN_SALT',   'rgpwigzl6b1qaeyyjnwducbg4kbsxsypzcswbyiqj9lweze93h8jalstoumri1iy' );
define( 'NONCE_SALT',       'w3tnyel5hwekrdqvx2wyngnotljyfiaurmpdum6lafjvv4axltqw9jjto9aj75b1' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp36_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



define( 'DISALLOW_FILE_EDIT', true );
define( 'CONCATENATE_SCRIPTS', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
