<?php
/**
 * Simple Grams.
 *
 * @package   Simple_Grams
 * @author    Constantine Kiriaze <hello@kiriaze.com>
 * @license   GPL-2.0+
 * @link      http://getsimple.io
 * @copyright 2013 Simple
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package Simple_Grams
 * @author  Constantine Kiriaze <hello@kiriaze.com>
 */

class Simple_Grams {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * @TODO - Rename "plugin-name" to the name your your plugin
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'simple-grams';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		add_shortcode('grams', array( &$this, 'shortcode') );
		add_action('wp_enqueue_scripts', array( &$this, 'enqueue_scripts') );

		// widget
		$this->widget = new Simple_Grams_Widget();
		add_action( 'widgets_init', array( &$this, 'simple_grams' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	}

	/**
	 * Curl
	 *
	 * @since    1.0.0
	 */
	public function get_curl($url) {
	    if( function_exists('curl_init') ) {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL,$url);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        $output = curl_exec($ch);
	        echo curl_error($ch);
	        curl_close($ch);
	        return $output;
	    }else{
	        return file_get_contents($url);
	    }

	}

	public function shortcode( $atts ) {
		extract( shortcode_atts( array(
			'account' 	=>	'ckiriaze',
			'count' 	=>	4,
			'tag'		=>	'',
			'class'		=>	'simple_grams',
			'effects'	=>	''
		), $atts ) );

		return $this->do_simple_grams( esc_attr($account), esc_attr($count), esc_attr($tag), esc_attr($class), esc_attr($effects) );
	}

	public function do_simple_grams( $account, $count = 4, $tag = '', $class = 'simple_grams', $effects = '' ) {

		$accessToken = '24765686.f4c7bbf.e2dd3e076b0e4fff992ec9de1898acb2'; // wordpress.dev
		// ask for CLIENT-ID & REDIRECT-URI
		// https://instagram.com/oauth/authorize/?client_id=CLIENT-ID&redirect_uri=REDIRECT-URI&response_type=token

		$userData = 'https://api.instagram.com/v1/users/search?q='.$account.'&access_token='.$accessToken;

		if ( $userData !== false )
		$jsonUserData = json_decode( ( @file_get_contents($userData) ) );
		// sp($jsonUserData);
		if ( $jsonUserData ) :
			foreach ( $jsonUserData->data as $userKey=>$userValue ) :
				// sp($userValue->id);
				$userID = $userValue->id;
			endforeach;
		else :
			$userID = '';
		endif;

		if ( $tag ) {
			$url = 'https://api.instagram.com/v1/tags/'.$tag.'/media/recent?access_token=' . $accessToken . '&count=' . $count;
		} else{
			$url = 'https://api.instagram.com/v1/users/'.$userID.'/media/recent/?access_token=' . $accessToken . '&count=' . $count;
		}

		$cache = dirname(__FILE__) . '/cache.json';

		if( file_exists($cache) && filemtime($cache) > time() - 60*60 ) {
		    // If a cache file exists, and it is newer than 1 hour, use it
		    $images = json_decode( file_get_contents($cache), true ); //Decode as an json array
		} else{
		    // Make an API request and create the cache file
		    // For example, gets the 32 most popular images on Instagram
		    $response = $this->get_curl($url); //change request path to pull different photos

		    $images = array();

		    if( $response ) {

		    	$data = json_decode($response)->data;

		        // Decode the response and build an array
		        foreach( $data as $item ) {

		            $title = ( isset( $item->caption ) ) ? mb_substr( $item->caption->text, 0, 70, "utf8" ) : null;

		            $src = $item->images->standard_resolution->url; //Caches standard res img path to variable $src

		            //Location coords seemed empty in the results but you would need to check them as mostly be undefined
		            $lat = ( isset( $item->data->location->latitude ) ) ? $item->data->location->latitude : null; // Caches latitude as $lat
		            $lon = ( isset( $item->data->location->longtitude ) ) ? $item->data->location->longtitude : null; // Caches longitude as $lon

		            $images[] = array(
			            "title" => htmlspecialchars($title),
			            "src" 	=> htmlspecialchars($src),
			            "lat" 	=> htmlspecialchars($lat),
			            "lon" 	=> htmlspecialchars($lon) // Consolidates variables to an array
		            );
		        }
		        file_put_contents( $cache, json_encode($images) ); //Save as json
		    }
		}

		// Debug out
		// print_r($images);

		$class = isset($class) ? ' class="'.$class.'"' : null;
		$effects = isset($effects) ? ' class="'.$effects.'"' : null;
		$output = '<ul'.$class.'>';

		foreach( $images as $image ) {

			$output .= '<li class="simple-gram">';
			$output .= '<a title="" href="' . $image['src'] . '"'. $effects .'>'; // link to large photo
			$output .= '<img src=" ' . $image['src'] . ' " alt="" />';
			$output .= '</a>';
			$output .= '</li>';

		}

		$output .= '</ul>';

		return $output;

	}

