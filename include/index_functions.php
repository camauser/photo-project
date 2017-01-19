<?php
/*
 * Cam Auser
 * January 18, 2017
 * Photo Project
 */
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

function getPageHeader()
{
    if (!isset($_GET["album"]))
    {
        return "Select an Album";
    }
    else if (!is_dir("img/" . $_GET["album"]))
    {
        return "Invalid Album";
    }
    else
    {
        return $_GET["album"] . " Images";
    }
}

function getAlbums()
{
    $aAlbums = array();
    $dir = new DirectoryIterator("img");
    foreach ($dir as $fileinfo)
    {
        if (!$fileinfo->isDot())
        {
            array_push($aAlbums, $fileinfo->getFilename());

            //echo $fileinfo->getFilename() . "<br />";
        }
    }

    usort($aAlbums, function($a, $b) {
        return strnatcmp($a, $b);
    });

    return $aAlbums;
}

function getAlbumMetaTag()
{
    if (!isset($_GET["album"]) || !is_dir("img/" . $_GET["album"]))
    {
        return "none";
    }
    else
    {
        return $_GET["album"];
    }
}

function validAlbumSelected()
{
    return isset($_GET["album"]) && is_dir("img/" . $_GET["album"]);
}

function listAlbums()
{
    $aAlbums = getAlbums();
    foreach($aAlbums as $currAlbum)
    {
        echo '<h3><a href="index.php?album=' . $currAlbum . '">' . $currAlbum . '</a></h3>';
    }
}