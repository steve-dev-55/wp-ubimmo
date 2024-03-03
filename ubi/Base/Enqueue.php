<?php

/**
 * @package  Ubimmo
 */

namespace Ubi\Base;

use Ubi\Base\BaseController;

/**
 * custom style plugin
 */
class Enqueue extends BaseController
{
	public function register()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue'));
	}

	function enqueue()
	{
		// enqueue all our scripts
		wp_enqueue_script('media-upload');
		wp_enqueue_media();
		wp_enqueue_style('mypluginstyle', $this->plugin_url . 'assets/mystyle.css');
		wp_enqueue_script('mypluginscript', $this->plugin_url . 'assets/myscript.js');
	}
}
