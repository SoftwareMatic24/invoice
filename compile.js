// E1
const fs = require('fs');
const CleanCSS = require('clean-css');
const UglifyJS = require('uglify-js');
const path = require("path");

function isMinifiedFileName(fileName) {
	const keywords = ['min', 'compressed', 'minified'];
	const lowerCaseFileName = fileName.toLowerCase();
	return keywords.some(keyword => lowerCaseFileName.includes(keyword));
}

function minifiedFileName(fileName) {
	let chunks = fileName.split('.');
	chunks[0] = `${chunks[0]}.min`;
	return chunks.join('.');
}

function minifyFile(filePath, extension) {
	try {
		let fileName = path.basename(filePath);
		if (path.extname(fileName).toLowerCase() !== extension || isMinifiedFileName(fileName)) return;

		let fileContent = fs.readFileSync(filePath, 'utf8');
		let minifiedContent = '';

		if (extension === '.css') minifiedContent = new CleanCSS().minify(fileContent).styles;
		else if (extension === '.js') minifiedContent = UglifyJS.minify(fileContent).code;

		fs.writeFileSync(minifiedFileName(filePath), minifiedContent);
	}
	catch (e) {
		console.log(e);
	}

}

function watchAndMinify(directory, extension) {

	fs.watch(directory, { recursive: true }, (eventType, fileName) => {
		if (fileName && path.extname(fileName).toLowerCase() === extension && !isMinifiedFileName(fileName)) {
			let filePath = path.join(directory, fileName);
			minifyFile(filePath, extension);
		}
	});
}

try {
	console.log("===Compiling===");
	watchAndMinify("resources/", ".css");
	watchAndMinify("resources/", ".js");
}
catch (e) {
	console.log(e);
}

