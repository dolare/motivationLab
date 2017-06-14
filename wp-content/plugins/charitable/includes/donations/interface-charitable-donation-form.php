 <?php
/**
 * Donation form interface. 
 *
 * This defines a strict interface that donation forms must implement.
 *
 * @version		1.0.0
 * @package		Charitable/Interfaces/Charitable_Donation_Form_Interface
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! interface_exists( 'Charitable_Donation_Form_Interface' ) ) : 

/**
 * Charitable_Donation_Form_Interface interface. 
 *
 * @since		1.0.0
 */
interface Charitable_Donation_Form_Interface {

	/**
	 * Render the donation form. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function render();

	/**
	 * Validate the submitted values. 
	 *
	 * @return  boolean
	 * @access  public
	 * @since   1.0.0
	 */
	public function validate_submission();

	/**
	 * Return the donation values.
	 *
	 * @return  array
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_donation_values();	
}

endif; // End interface_exists check.