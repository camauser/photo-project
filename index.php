<?php
/*
 * Cam Auser
 * January 18, 2017
 * Photo Project
 */
    // Include all of the functions designated for this page
    include_once("include/index_functions.php");
    session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo getPageHeader(); ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta id="albumMeta" album="<?php echo getAlbumMetaTag(); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="js/index.js"></script>
        <link href="css/thumbs.css" rel="stylesheet">
        <link href="css/index.css" rel="stylesheet" type="text/css">
        <script>
            var imgDirectory = "<?php 
                // Set the variable to the current album, if the user is
                // in an album
                if (isset($_GET["album"]))
                {
                    echo $_GET["album"];
                }
                else
                {
                    echo "";
                }
            ?>";
            var previous = "";
            var next = "";

        </script>
    </head>
    <body>
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
              <div class="navbar-header">
                <a class="navbar-brand" href="index.php">Photo Albums</a>
              </div>
              <ul class="nav navbar-nav">
                  <li <?php if (!validAlbumSelected()){ echo 'class="active"';} ?>><a href="index.php">Home</a></li>
                <li class="dropdown <?php if (validAlbumSelected()){ echo "active";} ?>">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <?php
                        if (validAlbumSelected())
                        { 
                            echo $_GET["album"];
                        }
                        else
                        {
                            echo "Select an Album";
                        } 
                        ?>
                  <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                      <?php
                      $aAlbums = getAlbums();
                      foreach($aAlbums as $currAlbum)
                      {
                          if (isset($_GET["album"]) && $_GET["album"] == $currAlbum)
                          {
                              echo "<li class='active'><a href='index.php?album=$currAlbum'>$currAlbum</a></li>";
                          }
                          else
                          {
                              echo "<li><a href='index.php?album=$currAlbum'>$currAlbum</a></li>";
                          }
                      }
                      ?>
                  </ul>
                </li>
              </ul>
            </div>
        </nav>
        <div class="container">
            <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo getPageHeader(); ?>
                <?php
                if (validAlbumSelected())
                {
                    // If a valid album was selected, we'll echo out a download link
                ?>
                    <span id="albumDownload"><a href="imgservice.php?album=<?php echo $_GET["album"]; ?>"> Download Album</a></span>
                <?php 
                
                }
                ?></h1>
            </div>
        <?php

        if (!isset($_GET["album"]))
        {
            // List all the albums
            listAlbums();
        }
        else
        {
            // Grab the album name, and check to see if it's valid (if it is, at least one image was returned)
            $aImages = getImages($_GET["album"]);

            if (count($aImages) > 0)
            {
                // Album is valid, let's go!
                // Loop through files and display them
                foreach($aImages as $currImg)
                {
                    echo '<div class="col-lg-3 col-md-4 col-xs-6 thumb">';
                    echo '<a class="thumbnail" class="thumbnail" id="' . $currImg . '" href="javascript:void(0)">';
                    echo '<img class="img-responsive" src="img/' . $_GET["album"] . '/thumb/' . $currImg . '" alt="">';
                    echo '</a></div>';
                }
            }
            else
            {
                echo "<h2>Invalid album entered!</h2>";
            }
        }
        ?>
                <div class="modal fade" id="imgModal" role="dialog">
                    <div class="modal-dialog modal-xl">
                        <img id="loadingImage" src="css/loading.svg" max-height="100vh">
                        <img id="modalImage" class="modal-content" src="" max-height="100vh">
                        <div id="controls">
                            <button type="button" class="btn btn-default" id="previous"><span class="glyphicon glyphicon-chevron-left"></span></button>
                            <button type="button" class="btn btn-default" id="closeModal" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-default" id="next"><span class="glyphicon glyphicon-chevron-right"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </body>
</html>
