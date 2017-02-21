<!doctype html>
<html>
  <head>
    <?php require('BoggleDriver.php');?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
    <title>Boggle Solver</title>
  </head>

  <body>
    <div id="container">
      <div id="header-div">
        <a class="btn btn-info" id="reset-button" href="index.php">Reset</a>
        <p id="score-text">Score: <?=$_SESSION["playerScore"]?></p>
        <h1 class="header">Boggle Solver</h1>
      </div>

      <hr />

      <div class="center-wrapper">
        <div id="mainform">
          <form method="GET" action="index.php">
            <div class="form-group">
              <fieldset>
                <legend><span class="glyphicon glyphicon-search"></span> Search for word:</legend>
                <input type="text" class="form-control" name="word_search" autofocus>
                <label class="form-check-label">
                  <input type="checkbox" class="form-check-input" name="highlight" checked="checked">
                  Highlight word on board if found?
                </label>

                <label class="form-check-label">
                  <input type="checkbox" class="form-check-input" name="trackwords" checked="checked">
                  Track words found and score?
                </label>
              </fieldset>
            </div>
            <div class="form-group">
              <fieldset>
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
              </fieldset>
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
