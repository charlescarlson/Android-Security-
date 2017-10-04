<?php
$video = $_FILES["video"];
$location = $_POST["location"];
if (isset($video) && $video["type"] === "video/webm" && isset($location)) {
    $directory = getcwd() . "/video/" . $location . "/" . date("Y/F/dS_l/hA/");
    if (!is_dir($directory))
        mkdir($directory, 0777, true);
    move_uploaded_file($video["tmp_name"], $directory . date("i") . ".webm");
}