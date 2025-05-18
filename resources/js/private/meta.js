var Meta = function () { };

Meta.prototype.setTabTitle = function (tabTitle = null) {
	if (tabTitle === null || tabTitle === undefined) return;
	document.querySelector('head title').innerHTML = tabTitle;
}

Meta.prototype.setMetaDescription = function (metaDescription = null) {
	if (metaDescription === null || metaDescription === undefined) return;
	document.querySelector('head meta[name="description"]').setAttribute('content', metaDescription);
}

Meta.prototype.setPageMeta = function (obj = null, defaultObj = {}) {
	if (obj === null) obj = defaultObj;
	if (typeof obj === 'string') obj = JSON.parse(obj);

	let { tabTitle = '' } = defaultObj;

	if (obj.tabTitle === null) obj.tabTitle = tabTitle;

	meta.setTabTitle(obj.tabTitle);
	meta.setMetaDescription(obj.metaDescription);
};

var meta = new Meta();