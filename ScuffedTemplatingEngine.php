<?php

class ScuffedTemplatingEngine {
  private $fileExt;
  private $templatePath;
  private $valueMap;

  public function __construct($fileExt, $templatePath) {
    $this->fileExt = $fileExt;
    $this->templatePath = $templatePath;
  }

  //Returns parsed HTML
  public function render($filename, $nameValueMap) {
    $this->valueMap = $nameValueMap;
    $template = $this->getFileContents($filename);
    return $this->renderTemplate($template);
  }

  private function renderTemplate($template) {
    $parseTemplate = function($result) {
      return $this->parseTemplateStatement($result[1]);
    };
    return preg_replace_callback("/{(.*)}/", $parseTemplate, $template);
  }

  //Redirects template statements (if, foreach or include) to appropriate parsing functions
  private function parseTemplateStatement($statement) {
    $prefix = $this->getFuncPrefix($statement);
    switch ($prefix) {
      case 'var':
        return $this->parseVar($statement);
        break;
      case 'if':
        return $this->parseIf($statement);
        break;
      case 'foreach':
        return $this->parseForeach($statement);
        break;
      case 'include':
        return $this->parseInclude($statement);
        break;
      default:
        throw new Error("Scuffed templating engine doesn't recognize the prefix '$prefix'");
    }
  }

  //Gets template contents from specific file and the directory that was declared in the constructor
  private function getFileContents($filename) {
    $path = $this->templatePath
          . '\\'
          . $filename
          . ".$this->fileExt";
    if(!file_exists($path)) {
      throw new Error("The file at '$path' does not exist.");
    }
    return file_get_contents($path);
  }

  private function parseVar($result) {
    if(!isset($this->valueMap[$result])) {
      throw new Error("The value for the placeholder '$result[1]' does not exist.");
    }

    return $this->valueMap[$result];
  }

  private function parseIf($result) {
    $innerds = $this->getInnerds($result);
    if(isset($this->valueMap[$innerds])) {
      return $this->valueMap[$innerds];
    }
    return '';
  }

  private function parseForeach($result) {
    $innerds = $this->getInnerds($result);
    if(gettype($this->valueMap[$innerds]) !== 'array') {
      throw new Error("Scuffed Templating Engine's 'Foreach' declaration requires an array");
    }

    $html = '';
    foreach($this->valueMap[$innerds] as $string) {
      if(gettype($string) !== 'string') {
        throw new Error("Scuffed Templating Engine's 'Foreach' declaration requires an array composed of strings");
      }
      $html .= $string;
    }
    return $html;
  }

  private function parseInclude($result) {
    $innerds = $this->getInnerds($result);
    return $this->getFileContents($innerds);
  }


  //Returns whatever's {in here} <h1>{or here}</h1>
  private function getFuncPrefix($string) {
    $regexString = "/^(.*?)\((.*)\)/";
    preg_match($regexString, $string, $matches);
    if(isset($matches[1])) {
      return $matches[1];
    }
    return 'var';
  }

  //Returns whatever's if(in here) include(or here) foreach(or maybe he)
  private function getInnerds($string) {
    $regexString = "/\((.*)\)/";
    preg_match($regexString, $string, $matches);
    return $matches[1];
  }


}
