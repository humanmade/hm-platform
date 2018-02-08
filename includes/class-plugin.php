<?php
/**
 * Plugin bootstrap object.
 *
 * @package hm-platform
 */

namespace HM\Platform;

class Plugin {

	/**
	 * A unique identifier for the plugin.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The file to load.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Free form associated plugin data.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * If not null the function used to load the plugin file.
	 *
	 * @var null|callable
	 */
	protected $load_with;

	/**
	 * An action string, boolean or callable that returns a boolean.
	 * Used to determine *when* to load the plugin.
	 *
	 * @var string|bool|callable
	 */
	protected $load_on = 'muplugins_loaded';

	/**
	 * Passed to the $load_on add_action call.
	 *
	 * @var int
	 */
	protected $load_priority = 10;

	/**
	 * Passed to the $load_on add_action call.
	 *
	 * @var int
	 */
	protected $load_args = 0;

	/**
	 * Whether the plugin is enabled or not. If this is an array
	 * it is evaluated for matching URLs. Wildcard * can be used when matching.
	 *
	 * If the key 'only' is found then the plugin is only loaded on URLs within
	 * that array.
	 *
	 * If the key 'except' is found then the plugin is loaded on all sites except
	 * the mastching ones.
	 *
	 * @var bool|array
	 */
	protected $enabled = false;

	/**
	 * A configuration used when processing the registered settings.
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * An array of keys and callbacks to handle the value assigned to that
	 * key in the $config.
	 *
	 * @var array
	 */
	protected $settings = [];

	/**
	 * Extra files to load before the main file.
	 *
	 * @var array
	 */
	protected $prepended = [];

	/**
	 * Extra files to load after the main file.
	 *
	 * @var array
	 */
	protected $appended = [];

	/**
	 * Whether the plugin has been loaded yet.
	 *
	 * @var bool
	 */
	protected $loaded = false;

	/**
	 * A store of the registered plugins. Accessible via $name as the key.
	 *
	 * @var array
	 */
	public static $plugins = [];

	/**
	 * Adds a plugin to the list. Will load $file on muplugins_loaded if no
	 * other methods are called.
	 *
	 * @param string $name An identifier for the plugin.
	 * @param string $file The file to load.
	 * @return Plugin
	 */
	public static function register( string $name, string $file ) {
		self::$plugins[ $name ] = new self( $name, $file );

		return self::$plugins[ $name ];
	}

	/**
	 * Plugin constructor.
	 *
	 * @param string $name The identifier for the plugin.
	 * @param string $file The file to load.
	 */
	public function __construct( string $name, string $file ) {
		$file = ROOT_DIR . '/' . ltrim( $file, '/' );

		if ( ! is_readable( $file ) ) {
			die( 'Could not find ' . $file . ' in HM Platform ' . ROOT_DIR );
		}

		$this->name = $name;
		$this->file = $file;
	}

	/**
	 * Toggle the enabled state of the plugin. Any truthy value
	 * will enable it, false will disable it.
	 *
	 * @param bool|array $enable A boolean or an array of URLs to enable the site for.
	 * @return $this
	 */
	public function enabled( $enable ) {
		if ( ! empty( $enable ) && is_array( $enable ) ) {
			$this->enable_by_site( $enable, true );
		} else {
			$this->enabled = (bool) $enable;
		}

		return $this;
	}

	/**
	 * Get the enabled state.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return (bool) $this->enabled;
	}

	/**
	 * Set the freeform plugin data. If a callable is used it will
	 * be run on wp_loaded to provide access to WP functions and
	 * must return an array.
	 *
	 * @param array|callable $data
	 * @param bool           $merge Whether to merge the data with existing or overwrite it. Defaults to true.
	 * @return $this
	 */
	public function set_data( $data, $merge = true ) {
		// Defer setting data via a callback so WP functions eg. __() are available.
		if ( is_callable( $data ) ) {
			add_action( 'wp_loaded', function () use ( $data, $merge ) {
				$this->set_data( call_user_func( $data, $this ), $merge );
			} );
		} // Simple update.
		else {
			if ( $merge ) {
				$data = array_merge( $this->data, (array) $data );
			}

			$this->data = $data;
		}

		return $this;
	}

	/**
	 * Returns the freeform data array.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Set a configuration for the plugin. Used when processing
	 * registered settings.
	 *
	 * @param array $config An associative array of key value pairs.
	 * @param bool  $merge  Whether to merge the array with the existing config. Defaults to true.
	 * @return $this
	 */
	public function set_config( array $config, $merge = true ) {
		if ( $merge ) {
			$config = array_merge( $this->config, $config );
		}

		$this->config = $config;

		return $this;
	}

	/**
	 * Gets the config array.
	 *
	 * @return array
	 */
	public function get_config() {
		return $this->config;
	}

	/**
	 * Gets the path to the main plugin file.
	 *
	 * @return string
	 */
	public function get_file() {
		return $this->file;
	}

