/*
	Produced 2019
	By https://github.com/amattu2
	Copy Alec M.
	License GNU Affero General Public License v3.0
*/

// Variables
let file_upload_allowed_types = ["application/pdf", "text/plain", "image/jpeg", "image/jpg", "image/png"];
let file_upload_max_size = 2500000; // 2.5mb
let file_max_name = 30;
let ignoreFiles = Array();
let uploadFiles = Array();

// Events
document.getElementById('file-input').onchange = function() { buildPreviews(this) };
document.getElementById('file-wrapper').ondragover = function() { this.classList.add('image-dropping') };
document.getElementById('file-wrapper').ondragleave = function() { this.classList.remove('image-dropping') };
document.getElementById('submit').onclick = function(e) {
	// Variables
	let files = uploadFiles;
	let form = new FormData();
	let request = new XMLHttpRequest();
	form.append("function", "uploadFiles");

	// Checks
	if (!files || files.length <= 0) { return false }

	// Loops
	for (let i = 0; i < files.length; i++) {
		// Variables
		let file = files[i];

		// Checks
		if (typeof(file.type) !== "string" || file_upload_allowed_types.indexOf(file.type) < 0) { continue }
		if (!file || file.length <= 0) { continue }
		if (ignoreFiles.indexOf(file) !== -1) { continue }

		// Append
		form.append("uploads[]", file, file.name);
	}

	// Response
	request.onreadystatechange = function() {
		// Checks
		if (request.readyState !== 4) { return false }
		if (request.status !== 200) {
			alert("Unable to upload file(s). Try again later");
		} else {
			// Variables
			let response = JSON.parse(request.responseText);
			let errors = typeof(response) === "object" && typeof(response.data) === "object" && response.data.error instanceof Array ? response.data.error : Array();
			let message = "";

			// Loops
			errors.forEach(function(str) {
				// Checks
				if (str.length < 0 || str.trim().length < 0) { return false }

				// Variables
				message += str + "\n";
			});

			// Checks
			if (errors.length > 0 && message.length > 0) {
				alert(message);
			}
		}

		// UI
		hideLoader();
		removePreviews();
	}
	request.onprogress = function(e) {
		// Variables
		let progress = parseInt((e.loaded / e.total).toFixed(2) * 100);
		let bar = document.getElementById('progress-bar');

		// Checks
		if (!e.lengthComputable) { bar.dataset.progress = 0; return false }
		if (typeof(e.loaded) !== "number" || typeof(e.total) !== "number") { bar.dataset.progress = 0; return false }
		if (progress <= 0) {
			bar.dataset.progress = "0";
		} else if (progress > 0 && progress <= 99) {
			bar.dataset.progress = progress.toString();
		} else if (bar.dataset.progress > 99) {
			bar.dataset.progress = "100";
		}
	};

	// Submit
	request.open('POST', "api/upload.php");
	request.send(form);
	document.getElementById('progress-bar').dataset.progress = "0";
	showLoader();
	e.preventDefault();
	e.stopPropagation();
};

// Functions
function buildPreviews(input) {
	// Checks
	if (!input.files || input.files.length < 1) { removePreviews(); return false }

	// Variables
	let files = input.files;

	// UI
	document.getElementById('file-previews').innerHTML = '';

	// Loops
	for (let i = 0; i < files.length; i++) {
		// Variables
		let file = files[i];

		// Checks
		if (typeof(file.type) !== "string" || file_upload_allowed_types.indexOf(file.type) < 0) { continue }
		if (!file || file.length <= 0) { continue }
		if (file.type.indexOf("image/") < 0 && (file.type.indexOf("application/pdf") || file.type.indexOf("text/plain"))) {
			// Variables
			let div = document.createElement('div');
			let div2 = document.createElement('div');

			// Attributes
			div.innerHTML = `<div class='file-upload-preview'><span class='file-upload-name'>${file.name && file.name.length > 0 && file.name.length < file_max_name ? file.name.trim() : file.name && file.name.length > file_max_name ? file.name.substr(0, file_max_name) : ""}</span><span class='file-upload-remove'>&times;</span></div>`;
			div2.classList.add('file-upload-notice');
			div2.textContent = '* This file is too large';
			div.getElementsByClassName('file-upload-remove')[0].onclick = function() {
				div.outerHTML = '';
				ignoreFiles.push(file);
			};

			// Append
			if (file.size && file.size > file_upload_max_size) {
				div.appendChild(div2)
			} else {
				uploadFiles.push(file);
			}
			document.getElementById('file-previews').appendChild(div);
		} else if (file.type.indexOf("image/") >= 0) {
			// Variables
			let reader = new FileReader();

			// Events
			reader.onload = function(e) {
				// Variables
				let div = document.createElement('div');
				let image = document.createElement('img');
				let div2 = document.createElement('div');

				// Attributes
				div.innerHTML = `<div class='file-upload-preview'><span class='file-upload-name'>${file.name && file.name.length > 0 && file.name.length < file_max_name ? file.name.trim() : file.name && file.name.length > file_max_name ? file.name.substr(0, file_max_name) : ""}</span><span class='file-upload-remove'>&times;</span></div>`;
				div2.classList.add('file-upload-notice');
				div2.textContent = '* This file will be reduced in size';
				image.alt = "Preview"
				image.classList.add("file-upload-image");
				image.src = e.target.result;
				div.getElementsByClassName('file-upload-remove')[0].onclick = function() {
					div.outerHTML = '';
					ignoreFiles.push(file);
				};

				// Append
				if (file.size && file.size > file_upload_max_size) {
					div.appendChild(div2)
					compressImage(file);
				} else {
					uploadFiles.push(file);
				}
				div.getElementsByClassName('file-upload-preview')[0].insertBefore(image, div.getElementsByClassName('file-upload-preview')[0].children[0]);
				document.getElementById('file-previews').appendChild(div);
			};
			reader.readAsDataURL(file);
		}
	}
}

function compressImage(originalImage, compression = 0.4) {
	// Variables
	let reader = new FileReader();

	// Events
	reader.onload = function(event) {
		// Variables
		let img = new Image();

		// Attributes
		img.src = event.target.result;
		img.onload = function(e) {
			// Variables
			let elem = document.createElement('canvas');
			elem.width = img.width;
			elem.height = img.height;
			let ctx = elem.getContext('2d');

			ctx.drawImage(img, 0, 0, img.width, img.height);
			ctx.canvas.toBlob(function(b) {
				uploadFiles.push(new File([b], originalImage.name, {
					type: 'image/jpeg',
					lastModified: Date.now()
				}));
			}, 'image/jpeg', compression);
		}
	};
	reader.onerror = function(e) {}
	reader.readAsDataURL(originalImage);
}

function removePreviews() {
	uploadFiles = Array();
	ignoreFiles = Array();
	document.getElementById('file-previews').innerHTML = '';
	document.getElementById('file-input').value = "";
}

function hideLoader() {
	// Variables
	let loader = document.getElementsByClassName('loader-bg')[0];

	// Attributes
	loader.style.transition = "all 0.15s ease-in";

	// UI
	loader.classList.remove('visible');
}

function showLoader() {
	// Variables
	let loader = document.getElementsByClassName('loader-bg')[0];

	// Attributes
	loader.style.transition = "all 0s linear";

	// UI
	loader.classList.add('visible');
}
