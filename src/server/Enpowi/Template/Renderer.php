<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 5/26/15
 * Time: 1:46 PM
 */

namespace Enpowi\Template;

use WikiLingo;
use WikiLingo\Parser;
use WikiLingo\Event\Expression\Variable as V;

class Renderer {
    public $engine;
	public $template;
    public $userWikiLingo = false;
	public function __construct($template, $useWikiLingo = false) {
        if ($useWikiLingo) {
            $this->engine = new Parser();
            $this->userWikiLingo = $useWikiLingo;
        }
		$this->template = $template;
	}

	public function out($args) {
        $args = Args::get($args);

        if ($this->userWikiLingo) {
            $this->engine->events->bind(new V\Lookup(function ($key, WikiLingo\Model\Element $element, WikiLingo\Expression\Variable $variable) use ($args) {
                if (isset($args[$key])) {
                    $element->staticChildren[] = $args[$key];
                }
            }));

            $rendered = $this->engine->parse($this->template . '');
        } else {
            $template = $this->template . '';
            //TODO: better template engine
            if (preg_match_all("/{{\s*(.*?)\s*}}/", $template, $m)) {
                foreach ($m[1] as $i => $varname) {
                    if (isset($args[$varname])) {
                        $template = str_replace($m[0][$i], htmlentities($args[$varname], ENT_QUOTES), $template);
                    }
                }
            }
            $rendered = $template;
        }

		return $rendered;
	}
}