	/**
	 * Adds a setting and handler. Handlers are called for enabled plugins with
	 * the default value or the corresponding value from $config passed in.
	 *
	 * Use these to define constants, add pre_option filters or any other
	 * customisations you want to handle through the config file.
	 *
	 * @param string   $name     The setting key.
	 * @param callable $callback A callback to handle the setting value.
	 * @param mixed    $default  A default value for the callback. Defaults to null.
	 * @return $this
	 */
	public function register_setting( string $name, callable $callback, $default = null ) {
		$this->settings[ $name ] = [ 'callback' => $callback, 'default' => $default ];

		return $this;
	}

	/**
	 * Executes the settings handlers.
	 *
	 * @return $this
	 */
	public function do_settings() {
		if ( ! $this->is_enabled() ) {
			return $this;
		}

		$config = $this->get_config();
		foreach ( $this->settings as $name => $setting ) {
			if ( isset( $config['settings'], $config['settings'][ $name ] ) ) {
				$value = $config['settings'][ $name ];
			} else {
				$value = $setting['default'];
			}

			// Run the setting callback.
			call_user_func_array( $setting['callback'], [ $value, $this ] );
		}

		return $this;
	}

	/**
	 * Adds a file to be loaded before the main plugin file.
	 *
	 * @param string $file Full file path.
	 * @return $this
	 */
	public function prepend_file( string $file ) {
		$this->prepended[] = $file;

		return $this;
	}

	/**
	 * Adds a file to be loaded after the main plugin file.
	 *
	 * @param string $file Full file path.
	 * @return $this
	 */
	public function append_file( string $file ) {
		$this->appended[] = $file;

		return $this;
	}

	/**
	 * Determines when to load the plugin.
	 *
	 * If it's a string it's treated as an action. The priority and args count
	 * are passed to add_action().
	 *
	 * If it's a callable the function is evaluated and if its return value is
	 * true it will load the plugin.
	 *
	 * If it's a boolean and true the plugin file will be loaded immediately.
	 *
	 * @param string|callable|bool $action_or_callable Action to run on or callback to evaluate.
	 * @param int                  $priority           Action priority
	 * @param int                  $args               Action args count.
	 * @return $this
	 */
	public function load_on( $action_or_callable, $priority = 10, $args = 0 ) {
		$this->load_on       = $action_or_callable;
		$this->load_priority = $priority;
		$this->load_args     = $args;

		return $this;
	}

	/**
	 * Override the default plugin loading function, this will be called
	 * when $load_on is triggered.
	 *
	 * Use this to add any additional logic or steps you need to bootstrap
	 * the plugin.
	 *
	 * @param callable $callable Function used to load the plugin. Receives $this as first parameter.
	 * @return $this
	 */
	public function load_with( callable $callable ) {
		$this->load_with = $callable;

		return $this;
	}

	/**
	 * Whether the plugin has been loaded yet.
	 *
	 * @return bool
	 */
	public function has_loaded() {
		return $this->loaded;
	}

	/**
	 * Loads the plugin if it's enabled.
	 *
	 * @return $this
	 */
	public function load() {
		if ( ! $this->is_enabled() || $this->has_loaded() ) {
			return $this;
		}

		// Load with default loader.
		if ( ! $this->load_with ) {
			$loader = function () {
				foreach ( $this->prepended as $file ) {
					if ( is_readable( $file ) ) {
						require_once $file;
					}
				}
				require_once $this->get_file();
				foreach ( $this->appended as $file ) {
					if ( is_readable( $file ) ) {
						require_once $file;
					}
				}
			};
		} // Load with custom loader.
		else {
			$loader = function () {
				call_user_func_array( $this->load_with, [ $this ] );
			};
		}

		// Load on action.
		if ( is_string( $this->load_on ) ) {
			add_action( $this->load_on, $loader, $this->load_priority, $this->load_args );
		} // Load on callback if returns true.
		elseif ( is_callable( $this->load_on ) ) {
			if ( call_user_func_array( $this->load_on, [ $this ] ) ) {
				$loader();
			}
		} // Load immediately if truthy.
		elseif ( $this->load_on ) {
			$loader();
		}

		$this->loaded = true;

		return $this;
	}

	/**
	 * Handles enabling or disabling the plugin if matches the current site URL.
	 *
	 * @param array $sites
	 * @param bool  $enable
	 */
	protected function enable_by_site( $sites, $enable = true ) {
		$current_site = $_SERVER['HTTP_HOST'] . '/' . ltrim( $_SERVER['REQUEST_URI'], '/' );
		foreach ( $sites as $pattern ) {
			if ( preg_match( '#^' . str_replace( '\*', '.+', preg_quote( $pattern, '#' ) ) . '#', $current_site ) ) {
				$this->enabled = $enable;
			}
		}
	}

}
