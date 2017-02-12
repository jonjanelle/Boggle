<?php
  /**
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
    /*
      Search the current board for a given $word.
      Returns true if $word is found, false otherwise.
    */
    function wordSearch($word) {
      $result = "";
      $seen = array_fill(0, 5, array_fill(0,5,false)); //5 by 5 bool array, all false
      $word = strtolower($word);
      //need separate searches beginning at each letter.
      for ($r=0; $r<5; $r++){
        for ($c=0; $c<5; $c++){
          if ($this->dfSearch($r, $c, $result, $word, $seen, 7, 0)) {
            $_SESSION["boolArray"]=$seen;
            return true;
          }
        }
      }
      return false;
    }

    /**
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
    function dfSearch($r, $c, $result, $target, &$seen, $depthLimit, $currentDepth) {

      if ($result===$target){
        return true; //Done!
      }
      //Check whether result string too long or recursion depth exceeded
      else if (strlen($result)>strlen($target) or $currentDepth>$depthLimit){
        return false;
      }

      // Mark current cell as seen
      $seen[$r][$c] = true;
      //The following calculation assumes a 5x5 grid.
      $index = 5*$r+$c;
      //Add next letter to result string
      $result .= $this->cubes[$index]->getUpLetter();

      //check whether word matches any in the dictionary
      //if (in_array($word, $dict)) { echo "Found!"}
      //Need to check up to 8 positions around each letter
      for ($i=$r-1; $i<=$r+1; $i++){
        for ($j=$c-1; $j<=$c+1; $j++){
          if ($i>=0 and $j>=0 and $i<=4 and $j<=4 and !$seen[$i][$j]){
            if ($this->dfSearch($i, $j, $result, $target, $seen, $depthLimit, $currentDepth+1)) {
              return true;
            }
          }
        }
      }
      //No match, remove last letter from $result
      $result=substr($result,0,strlen($target)-1);
      //mark current as false as it is not part of solution path.
      $seen[$r][$c] = false;
      return false;

    }

    //Find all words on this board with length $wordLen that are in the
    //string array $dict
    function findInDict($dict, $wordLen)
    {

    }
}
