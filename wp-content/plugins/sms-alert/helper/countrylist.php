<?php
/**
 * Countrylist helper.
 *
 * @package Helper
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**SmsAlertCountryList class */
class SmsAlertCountryList {

	/**
	 * Get country code list.
	 *
	 * @return array
	 */
	/* public static function getCountryCodeList() {
		$countries = array();
		$datas     = (array) json_decode( SmsAlertcURLOTP::country_list(), true );
		if ( array_key_exists( 'description', $datas ) ) {
			$countries = $datas['description'];
		}
		return $countries;
	} */

	/**
	 * Get Country Pattern.
	 *
	 * @param string $countryCode countryCode.
	 *
	 * @return array
	 */
	/* public static function getCountryPattern( $countryCode = null ) {
		$c       = self::getCountryCodeList();
		$pattern = '';

		foreach ( $c as $list ) {
			if ( $list['Country']['c_code'] === $countryCode ) {

				if ( array_key_exists( 'pattern', $list['Country'] ) ) {
					$pattern = $list['Country']['pattern'];
					break;
				}
			}
		}
		return $pattern;
	} */
}
new SmsAlertCountryList();
