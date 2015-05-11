<?php

function migla_get_currency_array(){
	$currencies = array(
		'AUD' => array( 'code' => 'AUD' , 'name' => 'Australian Dollar', 'symbol' => '$', 'faicon' => 'fa-dollar' ),
		'BRL' => array( 'code' =>'BRL' , 'name' => 'Brazilian Real', 'symbol' => 'R$', 'faicon' => '' ),
		'CAD' => array( 'code' =>'CAD' , 'name' => 'Canadian Dollar', 'symbol' => '$', 'faicon' => 'fa-dollar'),
		'CZK' => array( 'code' =>'CZK' , 'name' => 'Czech Koruna', 'symbol' => 'Kc', 'faicon' => ''),
		'DKK' => array( 'code' =>'DKK' , 'name' => 'Danish Krone', 'symbol' => 'kr', 'faicon' => ''),
		'EUR' => array( 'code' =>'EUR' , 'name' => 'Euro', 'symbol' => '&euro;', 'faicon' => 'fa-eur'),
		'HKD' => array( 'code' =>'HKD' , 'name' => 'Hong Kong Dollar', 'symbol' => '$', 'faicon' => ''),
		'HUF' => array( 'code' =>'HUF' , 'name' => 'Hungarian Forint', 'symbol' => 'Ft', 'faicon' => ''),
		'ILS' => array( 'code' =>'ILS' , 'name' => 'Israeli New Sheqel', 'symbol' => '&#8362;', 'faicon' => 'fa-ils'),
		'JPY' => array( 'code' =>'JPY' , 'name' => 'Japanese Yen', 'symbol' => '&yen;', 'faicon' => 'fa-jpy'),
		'MYR' => array( 'code' =>'MYR' , 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'faicon' => ''),
		'MXN' => array( 'code' =>'MXN' , 'name' => 'Mexican Peso', 'symbol' => '$', 'faicon' => ''),
		'NOK' => array( 'code' =>'NOK' , 'name' => 'Norwegian Krone', 'symbol' => 'kr', 'faicon' => ''),
		'NZD' => array( 'code' =>'NZD' , 'name' => 'New Zealand Dollar', 'symbol' => '$', 'faicon' => ''),
		'PHP' => array( 'code' =>'PHP' , 'name' => 'Philippine Peso', 'symbol' => '&#8369;', 'faicon' => ''),
		'PLN' => array( 'code' =>'PLN' , 'name' => 'Polish Zloty', 'symbol' => '&#122;&#322;', 'faicon' => ''),
		'GBP' => array( 'code' =>'GBP' , 'name' => 'Pound Sterling', 'symbol' => '&pound;', 'faicon' => ''),
		'RUB' => array( 'code' =>'RUB' , 'name' => 'Russian Ruble', 'symbol' => 'RUB', 'faicon' => ''),
		'SGD' => array( 'code' =>'SGD' , 'name' => 'Singapore Dollar', 'symbol' => '$', 'faicon' => ''),
		'SEK' => array( 'code' =>'SEK' , 'name' => 'Swedish Krona', 'symbol' => 'kr', 'faicon' => ''),
		'CHF' => array( 'code' =>'CHF' , 'name' => 'Swiss Franc', 'symbol' => '&#8355;', 'faicon' => ''),
		'TWD' => array( 'code' =>'TWD' , 'name' => 'Taiwan New Dollar', 'symbol' => '$', 'faicon' => ''),
		'THB' => array( 'code' =>'THB' , 'name' => 'Thai Baht', 'symbol' => '&#3647;', 'faicon' => ''),
		'TRY' => array( 'code' =>'TRY' , 'name' => 'Turkish Lira', 'symbol' => '&#8378;', 'faicon' => 'fa-try'),
		'USD' => array( 'code' =>'USD' , 'name' => 'U.S. Dollar', 'symbol' => '$', 'faicon' => 'fa-usd')
	);
    return $currencies;
}
	

?>