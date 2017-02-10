<?php
  session_start(); //board stored as a session variable.
  //Used https://boardgamegeek.com/thread/300883/letter-distribution
  //for info about letter distribution



  if ($_GET) {
    $board = $_SESSION["board"];
    if (isset($_GET["options"])){
      if ($_GET["options"]=="shuffle") {
        $board->scramble();
      }
    }
    if (isset($_GET["word_search"])){
      $_SESSION["resultString"] = "Target word NOT found.";
      $board->wordSearch(trim($_GET["word_search"]));
    }
  }

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
   */
  class BogglePiece {
    public $sides; //Array of all sides
    public $upSide; //index of the current side facing up
    public $color; //border color of piece.

    //New pieces are constructed using $sides array
    function __construct($sideArray){
      $this->sides = $sideArray;
      $this->upSide = rand(0,count($this->sides)-1);
      $this->color = "gains-border";
    }

    //Get the current visible letter
    function getUpLetter(){
      return $this->sides[$this->upSide];
    }

    //Set the current visible letter
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
          $pos = rand(0, $endPos-1); //pick value to swap
          $temp = $this->cubes[$pos];
          $this->cubes[$pos] = $this->cubes[$endPos];
          $this->cubes[$endPos] = $temp;
          $endPos-=1;
        }
      }
    }

    //Search the current board for a given word.
    //This is a wrapper method that calls the dfSearch method
    function wordSearch($word) {
      $result = "";
      $seen = array_fill(0, 5, array_fill(0,5,false)); //5 by 5 bool array, all false
      //need separate searches beginning at each letter.
      for ($r=0; $r<5; $r++){
        for ($c=0; $c<5; $c++){
          if ($this->dfSearch($r, $c, $result, $word, $seen, 5, 0)){
            for ($i=0; $i<count($seen);$i++){
              for ($j=0; $j<count($seen[$i]); $j++){
                if ($seen[$i][$j]==true){
                  $index = 5*$i+$j;
                  $this->cubes[$index]->color="red-border";
                }
              }
            }
            return;
          }
        }
      }
    }

    function dfSearch($r, $c, $result, $target, $seen, $depthLimit, $currentDepth) {
      // Mark current cell as seen
      $seen[$r][$c] = true;
      //The following calculation assumes a 5x5 grid.
      $index = 5*$r+$c;
      //Add next letter to result string
      $result .= $this->cubes[$index]->getUpLetter();

      if (strlen($target)<strlen($result) or $depthLimit<=$currentDepth){
        return false; //The string built up longer than target word, so backtrack.
      }
      //check whether word matches any in the dictionary
      else if ($result==$target){
        $_SESSION["resultString"] = "Target word found!";
        return true; //Done!
      }
      //if (in_array($word, $dict)) { echo "Found!"}

      //Need to check up to 8 positions around each letter
      for ($i=$r-1; $i<=$r+1; $i++){
        for ($j=$c-1; $j<$c+1; $j++){
          if ($i>=0 and $j>=0 and $i<=4 and $j<=4 and !$seen[$i][$j]){
            $this->dfSearch($i, $j, $result, $target, $seen,$depthLimit,$currentDepth+1);
          }
        }
      }

      //No match, get rid of last letter
      $result=substr($result,0,strlen($target)-1);
      //mark current as false as it isn't part of solution path.
      $seen[$r][$c] = false;
      //$this->cubes[$index]->color="gains-border";
      return false;
    }

  }
