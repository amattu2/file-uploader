# Introduction
This is a simple file uploader based on PHP (7.2) which implements file mime sniffing, a type whitelist, a maximum file size, and basic security measures. Built on pure JavaScript, HTML, and CSS. No frameworks used.

# Usage
1. Download files
2. Open /assets/php/config.php
3. Configure

# Previews
![preview image](https://github.com/amattu2/file-uploader/blob/master/screenshots/file-selected.png)
![preview image](https://github.com/amattu2/file-uploader/blob/master/screenshots/home-page.png)

# Notes
To produce a simple product, this uploader does not store files outside of the public www/public_html directory. I strongly recommend storing all uploaded files out of public access. GitHub would not upload the .htaccess files for the /upload/ directory, which contained important security features. Please be advised.