	public function enqueue_scripts() {
		// wp_enqueue_style( 'simple-grams-admin', plugins_url( '/assets/css/simple-grams-admin.css', __FILE__ ) );
		// wp_enqueue_style( 'simple-grams', plugins_url( '/assets/css/simple-grams.css', __FILE__ ) );
	}

	// REGISTER WIDGET
	public function simple_grams() {
		register_widget( 'Simple_Grams_Widget' );
	}

}

// WIDGET CLASS
class Simple_Grams_Widget extends WP_Widget {

	/*--------------------------------------------------------------------*/
	/*	WIDGET SETUP
	/*--------------------------------------------------------------------*/
	public function __construct() {
		parent::__construct(
	 		'simple_grams', // BASE ID
			'SimpleGrams', // NAME
			array( 'description' => __( 'A widget that displays your Instagrams', 'simple' ), )
		);
	}


	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @args			The array of form elements
	 * @instance		The current instance of the widget
	 */

	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );
	    // WIDGET VARIABLES
		extract( $args );

		$title 		= apply_filters( 'widget_title', $instance['title'] );
		$account 	= $instance['account'];
		$count 		= $instance['count'];

		echo $before_widget;

		if ( !empty( $title ) ) echo $before_title . $title . $after_title;

		$plugin = Simple_Grams::get_instance();
		echo $plugin->do_simple_grams( esc_attr($account), esc_attr($count) );

		echo $after_widget;

	} // END WIDGET

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @new_instance	The previous instance of values before the update.
	 * @old_instance	The new instance of values to be generated via the update.
	 */

	function update( $new_instance, $old_instance ) {

		// STRIP TAGS TO REMOVE HTML - IMPORTANT FOR TEXT IMPUTS
		$instance 				= $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['account'] 	= trim($new_instance['account']);
		$instance['count'] 		= trim($new_instance['count']);
		$instance['cache'] 		= trim($new_instance['cache']);

		return $instance;

	}

	/**
	 * GENERATES THE ADMIN FORM FOR THE WIDGET
	 * @instance
	 */

	function form( $instance ) {

		// WIDGET DEFAULTS
		$defaults = array(
			'title' 	=> 'Instagram Widget',
			'account' 	=> 'ckiriaze',
			'count' 	=> 4,
			'cache'		=> 15
		);

		$instance 	= wp_parse_args( (array) $instance, $defaults );
		$title 		= $instance['title'];
		$account 	= $instance['account'];
		$count 		= $instance['count'];
		$cache 		= $instance['cache'];

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'simple'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('account'); ?>"><?php _e('<a href="http://www.dribbble.com/constantine">Instagram</a> account:', 'simple'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('account'); ?>" name="<?php echo $this->get_field_name('account'); ?>" type="text" value="<?php echo $account; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of Grams:', 'simple'); ?></label>
			<select name="<?php echo $this->get_field_name('count'); ?>">
				<?php for( $i = 1; $i <= 12; $i++ ) { ?>
					<option value="<?php echo $i; ?>" <?php selected( $i, $count ); ?>><?php echo $i; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Cache:', 'simple'); ?> (Coming Soon!)</label>
			<input class="widefat" id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" type="text" value="<?php echo $cache; ?>" />
		</p>

	<?php

	} // END FORM

} // END CLASS