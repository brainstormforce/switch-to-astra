<?php
/**
 * Switch to Astra Initial
 *
 * @package Switch to Astra
 */

if ( ! class_exists( 'Switch_To_Astra' ) ) {

	/**
	 * Switch_To_Astra initial
	 *
	 * @since 1.0.0
	 */
	class Switch_To_Astra {

		/**
		 * Switch_To_Astra_Process instance
		 *
		 * @var Switch_To_Astra_Process
		 */
		protected $process_all;

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Switch_To_Astra constructor.
		 */
		public function __construct() {

			add_action( 'admin_notices',                   array( $this, 'add_admin_notice' ) );
			add_action( 'admin_enqueue_scripts',           array( $this, 'admin_scripts' ) );
			add_action( 'wp_ajax_switch_to_astra_updated', array( $this, 'switch_to_astra_updated_callback' ) );
			add_action( 'plugins_loaded',                  array( $this, 'init' ) );
			add_action( 'admin_init',                      array( $this, 'process_handler' ) );
			register_deactivation_hook( SWITCH_TO_ASTRA_FILE, array( $this, 'deactivate' ) );

		}

		/**
		 * Init
		 */
		public function init() {
			require_once SWITCH_TO_ASTRA_DIR . 'lib/class-wp-async-request.php';
			require_once SWITCH_TO_ASTRA_DIR . 'lib/class-wp-background-process.php';
			require_once SWITCH_TO_ASTRA_DIR . 'classes/class-logger.php';
			require_once SWITCH_TO_ASTRA_DIR . 'classes/class-switch-to-astra-process.php';

			$this->process_all    = new Switch_To_Astra_Process();
		}

		/**
		 * Process handler
		 */
		public function process_handler() {

			if ( ! isset( $_GET['switch'] ) || ! isset( $_GET['_wpnonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'switch' ) ) {
				return;
			}

			if ( 'to-astra' === $_GET['switch'] ) {
				$this->handle_all();

				wp_redirect( remove_query_arg( array( 'switch', '_wpnonce' ) ) );
				exit();
			}
		}

		/**
		 * Handle all
		 */
		protected function handle_all() {
			$ids = $this->get_post_ids();

			foreach ( $ids as $id ) {
				$this->process_all->push_to_queue( $id );
			}

			update_option( 'switch-to-astra-flag', 'updating' );
			$this->process_all->save()->dispatch();
		}

		/**
		 * Get ids
		 *
		 * @return array
		 */
		protected function get_post_ids() {

			$post_ids = array();
			// get all post types.
			$all_post_type = get_post_types(
				array(
					'public' => true,
				)
			);
			unset( $all_post_type['attachment'] );

			// wp_query array.
			$query = array(
				'post_type'      => $all_post_type,
				'posts_per_page' => '-1',
				'no_found_rows'  => true,
				'post_status'    => 'any',
				'fields'         => 'ids',
			);

			// exicute wp_query.
			$posts = new WP_Query( $query );

			if ( isset( $posts->posts ) ) {
				$post_ids = $posts->posts;
			}

			wp_reset_query();

			return $post_ids;
		}

		/**
		 * Deactivate Plugin.
		 *
		 * @return void
		 */
		public function deactivate() {
			delete_option( 'switch-to-astra-flag' );
		}

		/**
		 * Admin Notice.
		 *
		 * @return void
		 */
		public function add_admin_notice() {

			$flag = get_option( 'switch-to-astra-flag', 'true' );
			if ( 'updated' == $flag ) { ?>

				<div id="switch-to-astra-notice" class="switch-to-astra-updated updated notice notice-success is-dismissible">
					<p><strong><?php _e( 'Updated', 'switch-to-astra' ); ?></strong> &#8211; <?php _e( 'Updated page layout to full width and disabled page title for all the pages created using <i>Beaver Builder</i> or <i>Visual Composer</i> or <i>Elementor</i>.', 'switch-to-astra' ); ?></p>
				</div>

				<?php
			} elseif ( 'updating' == $flag ) { ?>

				<div id="switch-to-astra-notice" class="switch-to-astra-updating updated notice">
					<p><strong><?php _e( 'Updating', 'switch-to-astra' ); ?></strong> &#8211; <?php _e( 'Migrating page layout to full width and page title meta for all the pages created using <i>Beaver Builder</i> or <i>Visual Composer</i> or <i>Elementor</i>.', 'switch-to-astra' ); ?></p>
				</div>

				<?php
			} elseif ( 'true' === $flag && ( ! isset( $_GET['switch'] ) || 'to-astra' != $_GET['switch'] ) ) { ?>

				<div id="switch-to-astra-notice" class="updated">
					<p><strong><?php _e( 'Switch to Astra', 'switch-to-astra' ); ?></strong> &#8211; <?php _e( 'Set page layout to full width and disable page title for all the pages created using <i>Beaver Builder</i> or <i>Visual Composer</i> or <i>Elementor</i>.', 'switch-to-astra' ); ?></p>
					<p class="submit"><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'switch', 'to-astra' ), 'switch' ) ); ?>" class="switch-to-astra-update-now button-primary"><?php _e( 'Run the updater', 'switch-to-astra' ); ?></a></p>
				</div>

				<?php
			}
		}

		/**
		 * Customizer Preview
		 */
		public function admin_scripts() {
			wp_enqueue_script( 'switch-to-astra', SWITCH_TO_ASTRA_URI . 'assets/js/switch-to-astra.js', array( 'jquery' ), null, true );

			/**
			 * Registered localize vars
			 */
			$localize_vars = array(
				'confirm_message' => esc_js( __( 'Are you sure you wish to run the updater now?', 'switch-to-astra' ) ),
			);
			wp_localize_script( 'switch-to-astra', 'switchToAstra', $localize_vars );
		}

		/**
		 * Customizer Preview
		 */
		public function switch_to_astra_updated_callback() {
			update_option( 'switch-to-astra-flag', 'false' );
		}

	}
}// End if().

/**
 * Kicking this off by calling 'get_instance()' method
 */
Switch_To_Astra::get_instance();
