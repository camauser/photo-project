<?php
/*
 * Cam Auser
 * January 18, 2017
 * Photo Project
 */
// Increase the timeout time if a full thumbnail regeneration is done
// Setting to 900s (15 min) - it takes ~ 10 to 12 minutes for a Pi 2 to regenerate 1.1 GB of images
// Adjust as needed for your project
    session_start();
    ini_set("max_execution_time", 900);

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

function getImages($album)
{
    $aImageNames = array();
    $dir = new DirectoryIterator("img/$album");
        foreach ($dir as $fileinfo) 
        {
            $ext = $fileinfo->getExtension();
            if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp" || $ext == "gif") 
            {
                array_push($aImageNames, $fileinfo->getFilename());
            }
        }

    usort($aImageNames, function($a, $b) { 
        return strnatcmp($a, $b);
    });

    return $aImageNames;
}

function previousImgExists($imgName, $dir)
{
    // Grab all images in directory
    $aImages = getImages($dir);
    // Check to make sure directory exists. If it does, check to ensure
    //  item isn't in index zero
    return imageExists($imgName, $dir) && count($aImages) > 0 && $aImages[0] != $imgName;
}

function nextImgExists($imgName, $dir)
{
    // Grab all images in directory
    $aImages = getImages($dir);
    // Check to make sure directory exists. If it does, check to ensure
    //  item isn't in the last index
    $imgCount = count($aImages);
    return imageExists($imgName, $dir) && $imgCount > 0 && $aImages[$imgCount - 1] != $imgName;
}

function imageExists($imgName, $dir)
{
    // Get all images in the dir and check to see if it contains this image
    return in_array($imgName, getImages($dir));
}

function getPreviousImage($imgName, $dir)
{
    $aImages = getImages($dir);
    // Find the image's index in the array, and return the image before it
    return $aImages[array_search($imgName, $aImages) - 1];
}

function getNextImage($imgName, $dir)
{
    $aImages = getImages($dir);
    // Find the image's index in the array, and return the image after it
    return $aImages[array_search($imgName, $aImages) + 1];
}

function checkThumbnails($aImages, $dir)
{
    // First, create the directory if it doesn't exist
    if (!file_exists("img/$dir/thumb"))
    {
        mkdir("img/$dir/thumb");
    }
    
    // Now loop and check to see if thumbnails exist for each file
    foreach($aImages as $currImg)
    {
        if (!file_exists("img/$dir/thumb/$currImg"))
        {
            genThumbnail($currImg, $dir);
        }
    }
}

function regenerateAllThumbnails()
{
    // Regenerate all the thumbnails according to the albums
    $aAlbums = getAlbums();
    foreach ($aAlbums as $currAlbum)
    {
        //echo "Curr album: $currAlbum <br />";
        if (!file_exists("img/$currAlbum/thumb"))
        {
            mkdir("img/$currAlbum/thumb");
        }
        
        $aImages = getImages($currAlbum);
        
        foreach($aImages as $currImg)
        {
            thumbnailGen($currImg, $currAlbum);
            
        }
        
    }
}

function thumbnailGen($img, $dir)
{
    $desired_width = 400;
    /* read the source image */
    $source_image = imagecreatefromjpeg("img/$dir/$img");
    $width = imagesx($source_image);
    $height = imagesy($source_image);

    /* find the “desired height” of this thumbnail, relative to the desired width  */
    $desired_height = floor($height * ($desired_width / $width));

    /* create a new, “virtual” image */
    $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

    /* copy source image at a resized size */
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

    /* create the physical thumbnail image to its destination */
    imagejpeg($virtual_image, "img/$dir/thumb/$img");
}

