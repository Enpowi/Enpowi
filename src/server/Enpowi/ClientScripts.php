<?php

namespace Enpowi;


class ClientScripts
{

  public $jsFiles = [];
  public $js = [];
  public $cssFiles = [];
  public $css = [];
  public $isMinified = false;

  function addJsFile($file)
  {
    $this->jsFiles[] = $file;
    return $this;
  }

  function addJs($script)
  {
    $this->js[] = $script;
    return $this;
  }

  function outJs()
  {
    $out = $this->outputJsFiles();
    $out .= $this->outputJs();
    return $out;
  }

  function outputJsFiles()
  {
    $back = "";

    foreach ($this->jsFiles as $file) {
      $back .= "<script src=\"" . ($file) . "\"></script>\n";
    }
    $back .= "\n";

    return $back;
  }

  function outputJs()
  {
    $out = "";

    foreach ($this->js as $js) {
      $out .= ";(function() {" . $js . "})();";
    }

    return $out;
  }

  function addCssFile($file)
  {
    $this->cssFiles[] = $file;
    return $this;
  }

  function addCss($rules, $rank = 0)
  {
    if (empty($this->css[$rank]) or !in_array($rules, $this->css[$rank])) {
      $this->css[$rank][] = $rules;
    }
    return $this;
  }

  function outCss()
  {
    $out = $this->outputCssFiles();
    $out .= $this->outputStyles();
    return $out;
  }

  function outputStyles()
  {
    $out = '';
    if (count($this->css)) {
      $out .= "<style><!--\n";
      foreach ($this->css as $css) {
        $out .= "$css\n";
      }
      $out .= "-->\n</style>\n\n";
    }

    return $out;
  }

  function clear()
  {
    $this->js = [];
    $this->jsFiles = [];
    $this->css = [];
    $this->cssFiles = [];

    return $this;
  }

  function outputCssFiles($media = '')
  {
    $back = '';

    if ($this->isMinified) {
      //TODO: handle minify
    }

    foreach ($this->cssFiles as $file) {
      $back .= "<link rel=\"stylesheet\" href=\"" . ($file) . "\" type=\"text/css\"";
      if (!empty($media)) {
        $back .= " media=\"" . ($media) . "\"";
      }
      $back .= " />\n";
    }

    return $back;
  }
}