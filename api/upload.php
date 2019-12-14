<?php
/*
	Produced 2019
	By https://github.com/amattu2
	Copy Alec M.
	License GNU Affero General Public License v3.0
*/

/*
	API FILE INFORMATION
	For: User
	Description: Handles external file uploads for "/upload/"
	Notes: Non compliant with API Output convention
*/

/*
	API STATUS CONVENTION
	200 - Successful / Good data
	400 - Bad data supplied
	403 - Unauthorized
	410 - Not available / Gone
*/

// Files
require(dirname(__FILE__, 2) ."/assets/php/config.php");
require(dirname(__FILE__, 2) ."/assets/php/mime.php");

// Checks
if (empty($_POST)) { returnData(400, null); }
if (empty($_POST['function'])) { returnData(400, null); }

// Return Function
function returnData($status_code, $data) {
	// Variables
	$result = Array(
		"status_code" => (!empty($status_code) ? $status_code : 200),
		"data" => $data,
		"notice" => ""
	);

	// Headers
	header('Content-Type: application/json');
	http_response_code($result["status_code"]);

	// Data
	echo json_encode($result);

	// End
	die();
}

// Clean input/output
function cleanInput($i) {
	$i = strip_tags($i);
	$i = preg_replace('/[^\00-\255]+/u', '', $i);
	$i = stripslashes($i);
	return $i;
}

// Clean All Non-Numeric
function cleanNA($i) {
	return preg_replace('/[^0-9]/', '', $i);
}

// Clean All Non-AZ
function cleanNC($i) {
	return preg_replace('/\PL/u', '', $i);
}

// Remove All Non-Alphanumeric
function cleanNAN($i) {
	return preg_replace("/(\W)+/", '', $i);
}

// Format File Name
function cleanFileName($str = "") {
	return preg_replace("/[\s_]/", "-", trim($str));
}

// Router
if ($_POST['function'] === "uploadFiles") {
	uploadFiles();
} else {
	returnData(400, null);
}

/*********************************************************************/
/*													 	  		     */
/*			      				 Functions 		 		             */
/*								     								 */
/*********************************************************************/
// File Upload Function
function uploadFiles() {
	// Checks
	if (!isset($_FILES) || empty($_FILES) || !isset($_FILES['uploads']) || empty($_FILES['uploads'])) {
		returnData(400, false);
	}
	if (count($_FILES['uploads']['name']) <= 0) {
		returnData(400, false);
	}

	// Variables
	global $file_upload_max_size, $file_upload_allowed_types, $application_upload_baseurl;
	$count = count($_FILES['uploads']['name']);
	$success = Array();
	$errors = Array();

	// Loops
	for ($i = 0; $i < $count; $i++) {
		// Variables
		$originalName = cleanFileName(strtolower(cleanInput($_FILES['uploads']['name'][$i]))) ?: "";
		$originalType = $_FILES['uploads']['type'][$i];
		$originalSize = $_FILES['uploads']['size'][$i];
		$tempPath = $_FILES['uploads']['tmp_name'][$i];
		$error = $_FILES['uploads']['error'][$i];
		$mime = new MimeReader($tempPath);
		$actualType = $mime->getType() ?: "";
		$actualName = "";
		$actualExtension = "";
		$actualSizeMB = 0;
		$actualSizeB = 0;

		// Checks
		if ($error != 0) { $errors[] = $originalName . " did not upload"; continue; }
		if (!file_exists($tempPath)) { $errors[] = $originalName . " did not upload"; continue; }
		if (!is_uploaded_file($tempPath)) { $errors[] = $originalName . " was not uploaded"; continue; }
		if ($originalSize <= 0 || $originalSize >= $file_upload_max_size) { $errors[] = $originalName . " was too large or empty"; continue; }
		if (!array_key_exists($originalType, $file_upload_allowed_types)) { $errors[] = $originalName . " was a invalid file type"; continue; }
		if (!array_key_exists($actualType, $file_upload_allowed_types)) { $errors[] = $originalName . " was a invalid file type"; continue; }
		if ($actualType !== $originalType) { $errors[] = $originalName . " was a invalid file type"; continue; }

		// Redefine Variables
		$actualName = md5_file($tempPath);
		$actualExtension = $file_upload_allowed_types[$actualType];
		$actualSizeMB = number_format($originalSize / 1048576, 6);
		$actualSizeB = $originalSize ?: 0;

		// Move File, Save Log
		if (move_uploaded_file($tempPath, dirname(__FILE__, 2) ."/uploads/". $actualName . $actualExtension)) {
			$success[] = $application_upload_baseurl . $actualName . $actualExtension;
		} else {
			$errors[] = $originalName . " unknown error";
		}
	}

	// Return
	returnData(200, Array("success" => $success, "error" => $errors));
}
?>
