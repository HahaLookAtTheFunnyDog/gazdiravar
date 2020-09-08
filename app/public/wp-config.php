<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'L4Nzxb5MIPf6n3Bf0CaTaBBz22SZOh6mjBGsRs5z21lqfEf0PDndxJqWOgdY7irx87wRM7PIAwawQn9WumEvFA==');
define('SECURE_AUTH_KEY',  'iMJOswqOn+RDDxc1FaXNnbAc/UwXi0hrjZZuCJ7qUeMd5UK6j+woFW+pIow36vkMzI4E84bTT4N59W73YWJMnQ==');
define('LOGGED_IN_KEY',    'C7w9Ll2YlYHL3HCHVKiPJvCRFPDZom9yeMofFhEbTcX7vd6uQE+QhxsfhWoOsVWEHQc3Wg9awlDroqxy5zYZ1A==');
define('NONCE_KEY',        '8JlM++KO05YEl/9igwirCd1lD/G24rQxtS6xZ0noD2ZIhzUSEyAnotvNvxyJ8+uLC3oeYhU3bvLWKDSXAHNNYA==');
define('AUTH_SALT',        'FJQJQ8QpNg1tqi7lRsbVB/iVGAkPWmBocapxw0MiOaIvvnwbOK5Q2HE/CgS/mde/LpNaYf2FB9OfXWtc+K7kag==');
define('SECURE_AUTH_SALT', '8+JoWy3ijY9Ab4MOBhazCuVibJvETVXxyG06ViIbGcs9BDrJF76KwK+Ri9at4jM2wul46Hl0ZMpvOgLGoaqsCw==');
define('LOGGED_IN_SALT',   'q7HDR6vsggv5/YTWFmc+40OIr0ci2FA9ob1YHAC8SC5SOWCeEChnwOydoU0767rquUfArEpQGar33ChFCtqGPw==');
define('NONCE_SALT',       'IEQm7WPp/rfiCbbGcMfZmYSUp56+RoXROguG0OVyXZVZdW72dc+qoZvFjwCCG9+Tckj9FMv2XD7dHdHi2PLKuQ==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
