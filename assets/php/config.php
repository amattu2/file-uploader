<?php
/*
	Produced 2019
	By https://github.com/amattu2
	Copy Alec M.
	License GNU Affero General Public License v3.0
*/

/*
	Environment Notes:
	- Built on PHP 7.2.24
	- Static reference to file_upload_max_size in /js/upload.js
	- Static reference to file_upload_allowed_types in /js/upload.js
*/

// Application
$application_name = "File Uploader";
$application_page_title = "$application_name";
$application_upload_baseurl = "https://example.com/uploads/";

// File Upload Details
$file_upload_max_size = 2500000; // 2.5mb
$file_upload_allowed_types = Array(
	"application/pdf" => ".pdf",
	"text/plain" => ".txt",
	"image/jpeg" => ".jpeg",
	"image/jpg" => ".jpg",
	"image/png" => ".png"
);
?>
