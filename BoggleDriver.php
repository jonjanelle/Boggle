<?php
  require('BogglePiece.php');
  require('BoggleBoard.php');
  require('WordList.php');
  require('tools.php');
  set_time_limit(30);
  /*
      Process the submission of the "Boggle Processor" form.
      Classes are used represent cubes and the board. the BoggleBoard class
      contains methods to shuffle the board (new game), search for a given word,
      and list all words of specified lengths by searching the cubes and
      checking potential words against a dictionary file.

      Used https://boardgamegeek.com/thread/300883/letter-distribution
      for info about letter distribution

      Word list obtained from: http://www.gwicks.net/dictionaries.htm

      Author: Jon Janelle
  */

  /**
   * Get point value of word.
   *
   */
  function getWordValue($word) {
    $len = strlen($word);
    return $len-2;

  }

  /**
   * Create a new BoggleBoard.
   * $cubeData: A newline-separated text file of BogglePiece side
   *            configurations.
   */
  function initNewBoard($cubeData) {
    $board=new BoggleBoard();
    $_SESSION["wordsFound"]="";
    $_SESSION["playerScore"]=0;
    $cubeData = fopen($cubeData,"r") or die("Unable to read cube data file!");
    while(!feof($cubeData)) {
      $cube = new BogglePiece(str_split(trim(fgets($cubeData))));
      array_push($board->cubes,$cube);
    }
    $_SESSION["board"] = $board;
  }

  session_start();

  //Reset resultString to remove any previous results
  $resultString="";

  //Unset boolArray so that new searches can be performed
  if (isset($_SESSION["boolArray"])) { unset($_SESSION['boolArray']);}

  //Reset result box color to its neutral state
  $alert_color = "alert-info";

  if ($_GET) {
    //$board is a BoggleBoard object
    $board = $_SESSION["board"];

    //Reset board piece border colors
    $board->setColorAll("gains-border");
    //Result result box color
    $alert_color = "alert-info";
    //Process any radio button selections first
    if (isset($_GET["options"])){
      $alert_color = "alert-success";
      if ($_GET["options"]=="shuffle") {
        $board->scramble();
        $_SESSION["wordsFound"]="";
        $_SESSION["playerScore"]=0;
        $alert_color = "alert-info";
      }
      elseif ($_GET["options"]=="three-letter"){
        //word list was broken into smaller files to improve efficiency.
        if (!isset($_SESSION["words3"])){ //load file only if needed
          $_SESSION["words3"] = new WordList("english3.txt");
        }
        $words = $board->findInDict($_SESSION["words3"],3);
        $resultString.="Three letter words:<br />";
        foreach ($words as $w) {
          $resultString.=$w." ";
        }
      }
      elseif ($_GET["options"]=="four-letter"){
        if (!isset($_SESSION["words4"])){
          $_SESSION["words4"] = new WordList("english4.txt");
        }
        $words = $board->findInDict($_SESSION["words4"],4);
        $resultString.="Four letter words:<br />";
        foreach ($words as $w) {
          $resultString.=$w." ";
        }
      }
      elseif ($_GET["options"]=="five-letter"){
        if (!isset($_SESSION["words5"])){
          $_SESSION["words5"] = new WordList("english5.txt");
        }
        $words = $board->findInDict($_SESSION["words5"],5);
        $resultString.="Five letter words:<br />";
        foreach ($words as $w) {
          $resultString.=$w." ";
        }
      }
    }

    //Process and search for word in text input.
    if (isset($_GET["word_search"]) and strlen(trim($_GET["word_search"]))>0){
      //Load dictionary file as WordList object
      if (!isset($_SESSION["wordList"])){
        $_SESSION["wordList"] = new WordList("english.txt");
      }
      //sanitize user input and convert to upper case.
      $target = strtoupper(trim(sanitize($_GET["word_search"])));
      //Get status of checkboxes
      $highlight =  isset($_GET["highlight"]);
      $trackWords = isset($_GET["trackwords"]);
      //Get WordList for checking input against dictionary
      $wordList = $_SESSION["wordList"];

      if ($board->wordSearch($target)) { //Search for word on board
        $resultString = $target." was found on the board.<br>";
        if ($highlight) {
          $board->booleanColorSet($_SESSION["boolArray"], "green-border");
        }

        //Check if word in dictionary file
        if ($wordList->inList($target)){
          if (stristr($_SESSION["wordsFound"],$target)==false) {
            if ($trackWords){
              $_SESSION["wordsFound"].=$target." ";
              $_SESSION["playerScore"]+=getWordValue($target);
            }
            $alert_color = "alert-success";
          }
          else {
            $alert_color = "alert-warning";
            $resultString.="Word already found.";
          }
        }
        //else word found on board but not in dictionary
        else {
          if ($highlight){
            $board->booleanColorSet($_SESSION["boolArray"], "red-border");
          }
          $resultString.=$target." was NOT found in the dictionary.<br />";
          $alert_color = "alert-danger";
        }
      }
      //else word was not found on the board at all
      else {
        $resultString = $target." was not found.";
        $alert_color = "alert-danger";
      }
    }
  }

  //If not here after a form submission,then create a new board
  else {
    initNewBoard("cubes.dat");
  }
