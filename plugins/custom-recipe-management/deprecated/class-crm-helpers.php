<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_Helpers {

	public static function value2array( $value ) {
		$value = (int) $value;
		$binary = decbin( $value );

		$months = array();
		for ($i = 0; $i <= 11; $i++) {
			$months[$i+1]=($binary[$i]==1);
		}
		return $months;
	}

	public static function array2value( $months ) {
		if (!is_array($months)) return false;
		$value=0;
		for ($i = 0; $i <= 11; $i++) {
			$value += (!empty($months[$i+1]))?2**$i:0;
		}
		return $value;
	}


}
