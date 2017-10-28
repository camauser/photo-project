# photo-project
A PHP photo project to create an online collection of photo albums. Takes all images located in the img subdirectory, and divides them into albums based on subdirectories in img/ (however, directories are only traversed one level deep - ex. img/album/subfolder - in this case, 'subfolder' wouldn't be checked for images).

# Pre-Requisites
This project requires the php-gd extension (can be installed with `sudo apt-get install php5-gd` if using Linux with PHP5).

Additionally, the web server service should have read/write/execute permissions on the img directory. If it doesn't, thumbnails won't be successfully generated (and as of now, these errors are NOT visually reported to the user - they fail silently).
