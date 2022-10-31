<?php
error_reporting(0);

/** Enable W3 Total Cache ---- */
 // Added by W3 Total Cache





 // Added by WP Rocket




/**

 * The base configuration for WordPress

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

 * @link https://wordpress.org/support/article/editing-wp-config-php/

 *

 * @package WordPress

 */


// ** Database settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define( 'DB_NAME', 'alshia5_woocommerce' );


/** Database username */

define( 'DB_USER', 'alshia5_user' );


/** Database password */

define( 'DB_PASSWORD', '[RqF$,W$V==0' );


/** Database hostname */

define( 'DB_HOST', 'localhost' );


/** Database charset to use in creating database tables. */

define( 'DB_CHARSET', 'utf8mb4' );


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

define( 'AUTH_KEY',         '<,I!i$7~~C5Y!5h-XfAnILnQR4wQ<69i0Pj5LwNig3+Dc8f,+bej{^7g7&c`+%d3' );

define( 'SECURE_AUTH_KEY',  '}(5Lku2kCj,:>@~f~$e<_`)HFcH4()To@7@C4e{_M8Nlz}WD?4&P`[3P]<tBF9iM' );

define( 'LOGGED_IN_KEY',    'B%=<uKx=h]zi5:T[_k? 6uvtmk&]yDe.)Vw(~<43=R&+dg.s5zXYSW$$8o=g:nHO' );

define( 'NONCE_KEY',        'j4WJ7ZShl|.0O0mp:Vs1wW=LZ~Z{xD7G)z*ZU9My{pNt!3[2;+2M;R7k_G-|4A>.' );

define( 'AUTH_SALT',        '}Wp4f?uY9{jHWYIK;3H8^PBtKlwu+&:0S,ESD8W(^z8[CLFl>qA/aV=BjghwdA6^' );

define( 'SECURE_AUTH_SALT', 'Ex0{(V<Ldu4qf;DW(w/nSB>5/l`N $pEKTH+#iFi&0~6q!k/?z{I}J_[2kkB*Y@~' );

define( 'LOGGED_IN_SALT',   'xf1g_QIIqbCrR.Xd7Duv}po BoH7%/,z*kVbVo@oxnr`:T#K)ccU}us)`AJ*,]_.' );

define( 'NONCE_SALT',       'Uhau->w_H{}W#uiz_.s-ru.-K#jJ;{]Rx<wa~3<fqNi*p*.4 .QWNUiw#*DSIrCR' );


define('JWT_AUTH_SECRET_KEY', 'secret');
define('JWT_AUTH_CORS_ENABLE', true);


/**#@-*/


/**

 * WordPress database table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix = 'wp_';


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

 * @link https://wordpress.org/support/article/debugging-in-wordpress/

 */

ini_set('display_errors','Off');
ini_set('error_reporting', 0 );
define('WP_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);


//  error_reporting(E_ERROR );

define('WP_DEBUG_LOG', false);

 

/* Add any custom values between this line and the "stop editing" line. */




/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}


/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';