function sendZipToClient($dir)
{
    // Recreate zip file everytime, just in case anything has changed since
    // last download, or in case the last execution of this page was interrupted
    if (!file_exists("img/$dir"))
    {
        echo "Invalid album!";
        exit;
    }
    $zip = new ZipArchive();
    $zip->open("img/$dir/$dir.zip", ZipArchive::CREATE || ZipArchive::OVERWRITE);
    $dirIterator = new DirectoryIterator("img/$dir");
    foreach ($dirIterator as $fileinfo) 
    {
        $ext = $fileinfo->getExtension();
        if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp" || $ext == "gif") 
        {
            $zip->addFile($fileinfo->getRealPath(), $fileinfo->getFilename());
        }
    }

    $zip->close();
    // Set headers to tell the user's browser to download the zip file
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime("img/$dir/$dir.zip")) . ' GMT');
    header('Content-Type: application/force-download');
    header('Content-Disposition: inline; filename="' . $dir . '.zip"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize("img/$dir/$dir.zip"));
    header('Connection: close');
    readfile("img/$dir/$dir.zip");
    
    // Some code to make sure that the file is deleted after downloading
    // Source: https://stackoverflow.com/questions/5603851/how-to-create-a-zip-file-using-php-and-delete-it-after-user-downloads-it
    ignore_user_abort(true);
    if (file_exists("img/$dir/$dir.zip"))
    {
        unlink("img/$dir/$dir.zip");
    }
    
}

function regenAllZips()
{
    $aAlbums = getAlbums();
    foreach ($aAlbums as $currAlbum)
    {
        // Regenerate the zip file for the current directory
        $zip = new ZipArchive();
        $zip->open("img/$currAlbum/$currAlbum.zip", ZipArchive::CREATE || ZipArchive::OVERWRITE);
        $dirIterator = new DirectoryIterator("img/$currAlbum");
        foreach ($dirIterator as $fileinfo) 
        {
            $ext = $fileinfo->getExtension();
            if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp" || $ext == "gif") 
            {
                $zip->addFile($fileinfo->getRealPath(), $fileinfo->getFilename());
            }
        }
    }
}

// Main area
// Check to see if access method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $obRequest = json_decode(file_get_contents("php://input"));
    switch($obRequest->type)
    {
        // Previous-Next case: Determine if there are images before or
        // after this image. If so, return their names.
        case "PrevNext":
            
            $obReturn = new stdClass();
            $obReturn->hasPrevious = previousImgExists($obRequest->currImg, $obRequest->dir);
            $obReturn->hasNext = nextImgExists($obRequest->currImg, $obRequest->dir);
            if ($obReturn->hasPrevious)
            {
                $obReturn->previous = getPreviousImage($obRequest->currImg, $obRequest->dir);
            }
            if ($obReturn->hasNext)
            {
                $obReturn->next = getNextImage($obRequest->currImg, $obRequest->dir);
            }
            echo json_encode($obReturn);
            break;
        // All Images case: return the names of all the images in the current
        // directory
        case "AllImages":
            $obReturn = new stdClass();
            $obReturn->aImages = getImages($obRequest->dir);
            // Generate thumbnails for the images if they haven't already
            // been created
            checkThumbnails($obReturn->aImages, $obRequest->dir);
            echo json_encode($obReturn);
            break;
        case "ThumbnailRegen":
            $obReturn = new stdClass();
            $obReturn->regenSucceeded = true;
            try
            {
                // Thumbnail regeneration password is:
                // ZDf9mpdkJFPkVmYMes9fv9Q987QqeJH2
                // We'll check to see if the session key has been set making
                // this user an admin. This is done in admin.php when the admin
                // logs in.
                if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"])
                {
                    // Regenerate all the thumbnails
                    regenerateAllThumbnails();
                    //regenAlt();
                }
                else
                {
                    $obReturn->regenSucceeded = false;
                    $obReturn->reason = "Authentication failed.";
                }
            }
            catch (Exception $e)
            {
                    $obReturn->regenSucceeded = false;
                    $obReturn->reason = "Unknown error has occurred - forward to admin for investigation.";
            }
            echo json_encode($obReturn);
            break;
        case "ZipRegen":
            $obReturn = new stdClass();
            $obReturn->regenSucceeded = true;
            try
            {
                if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"])
                {
                    // Regenerate all zip files
                    regenAllZips();
                }
                else
                {
                    $obReturn->regenSucceeded = false;
                }
            }
            catch (Exception $e)
            {
                    $obReturn->regenSucceeded = false;
            }
            echo json_encode($obReturn);
            break;
    }
}
else if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    if (isset($_GET["album"]))
    {
        sendZipToClient($_GET["album"]);
    }
}
