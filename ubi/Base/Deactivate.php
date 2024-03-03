<?php

/**
 * @package  Ubimmo
 */

namespace Ubi\Base;

class Deactivate
{
	public static function deactivate()
	{
		flush_rewrite_rules();
	}
}
