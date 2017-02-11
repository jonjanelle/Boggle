<?php
  /*
      Process the submission of the "Boggle Processor" form.
      Classes are used represent cubes and the board. the BoggleBoard class
      contains methods to shuffle the board (new game), search for a given word,
      and list all words of specified lengths by searching the cubes and
      checking potential words against a dictionary file.

      Used https://boardgamegeek.com/thread/300883/letter-distribution
      for info about letter distribution

      Author: Jon Janelle
  */
  session_start(); //board stored as a session variable.

  //If here after a form submission
  if ($_GET) {
    $board = $_SESSION["board"];
    //Reset board cube color
    $board->setColorAll("gains-border");
    //Unset all result session variables
    if (isset($_SESSION["resultString"])){ unset($_SESSION["resultString"]); }
    if (isset($_SESSION["boolarray"])) { unset($_SESSION['boolarray']);}

    if (isset($_GET["options"])){
      if ($_GET["options"]=="shuffle") {
        $board->scramble();
      }
      elseif ($_GET["options"]=="three-letter"){

      }
    }

    if (isset($_GET["word_search"]) and strlen(trim($_GET["word_search"]))>0){
      $board->wordSearch(trim($_GET["word_search"]));
      if (isset($_SESSION["boolarray"]) and isset($_GET["highlight"]) ) {
        $board->booleanColorSet($_SESSION["boolarray"], "red-border");
      }
    }
  }

  //If not here after a form submission,then create a new board
  //and set the board session variable
  else {
    $board=new BoggleBoard();
    if (count($board->cubes)==0){
      $cubeData = fopen("cubes.dat","r") or die("Unable to read cube data file!");
      while(!feof($cubeData)) {
        $cube = new BogglePiece(str_split(trim(fgets($cubeData))));
        array_push($board->cubes,$cube);
      }
    }
    $_SESSION["board"] = $board;
  }


  /*
   * Represents a single 6-sided Boggle cube
   * $sides: an array of characters corresponding to letters on cube faces
   * $upSide: The index of $sides corresponding to the letter currently visible
   * $color: The string name of a css class containing color info for the cube
   */
  class BogglePiece {
    public $sides;  //Array of all sides
    public $upSide; //index of the current side facing up
    public $color;  //border color of piece.

    //Construct a new cube
    //$sideArray: An array of strings representing faces of a Goggle cube
    function __construct($sideArray){
      $this->sides = $sideArray;
      $this->upSide = rand(0,count($this->sides)-1);
      $this->color = "gains-border";
    }

    //Get the currently visible letter string
    function getUpLetter(){
      return $this->sides[$this->upSide];
    }

    //Set the current visible letter
    //$newUpSide: An integer [0,$this->sides-1] corresponding to
    //            a side of the cube face
    function setUpSide($newUpSide){
      if ($newUpSide>=0 and $newUpSide<count($this->sides)){
        $this->upSide=$newUpSide;
      }
    }
  }

  /*
   * Represents an entire Boggle board as a collection of BogglePieces
   * $cubes: A square 2D array of BogglePieces
   */
  class BoggleBoard {
    public $cubes = array(); //will be initialized as a 5x5 array of BogglePieces

    //Reorder the cubes on the board. This is accomplished by:
    //1) Perform a Knuth-Fisher-Yates shuffle on the $cubes array
    //2) Choose a new "random" upSide for each BogglePiece
    function scramble() {
      if (count($this->cubes)>0){
        $endPos = count($this->cubes)-1;
        $temp=0;
        $pos=0;
        $numSides=count($this->cubes[$endPos]->sides); //sides per cube.

        while ($endPos > 1) {
          //Choose a new upward face for cube at $endPos
          $this->cubes[$endPos]->setUpSide(rand(0, $numSides));
          //pick random cube to swap with cube at $endPos
          $pos = rand(0, $endPos-1);
          //Perform swap
          $temp = $this->cubes[$pos];
          $this->cubes[$pos] = $this->cubes[$endPos];
          $this->cubes[$endPos] = $temp;
          //Cube at endPos done being shuffled. Move left one cube.
          $endPos-=1;
        }
      }
    }

    function setColorAll($colorClassName){
        for ($i=0; $i<count($this->cubes); $i++) {
          $this->cubes[$i]->color=$colorClassName;
        }
    }

    function booleanColorSet($arr, $className){
      for ($i=0;$i<count($arr);$i++){
        for ($j=0; $j<count($arr[$i]); $j++){
          if ($arr[$i][$j]) {
            $index = 5*$i+$j;
            $this->cubes[$index]->color=$className;
          }
        }
      }
    }
    //Search the current board for a given word.
    //Wrapper for the dfSearch method
    function wordSearch($word) {
      $result = "";
      $_SESSION["resultString"] = "Target word NOT found.";
      $seen = array_fill(0, 5, array_fill(0,5,false)); //5 by 5 bool array, all false
      $word = strtolower($word);
      //need separate searches beginning at each letter.
      for ($r=0; $r<5; $r++){
        for ($c=0; $c<5; $c++){
          $this->dfSearch($r, $c, $result, $word, $seen, 7, 0);
        }
      }
    }

    /*
     *  Search $this->cubes array (view as 5x5 array of arrays) beginning at
     *  given row and column indices for a given value. Search method is depth-first.
     *  Time-complexity is an issue here, so recursion depth can be limited.
     *  $r :starting row index
     *  $c: starting column index
     *  $result : Temporary storage in which potential matches are built.
     *  $seen : A 5x5 boolean array of arrays indicating whether a particular
     *          cube has already been visited in the construction of $result
     *  $depthLimit:
     */
    function dfSearch($r, $c, $result, $target, $seen, $depthLimit, $currentDepth) {
      // Mark current cell as seen
      $seen[$r][$c] = true;
      //The following calculation assumes a 5x5 grid.
      $index = 5*$r+$c;
      //Add next letter to result string
      $result .= $this->cubes[$index]->getUpLetter();

      if ($result===$target){
        $_SESSION["resultString"] = "Target word found: ".$result."<br>";
        $_SESSION["boolarray"]=$seen;
        return true; //Done!
      }

      //Check whether result string too long or recursion depth exceeded
      else if (strlen($result)>strlen($target) or $currentDepth>=$depthLimit){
        return false;
      }
      //check whether word matches any in the dictionary
      //if (in_array($word, $dict)) { echo "Found!"}

      //Need to check up to 8 positions around each letter
      for ($i=$r-1; $i<=$r+1; $i++){
        for ($j=$c-1; $j<=$c+1; $j++){
          if ($i>=0 and $j>=0 and $i<=4 and $j<=4 and !$seen[$i][$j]){
            $this->dfSearch($i, $j, $result, $target, $seen, $depthLimit, $currentDepth+1);
          }
        }
      }

      //No match, remove last letter from $result
      $result=substr($result,0,strlen($target)-1);
      //mark current as false as it is not part of solution path.
      $seen[$r][$c] = false;
      return false;

    }
}
