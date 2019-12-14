<?php
/*
	Produced 2019
	By https://github.com/amattu2
	Copy Alec M.
	License GNU Affero General Public License v3.0
*/

// Files
require(dirname(__FILE__, 1) ."/assets/php/config.php");
?>
<!DOCTYPE html>
<html>
	<!--
		Produced 2019
		By https://github.com/amattu2
		Copy Alec M.
		License GNU Affero General Public License v3.0
	-->
	<head>
		<!-- Title, Meta, Etc -->
		<title><?php echo $application_page_title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="robots" content="noindex,nofollow">
		<!-- Styles -->
		<link rel="stylesheet" type="text/css" href="assets/css/upload.css?v=0.01c">
	</head>

	<body class="display-bg">
		<!-- Container Loader -->
		<div class='loader-bg'>
			<div>
				<div class="spinner">
					<div class="rect1"></div>
					<div class="rect2"></div>
					<div class="rect3"></div>
					<div class="rect4"></div>
					<div class="rect5"></div>
				</div>
				<div class='progress-bar' id='progress-bar' data-progress="0"></div>
			</div>
		</div>

		<!-- Container -->
		<div class='container'>
			<div class='header'>
				<div class='header-logo'>
					<div class='logo-wrap'></div>
				</div>
				<div class='header-name'>
					<span><?php echo $application_name; ?></span>
				</div>
			</div>
			<div class='body'>
				<!-- Input Form -->
				<form method="post" novalidate name="formsubmit" onsubmit="return false">
					<div class="input-container">
						<div class="image-upload-wrap" id='file-wrapper'>
							<input class="file-upload-input" id='file-input' type='file' accept="image/png,image/jpeg,image/jpg,application/pdf,text/plain" multiple="true" />
							<div class="drag-text">
								<h3>Drag and drop</h3>
							</div>
						</div>
						<div class="file-upload-content" id='file-previews'></div>
					</div>
					<button id='submit' type="submit" tabindex="3" name="submit">Add Files(s)</button>
				</form>
			</div>
		</div>

		<!-- Scripts -->
		<script src='assets/js/upload.js'></script>
	</body>
</html>
