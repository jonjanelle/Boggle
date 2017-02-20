<?php require('BoggleDriver.php');?>
<!doctype html>
<html>
  <head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="styles.css">
    <title>Boggle Solver</title>
  </head>

  <body>
    <div id="container">
      <a type="button"class="btn btn-info" id="reset-button" href=".\">Reset</a>
      <p id="score-text">Score:
        <?=$_SESSION["playerScore"]?>
      </p>
      <h1 class="header">Boggle Solver</h1>

      <hr />

      <div class="center-wrapper">
        <div id="mainform">
          <form method="GET" action="index.php">
            <div class="form-group">
              <legend><span class="glyphicon glyphicon-search"></span> Search for word:</legend>

              <input type="text" class="form-control" name="word_search" autofocus>
              <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="highlight" checked="checked">
                Highlight word on board if found?
              </label>

              <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="trackwords" checked="checked">
                Keep track of words found?
              </label>

            </div>
            <div class="form-group">
              <legend><span class="glyphicon glyphicon-plus"></span> Other Options:</legend>
              <label class="form-check-label rad">
                <input type="radio" class="form-check" name="options" value="shuffle">
                Shuffle board
              </label>
              <label class="form-check-label rad">
                <input type="radio" class="form-check" name="options" value="three-letter">
                Show all 3-letter words
              </label>
              <label class="form-check-label rad">
                <input type="radio" class="form-check" name="options" value="four-letter">
                Show all 4-letter words
              </label>
              <label class="form-check-label rad">
                <input type="radio" class="form-check" name="options" value="five-letter">
                Show some 5-letter words
              </label>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </div>
          </form>
        </div>

        <div id="boggle-board">
          <?php
            if (isset($_SESSION["board"])) {
              foreach($_SESSION["board"]->cubes as $cube) {
                echo "<div class=\"boggle-square ".$cube->color."\">".$cube->getUpLetter()."</div>";
              }
            }
           ?>
        </div>
      </div>



      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6 col-sm-6 col-xs-6 result-label">Results</div>
          <div class="col-md-6 col-sm-6 col-xs-6 result-label">Words Found</div>
        </div>

        <div class="row">
          <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="alert <?=$alert_color?> shadowbox" id="result-box">
              <?=$resultString?>
            </div>
          </div>
          <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="alert alert-info shadowbox" id="found-box">
              <?=$_SESSION["wordsFound"]?>
            </div>
          </div>
        </div>
      </div>


    </div>
  </body>
</html>
