<?php

/*
 * Class to load a word list from a file. Each line of the input file should
 * contain exactly one word.
 *
 */
class WordList {
  public $wordArray;

  /*
  Create a new wordlist
  $fileName: A string filepath for the input data file
  */
  function __construct($fileName){
    $this->wordArray=array();
    $wordFile = fopen($fileName,"r") or die("Unable to open data file!");
    while(!feof($wordFile)) {
      $word = trim(fgets($wordFile));
      array_push($this->wordArray,$word);
    }
  }

  //Check whether $word is in this WordList
  function inList($word) {
    $word = strtolower(trim($word));
    for ($i=0; $i<count($this->wordArray);$i++){
      if ($this->wordArray[$i]==$word) {
        return true;
      }
    }
    return false;
  }
}
