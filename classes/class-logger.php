<?php
/**
 * Switch to Astra Logger
 *
 * @package Switch_to_Astra
 */

/**
 * Switch to Astra Logger
 */
trait WP_Switch_To_Astra_Logger {

	/**
	 * Really long running process
	 *
	 * @return int
	 */
	public function really_long_running_task() {
		return sleep( 1 );
	}

	/**
	 * Log
	 *
	 * @param string $message Log Message.
	 */
	public function log( $message ) {
		error_log( $message );
	}

	/**
	 * Get lorem
	 *
	 * @param string $id Post Id.
	 *
	 * @return string
	 */
	protected function get_message( $id ) {

		$message = 'Post Updated : ' . $id;
		return $message;
	}

}
