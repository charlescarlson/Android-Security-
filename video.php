<button id="back">Back</button>
<?php
error_reporting(E_ALL);
function GetVideos($path) {
    $class = str_replace("/", "_", $path);
    $items = scandir(getcwd() . $path);
    foreach ($items as $item) {
        if ($item !== "." && $item !== "..") {
            $newPath = $path . $item;
            $id = str_replace("/", "_", $newPath);
?>
        <button id="<?= $id ?>_" class="<?= $class ?>" value="<?= $newPath ?>"><?= $item ?></button>
<?php
            if (is_dir(getcwd() . $newPath)) {
                GetVideos($newPath . "/");
            }
        }
    }
}
GetVideos("/video/");
?>
<video id="video" autoplay controls style="display: none;">
</video>
<script>
var buttons = Array.from(document.getElementsByTagName("button"));
var back = null;
changeButtonsDisplay("", "none");
changeButtonsDisplay("_video_", "initial");
back.style.display = "none";
function changeButtonsDisplay(desiredClass, display){
    buttons.forEach(function(e){
        if (e.id === "back") {
            back = e;
            return;
        }
        if (desiredClass === "" || e.classList.contains(desiredClass))
            e.style.display = display;
    });
}
buttons.forEach(function(e){
    if (e.id === "back") {
        e.addEventListener("click", function(e) {
            changeButtonsDisplay("", "none");
            var classes = back.classList;
            var displayClass = classes[classes.length - 1];
            classes.remove(displayClass);
            changeButtonsDisplay(displayClass, "initial");
            if (classes.length < 1)
                back.style.display = "none";
            else
                back.style.display = "initial";
        });
        return;
    }
    e.addEventListener("click", function(e) {
        var el = e.target;
        if (el.value.indexOf("webm") > -1){
            var video = document.getElementById("video");
            video.src = el.value;
            video.style.display = "block";
            video.play();
        }
        changeButtonsDisplay(el.className, "none");
        changeButtonsDisplay(el.id, "initial");
        back.style.display = "initial";
        back.classList.add(el.className);
    });
});
</script>