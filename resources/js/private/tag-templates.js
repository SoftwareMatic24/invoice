function IMG_TAG_TEMPLATE(obj, media = null) {
	let attrs = [];

	let {
		id = null,
		classes = [],
		lazyLoading = false,
		imageURL = `${BASE_URL}/assets/10x10-transparent.png`,
		title = null,
		alt = null,
		width = null,
		height = null,
		onclick = null
	} = obj;

	// meta
	let options = null;
	if (media !== null && media.options !== null) {
		options = JSON.parse(media.options);
		if (alt === null) alt = options.alt;
		if (title === null) title = options.title;
	}

	// lazy loading
	if (lazyLoading === true) {
		attrs.push(`src="${BASE_URL}/assets/10x10-transparent.png"`);
		attrs.push(`data-src="${imageURL}"`);
		classes.push('lazy');
	} else attrs.push(`src="${imageURL}"`);


	// events

	if (onclick !== null) {
		attrs.push(`onclick=${onclick}`)
	}

	// essentials
	if (title !== null && title !== undefined) attrs.push(`title="${title}"`);
	if (alt !== null && alt !== undefined) attrs.push(`alt="${alt}"`);
	if (width !== null) attrs.push(`width="${width}"`);
	if (width !== null) attrs.push(`height="${height}"`);

	attrs.push(`class="${classes.join(' ')}"`);

	return `<img ${attrs.join(' ')} />`;
}