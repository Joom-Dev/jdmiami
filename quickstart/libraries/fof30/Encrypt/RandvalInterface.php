<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Encrypt;

// Protect from unauthorized access
defined('_JEXEC') or die();

interface RandvalInterface
{
	/**
	 *
	 * Returns a cryptographically secure random value.
	 *
	 * @return string
	 *
	 */
	public function generate();
}