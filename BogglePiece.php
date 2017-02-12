<?php
/**
 * Represents a single Boggle piece
 * $sides: Array of characters corresponding to letters on piece faces.
 *         Should be length 6 to match the actual game.
 * $upSide: The index of $sides corresponding to the face currently visible
 * $color: The string name of a css class containing color info for the cube
 */
class BogglePiece {
  public $sides;  //Array of strings for each side
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
