<?php
  /**
   * Represents an entire Boggle board as a collection of BogglePieces.
   *
   * Attributes:
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

    /**
     * Set the border color of all cubes to a given
     * CSS color string
     */
    function setColorAll($colorClassName){
        for ($i=0; $i<count($this->cubes); $i++) {
          $this->cubes[$i]->color=$colorClassName;
        }
    }

    /**
     * Set the border color of specified cubes to a given
     * css $colorName based on a boolean array.
     * $arr: A 2D boolean array (representing game grid) with
     *       true in positions that should be colored and false
     *       in positions that should not be colored.
     */
    function booleanColorSet($arr, $colorName){
      for ($i=0;$i<count($arr);$i++){
        for ($j=0; $j<count($arr[$i]); $j++){
          if ($arr[$i][$j]) {
            $index = 5*$i+$j;
            $this->cubes[$index]->color=$colorName;
          }
        }
      }
    }

    /*
      Search the current board for a given $word.
      Returns true if $word is found, false otherwise.
    */
    function wordSearch($word) {
      $seen = array_fill(0, 5, array_fill(0,5,false)); //5 by 5 bool array, all false
      $word = strtolower($word);
      //need separate searches beginning at each letter.
      for ($r=0; $r<5; $r++){
        for ($c=0; $c<5; $c++){
          if ($this->dfSearch($r, $c, "", $word, $seen, 7, 0)) {
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
     *  $depthLimit: The max possible length of the result string
     */
    function dfSearch($r, $c, $result, $target, &$seen, $depthLimit, $currentDepth, $wordList=NULL, &$wordsFound=NULL) {

      if ($wordList == NULL) {
        if ($result===$target){
          return true; //Done!
        }

        //Check whether result string too long or recursion depth exceeded
        else if (strlen($result)>strlen($target) or $currentDepth>=$depthLimit){
          return false;
        }
      }

      //Used for trying to find all words in given WordList of a given length
      else {
        if ($currentDepth==$depthLimit) {
          if ($wordList->inList($result) and !in_array($result,$wordsFound)){
            array_push($wordsFound, $result);
            //Next line included to prevent timeouts. Prevents the method
            //From finding all words of length 5+
            if ($currentDepth>=5) { return true;}
          }
          return false;
        }
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
            if ($this->dfSearch($i, $j, $result, $target, $seen, $depthLimit, $currentDepth+1,$wordList,$wordsFound)) {
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
    //WordList $wordList. This method is needed because it is more efficient to
    //check all possible board constructions against a dictionary than to check
    //whether each word in the dictionary is on the board.
    function findInDict($wordList, $wordLen)
    {
      $wordsFound=array();
      $seen = array_fill(0, 5, array_fill(0,5,false)); //5 by 5 bool array, all false
      for ($r=0; $r<5; $r++){
        for ($c=0; $c<5; $c++){
          $this->dfSearch($r, $c,"","", $seen, $wordLen, 0,$wordList,$wordsFound);
        }
      }
      return $wordsFound;
    }
} #eoc
