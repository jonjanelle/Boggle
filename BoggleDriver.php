<?php
  require('BogglePiece.php');
  require('BoggleBoard.php');
  require('WordList.php');
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

   */
  function getWordValue($word) {
    $len = strlen($word);
    return $len-2;

  }

  session_start();

  //Reset resultString to remove any previous results
  $resultString="";

  //Unset boolArray so that new searches can be performed
  if (isset($_SESSION["boolArray"])) { unset($_SESSION['boolArray']);}

  //Reset result box color to its neutral state
  $alert_color = "alert-info";

  if ($_GET) {
    $alert_color = "alert-info";
    //$board is a BoggleBoard object
    $board = $_SESSION["board"];

    //Reset board piece border colors
    $board->setColorAll("gains-border");

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

    //Process and search for word in text input
    if (isset($_GET["word_search"]) and strlen(trim($_GET["word_search"]))>0){
      //Load dictionary file as WordList object
      if (!isset($_SESSION["wordList"])){
        $_SESSION["wordList"] = new WordList("english.txt");
      }
      $target = strtoupper(trim($_GET["word_search"]));
      $wordList = $_SESSION["wordList"];
      if ($board->wordSearch($target)) {
        $resultString = $target." was found on the board.<br>";

        if (isset($_GET["highlight"]) ) {
          if ($wordList->inList($target)){
            $board->booleanColorSet($_SESSION["boolArray"], "green-border");

            if (stristr($_SESSION["wordsFound"],$target)==false) {
              $_SESSION["playerScore"]+=getWordValue($target);
              $_SESSION["wordsFound"].=$target." ";
              $alert_color = "alert-success";
            }
            else {
              $alert_color = "alert-warning";
              $resultString.="Word already found.";
            }
          }
          else {
            $board->booleanColorSet($_SESSION["boolArray"], "red-border");
            $resultString.=$target." was NOT found in the dictionary.<br />";
            $alert_color = "alert-danger";
          }
        }
      }
      else {
        $resultString = $target." was not found.";
        $alert_color = "alert-danger";
      }
    }
  }

  //If not here after a form submission,then create a new board
  //and set the board session variable
  else {
    $board=new BoggleBoard();
    $_SESSION["wordsFound"]="";
    $_SESSION["playerScore"]=0;
    $cubeData = fopen("cubes.dat","r") or die("Unable to read cube data file!");
    while(!feof($cubeData)) {
      $cube = new BogglePiece(str_split(trim(fgets($cubeData))));
      array_push($board->cubes,$cube);
    }
    $_SESSION["board"] = $board;
  }
