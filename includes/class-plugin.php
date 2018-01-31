<?php
/**
 * Plugin bootstrap object.
 */

namespace HM\Platform;

class Plugin {

	protected $name;
	protected $file;
	protected $data          = [];
	protected $load_with;
	protected $load_on       = 'muplugins_loaded';
	protected $load_priority = 10;
	protected $load_args     = 0;
	protected $enabled       = false;
	protected $config        = [];
	protected $settings      = [];
	protected $dependencies  = [];
	protected $prepended     = [];
	protected $appended      = [];

	public static $plugins = [];

	public static function register( string $name, string $file ) {
		self::$plugins[ $name ] = new self( $name, $file );

		return self::$plugins[ $name ];
	}

	public function __construct( string $name, string $file ) {
		$file = ROOT_DIR . '/' . ltrim( $file, '/' );

		if ( ! is_readable( $file ) ) {
			die( 'Could not find ' . $file . ' in HM Platform ' . ROOT_DIR );
		}

		$this->name = $name;
		$this->file = $file;
	}

	public function enabled( $enable ) {
		if ( ! empty( $enable ) && is_array( $enable ) ) {
			$sites  = $enable;
			$enable = true;

			// Handle only.
			if ( isset( $sites['only'] ) ) {
				$sites = $sites['only'];
			}
			// Handle except.
			if ( isset( $sites['except'] ) ) {
				$sites  = $sites['except'];
				$enable = false;
			}

			$this->enable_by_site( $sites, $enable );
		} else {
			$this->enabled = (bool) $enable;
		}

		return $this;
	}

	public function is_enabled() {
		return $this->enabled;
	}

	public function set_data( $data, $merge = true ) {
		// Defer setting data via a callback so WP functions eg. __() are available.
		if ( is_callable( $data ) ) {
			add_action( 'wp_loaded', function () use ( $data, $merge ) {
				$this->set_data( call_user_func( $data, $this ), $merge );
			} );
		}
		// Simple update.
		else {
			if ( $merge ) {
				$data = array_merge( $this->data, (array) $data );
			}

			$this->data = $data;
		}

		return $this;
	}

	public function get_data() {
		return $this->data;
	}

	public function set_config( array $config, $merge = true ) {
		if ( $merge ) {
			$config = array_merge( $this->config, $config );
		}

		$this->config = $config;

		return $this;
	}

	public function get_config() {
		return $this->config;
	}

	public function get_file() {
		return $this->file;
	}

	public function register_setting( string $name, callable $callback, $default = null ) {
		$this->settings[ $name ] = [ 'callback' => $callback, 'default' => $default ];

		return $this;
	}

	public function add_dependency( string $name ) {
		$this->dependencies[] = $name;

		return $this;
	}

	public function get_dependencies() {
		return $this->dependencies;
	}

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

	public function prepend_file( string $file ) {
		$this->prepended[] = $file;

		return $this;
	}

	public function append_file( string $file ) {
		$this->appended[] = $file;

		return $this;
	}

	public function load_on( $action_or_callable, $priority = 10, $args = 0 ) {
		$this->load_on       = $action_or_callable;
		$this->load_priority = $priority;
		$this->load_args     = $args;

		return $this;
	}

	public function load_with( callable $callable ) {
		$this->load_with = $callable;

		return $this;
	}

	public function load() {
		if ( ! $this->is_enabled() ) {
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
		}
		// Load with custom loader.
		else {
			$loader = function () {
				call_user_func_array( $this->load_with, [ $this ] );
			};
		}

		// Load on action.
		if ( is_string( $this->load_on ) ) {
			add_action( $this->load_on, $loader, $this->load_priority, $this->load_args );
		}
		// Load on callback if returns true.
		elseif ( is_callable( $this->load_on ) ) {
			if ( call_user_func_array( $this->load_on, [ $this ] ) ) {
				$loader();
			}
		}
		// Load immediately if truthy.
		elseif ( $this->load_on ) {
			$loader();
		}

		return $this;
	}

	protected function enable_by_site( $sites, $enable = true ) {
		$current_site = $_SERVER['HTTP_HOST'] . '/' . ltrim( $_SERVER['REQUEST_URI'], '/' );
		foreach ( $sites as $pattern ) {
			if ( preg_match( '#' . str_replace( '\*', '.+', preg_quote( $pattern, '#' ) ) . '#', $current_site ) ) {
				$this->enabled = $enable;
			}
		}
	}

}
