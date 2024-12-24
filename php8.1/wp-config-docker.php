<?php
/**
 * The base configuration for ClassicPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://docs.classicpress.net/user-guides/editing-wp-config-php/
 *
 * @package ClassicPress
 */

// a helper function to lookup "env_FILE", "env", then fallback
if ( ! function_exists( 'getenv_docker' ) ) {

	// https://github.com/docker-library/wordpress/issues/588 (WP-CLI will load this file 2x)
	function getenv_docker( $env, $default ) {

		if ( $fileEnv = getenv( $env . '_FILE' ) ) {
			
			return rtrim( file_get_contents ($fileEnv ), "\r\n" );
		} else if ( ( $val = getenv( $env ) ) !== false ) {
			
			return $val;
		} else {

			return $default;
		}
	}
}

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for ClassicPress */
define( 'DB_NAME', getenv_docker( 'CLASSICPRESS_DB_NAME', 'classicpress' ) );

/** Database username */
define( 'DB_USER', getenv_docker( 'CLASSICPRESS_DB_USER', 'username' ) );

/** Database password */
define( 'DB_PASSWORD', getenv_docker( 'CLASSICPRESS_DB_PASSWORD', 'password' ) );

/** Database hostname */
define( 'DB_HOST', getenv_docker( 'CLASSICPRESS_DB_HOST', 'mysql' ) );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', getenv_docker( 'CLASSICPRESS_DB_CHARSET', 'utf8' ) );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', getenv_docker( 'CLASSICPRESS_DB_COLLATE', '' ) );

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
define( 'AUTH_KEY',         getenv_docker( 'CLASSICPRESS_AUTH_KEY',         'put your unique phrase here' ) );
define( 'SECURE_AUTH_KEY',  getenv_docker( 'CLASSICPRESS_SECURE_AUTH_KEY',  'put your unique phrase here' ) );
define( 'LOGGED_IN_KEY',    getenv_docker( 'CLASSICPRESS_LOGGED_IN_KEY',    'put your unique phrase here' ) );
define( 'NONCE_KEY',        getenv_docker( 'CLASSICPRESS_NONCE_KEY',        'put your unique phrase here' ) );
define( 'AUTH_SALT',        getenv_docker( 'CLASSICPRESS_AUTH_SALT',        'put your unique phrase here' ) );
define( 'SECURE_AUTH_SALT', getenv_docker( 'CLASSICPRESS_SECURE_AUTH_SALT', 'put your unique phrase here' ) );
define( 'LOGGED_IN_SALT',   getenv_docker( 'CLASSICPRESS_LOGGED_IN_SALT',   'put your unique phrase here' ) );
define( 'NONCE_SALT',       getenv_docker( 'CLASSICPRESS_NONCE_SALT',       'put your unique phrase here' ) );

/**#@-*/

/**
 * ClassicPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv_docker( 'CLASSICPRESS_TABLE_PREFIX', 'cp_' );

/**
 * ClassicPress File Editor
 * 
 * The File Editor for plugins and themes is disabled in new installations.
 * If you want to enable the File Editor, simply remove the line below or
 * set it to "false".
 * 
 * @link https://docs.classicpress.net/user-guides/using-classicpress/editing-files/
 * 
 * @since CP-2.0.0
 */
define( 'DISALLOW_FILE_EDIT', true );

/**
 * For developers: ClassicPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://docs.classicpress.net/user-guides/debugging-in-classicpress/
 */
define( 'WP_DEBUG', !!getenv_docker( 'CLASSICPRESS_CP_DEBUG', '' ) );

/* Add any custom values between this line and the "stop editing" line. */

// If we're behind a proxy server and using HTTPS, we need to alert WordPress of that fact
// see also https://wordpress.org/support/article/administration-over-ssl/#using-a-reverse-proxy
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && strpos( $_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false ) {
	
	$_SERVER['HTTPS'] = 'on';
}

// (we include this by default because reverse proxying is extremely common in container environments)
if ( $configExtra = getenv_docker( 'CLASSICPRESS_CONFIG_EXTRA', '' ) ) {

	eval($configExtra);
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';