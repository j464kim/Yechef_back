<?php

function stripe_to_db($integer)
{
	return number_format(($integer / 100), 2);
}

function db_to_stripe($decimal)
{
	return floor($decimal * 100);
}

function get_currency($country)
{
	// TODO: come up with a generic way to get currency based on country - issue #125
	// Since we serve CA & US only for the time being
	return strtolower($country . 'd');
}