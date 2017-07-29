<?php

function stripe_to_db($integer)
{
	return number_format(($integer / 100), 2);
}

function db_to_stripe($decimal)
{
	return floor($decimal * 100);
}