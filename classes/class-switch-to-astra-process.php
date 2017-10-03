<?php
/**
 * Switch to Astra Process
 *
 * @package Switch_to_Astra
 */

if ( ! class_exists( 'Switch_To_Astra_Process' ) ) {

	/**
	 * Switch_To_Astra_Process
	 *
	 * @since 1.0.0
	 */
	class Switch_To_Astra_Process extends WP_Background_Process {

		use WP_Switch_To_Astra_Logger;

		/**
		 * Action.
		 *
		 * @var string
		 */
		protected $action = 'switch_to_astra_process';

		/**
		 * Task
		 *
		 * Override this method to perform any actions required on each
		 * queue item. Return the modified item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @param int $id Queue item to iterate over.
		 *
		 * @return boolean
		 */
		protected function task( $id ) {

			$elementor  = get_post_meta( $id, '_elementor_edit_mode', true );
			$vc         = get_post_meta( $id, '_wpb_vc_js_status', true );
			$fl_enabled = get_post_meta( $id, '_fl_builder_enabled', true );
			$cornerstone = get_post_meta( $id, '_cornerstone_settings', true );

			if ( $fl_enabled || 'builder' === $elementor || true === $vc || 'true' === $vc || $cornerstone ) {
				update_post_meta( $id, '_astra_content_layout_flag', 'disabled' );
				update_post_meta( $id, 'site-post-title', 'disabled' );
				update_post_meta( $id, 'site-sidebar-layout', 'no-sidebar' );
				update_post_meta( $id, 'ast-featured-img', 'disabled' );

				if ( true === $vc || 'true' === $vc ) {
					update_post_meta( $id, 'site-content-layout', 'plain-container' );
				} else {
					update_post_meta( $id, 'site-content-layout', 'page-builder' );
				}
			}

			return false;
		}

		/**
		 * Complete
		 *
		 * Override if applicable, but ensure that the below actions are
		 * performed, or, call parent::complete().
		 */
		protected function complete() {
			parent::complete();
			update_option( 'switch-to-astra-flag', 'updated' );
		}

	}
}
