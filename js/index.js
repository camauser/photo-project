/*
 * Cam Auser
 * January 18, 2017
 * Photo Project
 */
function determineButtonStatuses(imgName)
{
    // Make an AJAX call to the backend to determine if this image
    // has previous or next images

    var toSend = {
        type: "PrevNext",
        dir: imgDirectory,
        currImg: imgName
    };

    $.ajax({
        url: "imgservice.php",
        method: "POST",
        data: JSON.stringify(toSend),
        success: function(json)
        {
            // Check to see which buttons should be enabled/disabled
            var result = JSON.parse(json);
            // Set attributes and variables according to values
            // returned
            if (result.hasPrevious)
            {
                // If there's a previous image, ensure the previous
                // button can be clicked. Also set the previous
                // variable
                $("#previous").removeAttr("disabled");
                previous = result.previous;
            }
            else
            {
                // If there's no previous, disable the button
                $("#previous").attr("disabled", "disabled");
            }

            if (result.hasNext)
            {
                // If there's a next, make the next button clickable
                // Also, set the next variable
                $("#next").removeAttr("disabled");
                next = result.next;
            }
            else
            {
                // If there's no next, disable the next button
                $("#next").attr("disabled", "disabled");
            }
        }
    });
}

function moveNext()
{
    // Change the image
    changeImage(next);
}

function movePrevious()
{
    // Just change the image
    changeImage(previous);
}

function changeImage(imgName)
{
    // Some code to display a loading image while the main
    // page is loading
    // Hide the real image, show the loading image
    $("img#modalImage").css("display", "none");
    $("img#loadingImage").css("display", "block");
    // Change the real image source
    $("img#modalImage").attr("src", "img/" + imgDirectory + "/" + imgName);
    $("img#modalImage").on("load", function()
    {
        // When the image has finished loading, display the
        // actual image, and hide the loading image
        $("img#modalImage").css("display", "block");
        $("img#loadingImage").css("display", "none");
    });
    determineButtonStatuses(imgName);
}

$(document).ready(function()
{
    $(".thumbnail").click(function()
    {
        changeImage($(this).attr("id"));
        $("#imgModal").modal("show");
        // Determine whether back or forward buttons should be disabled
        determineButtonStatuses($(this).attr("id"));
    });

    $("#closeModal").click(function()
    {
        $("#previous").removeAttr("disabled");
        $("#next").removeAttr("disabled");
    })

    $("#previous").click(function()
    {
        // Change the image and update the button statuses
        movePrevious();
    });

    $("#next").click(function()
    {
        // Change the image and update button statuses
        moveNext();
    });

    // Functionality for moving forward/back between images
    // with the keys on the keyboard
    $("body").keydown(function(keyInfo)
    {
        // If statement courtesty of:
        // https://stackoverflow.com/questions/19506672/how-to-check-if-bootstrap-modal-is-open-so-i-can-use-jquery-validate
        if (($("#imgModal").data('bs.modal') || {}).isShown)
        {
            var keyCode = keyInfo.originalEvent.code;
            // Determine if forward or back was clicked
            /*
             * Note for these next two cases:
             * If the user is on the first/last image and
             * they click an arrow, the image will just be set
             * to whatever is set in the previous/next variable,
             * which will be the current image. This prevents
             * the user from going off the end/start of the images.
             */
            if (keyCode === "ArrowRight")
            {
                // Forward case
                moveNext();
            }
            else if (keyCode === "ArrowLeft")
            {
                // Backward case
                movePrevious();
            }
            else if (keyCode === "Escape")
            {
                // Close the modal
                $("#previous").removeAttr("disabled");
                $("#next").removeAttr("disabled");
                $("#imgModal").modal("toggle");
                // Change the modal image img source, so we don't
                // continue to load a closed image (what a waste
                // of bandwidth!)
                $("img#modalImage").attr("src", "");
            }
        }
    });
});