<?php
/*
 * Cam Auser
 * January 18, 2017
 * Photo Project
 */

    session_start();




    // If you're putting together your own copy of this project, BE SURE TO SET UP A PASSWORD IN
    // THE FILE LOCATED AT include/admin_hash.php
    // You should store the SHA-256 hash of your password in a define statement, with the variable named
    // HASHED_PASSWORD





    include_once("include/admin_hash.php");
    include_once("include/BootstrapForm.php");

    function getAlbums()
    {
        $aAlbums = array();
        $dir = new DirectoryIterator("img");
            foreach ($dir as $fileinfo) 
            {
                if (!$fileinfo->isDot()) 
                {
                    array_push($aAlbums, $fileinfo->getFilename());
                }
            }

        usort($aAlbums, function($a, $b) { 
            return strnatcmp($a, $b);
        });

        return $aAlbums;
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Admin Panel</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </head>
    <style>
        .status {
            height: 2em;
        }
        
        #successGlyph, #failureGlyph {
            font-size: 1.2em;
            padding-right: 1vw;
        }
        
        #successGlyph {
            color: green;
        }
        
        #failureGlyph {
            color: red;
        }
    </style>
    <script>
        $(document).ready(function()
        {
            // Hide the glyphs initially
            $("#successGlyph").hide();
            $("#failureGlyph").hide();
            
            $("#thumbRegen").click(function()
            {
                $("#regenModal").modal("show");
                $("#successGlyph").hide();
                $("#failureGlyph").hide();
                $("#statusImg").show();
                $("#statusHeader").text("Thumbnail file Regeneration");
                $("#statusMessage").text("Regenerating thumbnails...");
                // Lock out the close buttons
                $(".close-btn").hide();
                // Regenerate all thumbnails w/ AJAX
                var ajax = {
                    type: "ThumbnailRegen"
                };
                $.ajax({
                    url: "imgservice.php",
                    method: "POST",
                    data: JSON.stringify(ajax),
                    success: function(JSONresult)
                    {
                        console.log(JSONresult);
                        var result = JSON.parse(JSONresult);
                        // Regardless of what happened, hide the loading image
                        $("#statusImg").hide();
                        if (result.regenSucceeded)
                        {
                            // On success show the success glyph, hide the loading
                            // image
                            $("#successGlyph").show();
                            $("#statusMessage").text("Thumbnails successfully regenerated!");
                        }
                        else
                        {
                            // On failure, hide the loading image, and instead
                            // show a cross
                            $("#failureGlyph").show();
                            $("#statusMessage").text("An error occurred during regeneration. Try again later.");
                        }
                        
                        // Enable the close buttons
                        $(".close-btn").show();
                    }
                });
            });

            // This zipRegen bit is commented out, because for now we've decided to dynamically generate zip files
            // as needed (space > speed don'tcha know)
            /*$("#zipRegen").click(function()
            {
                $("#regenModal").modal("show");
                $("#successGlyph").hide();
                $("#failureGlyph").hide();
                $("#statusImg").show();
                $("#statusHeader").text("Zip file Regeneration");
                $("#statusMessage").text("Regenerating zip files...");
                // Lock out the close buttons
                $(".close-btn").hide();
                // Regenerate all thumbnails w/ AJAX
                var ajax = {
                    type: "ZipRegen"
                };
                $.ajax({
                    url: "imgservice.php",
                    method: "POST",
                    data: JSON.stringify(ajax),
                    success: function(JSONresult)
                    {
                        var result = JSON.parse(JSONresult);
                        // Regardless of what happened, hide the loading image
                        $("#statusImg").hide();
                        if (result.regenSucceeded)
                        {
                            // On success show the success glyph, hide the loading
                            // image
                            $("#successGlyph").show();
                            $("#statusMessage").text("Zip files successfully regenerated!");
                        }
                        else
                        {
                            // On failure, hide the loading image, and instead
                            // show a cross
                            $("#failureGlyph").show();
                            $("#statusMessage").text("An error occurred during regeneration. Try again later.");
                        }
                        
                        // Enable the close buttons
                        $(".close-btn").show();
                    }
                });
            });*/
            
        });
    </script>
    <body>
        
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
              <div class="navbar-header">
                <a class="navbar-brand" href="index.php">Photo Albums</a>
              </div>
              <ul class="nav navbar-nav">
                  <li><a href="index.php">Home</a></li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Photo Albums
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <?php
                        $aAlbums = getAlbums();
                        foreach($aAlbums as $currAlbum)
                        {
                              echo "<li><a href='index.php?album=$currAlbum'>$currAlbum</a></li>";
                        }
                        ?>                      
                    </ul>
                </li>
              </ul>
            </div>
        </nav>
        
        <div class="container">
            <?php
            
                function displayLoginForm()
                {
                    $obForm = new BootstrapForm("login", "admin.php", "POST");
                    $obForm->addPassword("Admin Password", "txtPassword");
                    echo $obForm->dumpForm("Login", "Reset");
                }
                
                function loginAdmin()
                {
                    $_SESSION["isAdmin"] = true;
                    // Display some admin options
                    ?>
                    <h2>Admin Panel</h2>
                    <hr>
                    <h4><a href="javascript:void(0)" id="thumbRegen">Regenerate all thumbnails</a></h4>
                    <!--<h4><a href="javascript:void(0)" id="zipRegen">Regenerate all album zip files</a></h4>-->

                    <?php
                }
            
                
                // Determine if admin session variable is set
                if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"])
                {
                    // User is an admin, show the admin panel
                    loginAdmin();
                }
                else if (!isset($_POST["txtPassword"]))
                {
                    // Show login page, the user is just looking to login
                    displayLoginForm();
                }
                else
                {
                    // Form has been submitted
                    // Check for a valid login
                    if (isset($_POST["txtPassword"]) && hash("sha256", $_POST["txtPassword"]) == HASHED_PASSWORD)
                    {
                        loginAdmin();
                    }
                    else
                    {
                        echo "<h3>Invalid login!</h3>";
                        displayLoginForm();
                    }
                }
            ?>
            <!-- Modal -->
            <div class="modal fade" id="regenModal" role="dialog">
              <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close close-btn" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="#statusHeader">Thumbnail Regeneration</h4>
                  </div>
                  <div class="modal-body">
                      <p id="modalStatus"><img src="css/loading.svg" class="status" id="statusImg"><span id="successGlyph" class="glyphicon glyphicon-ok"></span><span id="failureGlyph" class="glyphicon glyphicon-remove"></span><span id="statusMessage">Regenerating thumbnails...</span></p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default close-btn" data-dismiss="modal">Close</button>
                  </div>
                </div>

              </div>
            </div>
        </div>
    </body>
</html>
