<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 5/26/15
 * Time: 1:46 PM
 */

namespace Enpowi\Template;


class Renderer {

	public $template;
	public function __construct($templatePath) {
		$this->template = file_get_contents($templatePath);
	}

	public function out($template, $args) {
		$allArgs = Args::get($args);

		//TODO: better template engine
		if (preg_match_all("/{{\s*(.*?)\s*}}/", $template, $m)) {
			foreach ($m[1] as $i => $varname) {
				$template = str_replace($m[0][$i], $allArgs[$varname], $template);
			}
		}

		return $template;
	}
}