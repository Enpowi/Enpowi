<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 6/30/15
 * Time: 5:28 PM
 */

namespace Enpowi\Generic;


class Shareable implements IShareable
{
	public $sharedGroupNames;
	public $sharedEmails;
	public $type;

	public function __construct()
	{

	}
}