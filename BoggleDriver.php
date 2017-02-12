<?php
  require('BogglePiece.php');
  require('BoggleBoard.php');
  require('WordList.php');
  set_time_limit(60);
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
  session_start();

  //Load dictionary file as WordList object
  if (!isset($_SESSION["wordList"])){
    $_SESSION["wordList"] = new WordList("english.txt");
  }

  //Unset all result session variables from previous page loads
  if (isset($_SESSION["resultString"])){ unset($_SESSION["resultString"]); }
  if (isset($_SESSION["boolArray"])) { unset($_SESSION['boolArray']);}


  if ($_GET) { //If here after a form submission
    //$board is a BoggleBoard object
    $board = $_SESSION["board"];
    //Reset board piece colors
    $board->setColorAll("gains-border");
    //get array of words
    $wordList = $_SESSION["wordList"];

    if (isset($_GET["options"])){
      if ($_GET["options"]=="shuffle") {
        $board->scramble();
      }
      elseif ($_GET["options"]=="three-letter"){
        $found = "";
        foreach ($wordList->wordArray as $word){
          if (strlen($word)==4){
              if ($board->wordSearch($word)) {
                $found.=$word.", ";
                if (strlen($found)>50){break;}
              }
          }
        }
        $_SESSION["resultString"].="<br />".$found;
      }
    }

    if (isset($_GET["word_search"]) and strlen(trim($_GET["word_search"]))>0){
      if ($board->wordSearch(trim($_GET["word_search"]))) {
        $_SESSION["resultString"] = "Target word found on board.<br>";
        if (isset($_GET["highlight"]) ) {
          if ($wordList->inList($_GET["word_search"])){
            $board->booleanColorSet($_SESSION["boolArray"], "green-border");
          }
          else {
            $board->booleanColorSet($_SESSION["boolArray"], "red-border");
            $_SESSION["resultString"].="Word was NOT found in the dictionary.<br />";
          }
        }
      }
      else {
        $_SESSION["resultString"] = "Word was not found.";
      }
    }

  }

  //If not here after a form submission,then create a new board
  //and set the board session variable
  else {
    $board=new BoggleBoard();
    $cubeData = fopen("cubes.dat","r") or die("Unable to read cube data file!");
    while(!feof($cubeData)) {
      $cube = new BogglePiece(str_split(trim(fgets($cubeData))));
      array_push($board->cubes,$cube);
    }
    $_SESSION["board"] = $board;
  }
