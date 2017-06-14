 <?php

define('FS_METHOD', 'direct');

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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db682007952' );

/** MySQL database username */
define( 'DB_USER', 'dbo682007952' );

/** MySQL database password */
define( 'DB_PASSWORD', 'kLBOqZMsuisbjNsQiNrM' );

/** MySQL hostname */
define( 'DB_HOST', 'db682007952.db.1and1.com' );

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
define('AUTH_KEY',         'l@&S$|aI5|A#]@~ 5Wr-]pF9J.mt;#myLUD;z6AMU9OMXm{ MgIu-m]sYgaC(c<9');
define('SECURE_AUTH_KEY',  'ly|X6X]_Tclcz2+s=fkI`C/=)XYu:[-m}4v9q?u/hN|W`+lcRLVfoUpK8xGN;Ey;');
define('LOGGED_IN_KEY',    '&ORUS&.mv<oLLZ09&g~Xcjs3f71w}6Ui@a+CM3}_`cF|8:z*]a?!=*FmHS>x>c_p');
define('NONCE_KEY',        '+J:+.b7p#1Yd7_EX%eywaqjD0_c[&=&1[UJKe/rcg/uqBq16qd3+5]R*W@{u,gQ?');
define('AUTH_SALT',        '@t$BI<8faW]yq9v|&D+Kra>VD`q _gU_>x(P:_c@+Y!vp.raa2+yr%y)$~f+qsLW');
define('SECURE_AUTH_SALT', '=pO-o)ib4m+X&z)4-#K uJ4Pjwu#cwj94{l-[)P;Iuilr,DwG-x_r*^TCRV)@~Ln');
define('LOGGED_IN_SALT',   '4TN+FR-AO.paGW MoXLV+<1hREhk?6WmQ@EhfnMp(*{%^8CMa-:](BM6[~K-NlKV');
define('NONCE_SALT',       'E}$K(dc!4~@dqwtzGeMnQXd>0<~V7{}.40+rB|.:lPNU-d*=)rI-dTs-U+.D}>M$');


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'LZZacLpA';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
