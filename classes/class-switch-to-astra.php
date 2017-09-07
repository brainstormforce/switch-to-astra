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

			add_action( 'admin_notices',  array( $this, 'add_admin_notice' ) );
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			add_action( 'admin_init',     array( $this, 'process_handler' ) );
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

			update_option( 'switch-to-astra-flag', 'false' );
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
			if ( 'true' === $flag && ( ! isset( $_GET['switch'] ) || 'to-astra' != $_GET['switch'] ) ) {

				?>
				<div id="switch-to-astra-notice" class="updated">
					<p><strong><?php _e( 'Switch to Astra', 'switch-to-astra' ); ?></strong> &#8211; <?php _e( 'Set page layout to full width and disable page title for all the pages created using Beaver Builder or Visual Composer or Elementor.', 'switch-to-astra' ); ?></p>
					<p class="submit"><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'switch', 'to-astra' ), 'switch' ) ); ?>" class="switch-to-astra-update-now button-primary"><?php _e( 'Run the updater', 'switch-to-astra' ); ?></a></p>
				</div>
				<script type="text/javascript">
					document.querySelector( '.switch-to-astra-update-now' ).addEventListener( 'click', function ( event ) {
						var confirm = window.confirm( '<?php echo esc_js( __( 'Are you sure you wish to run the updater now?', 'switch-to-astra' ) ); ?>' ); // jshint ignore:line
						if( ! confirm ) {
							event.preventDefault();
						}
					});
				</script>
				<?php
			}
		}

	}
}// End if().

/**
 * Kicking this off by calling 'get_instance()' method
 */
Switch_To_Astra::get_instance();
