"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
;
class MediaCenter {
}
// Overlay
MediaCenter.prototype.initOverlay = function () {
    let overlay = document.querySelector('.media-center-overlay');
    if (overlay === null)
        return;
    overlay.addEventListener('click', function () {
        mediaCenter.hide();
    });
};
MediaCenter.prototype.showOverlay = function () {
    let overlay = document.querySelector('.media-center-overlay');
    if (overlay !== null)
        overlay.classList.add('active');
};
MediaCenter.prototype.hideOverlay = function () {
    let overlay = document.querySelector('.media-center-overlay');
    if (overlay !== null)
        overlay.classList.remove('active');
};
// Tabs
MediaCenter.prototype.initTabs = function () {
    let tabs = document.querySelectorAll('.media-center-tabs > li');
    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            let target = event.target;
            let name = target.dataset.name;
            mediaCenter.selectTab(name);
        });
    });
};
MediaCenter.prototype.selectTab = function (tabName) {
    let tabs = document.querySelectorAll('.media-center-tabs > li');
    let contentDivs = document.querySelectorAll('.media-center-tabs-content > div');
    tabs.forEach((tab) => {
        let element = tab;
        if (element.dataset.name === tabName) {
            element.classList.add('active');
        }
        else {
            element.classList.remove('active');
        }
    });
    contentDivs.forEach((div) => {
        let element = div;
        if (element.dataset.name === tabName + '-content')
            element.classList.add('active');
        else
            element.classList.remove('active');
    });
    if (tabName === 'media')
        mediaCenter.populateMedia(mediaCenter.selectedFolderId);
    //@ts-ignore
    handleLazyLoadImages();
    mediaCenter.hideHeaderToolbar();
};
// Upload Box
MediaCenter.prototype.initUploadBox = function () {
    let uploadBox = document.querySelector('.media-center-upload-box');
    if (uploadBox === null)
        return;
    let uploadButton = uploadBox.querySelector('[data-is="upload-button"]');
    let inputFile = uploadBox.querySelector('input[type="file"]');
    //open upload files
    if (uploadButton !== null) {
        uploadButton.addEventListener('click', function () {
            if (inputFile !== null) {
                inputFile.click();
                if (!inputFile.hasAttribute('on-change-event') || inputFile.getAttribute('on-change-event') !== 'true') {
                    inputFile.setAttribute('on-change-event', 'true');
                    inputFile.addEventListener('change', function (event) {
                        event.preventDefault();
                        mediaCenter.upload(inputFile.files);
                    });
                }
            }
        });
    }
    // drag-and-drop functions
    function dragStart(event) {
        event.preventDefault();
        if (uploadBox !== null)
            uploadBox.classList.add('highlight');
    }
    function dragLeave(event) {
        event.preventDefault();
        if (uploadBox !== null)
            uploadBox.classList.remove('highlight');
    }
    function dragOver(event) {
        event.preventDefault();
    }
    function drop(event) {
        var _a;
        event.preventDefault();
        dragLeave(event);
        let files = (_a = event.dataTransfer) === null || _a === void 0 ? void 0 : _a.files;
        mediaCenter.upload(files);
    }
    uploadBox.addEventListener('dragenter', dragStart);
    uploadBox.addEventListener('dragleave', dragLeave);
    uploadBox.addEventListener('dragover', dragOver);
    uploadBox.addEventListener('drop', drop);
};
MediaCenter.prototype.showUploadProgress = function () {
    let progressBox = document.querySelector('.media-center-progress-box');
    if (progressBox !== null)
        progressBox.classList.add('active');
};
MediaCenter.prototype.hideUploadProgress = function () {
    let progressBox = document.querySelector('.media-center-progress-box');
    let progressList = document.querySelector('.media-center-progress-list');
    let progressCount = document.querySelector('.media-center-progress-box-count');
    if (progressBox !== null)
        progressBox.classList.remove('active');
    if (progressList !== null)
        progressList.innerHTML = '';
    if (progressCount !== null)
        progressCount.innerHTML = 'Files: 0';
};
MediaCenter.prototype.upload = function (files) {
    let uploadCompleteText = this.texts.uploadFinished;
    if (mediaCenter.selectedFolderId === null) {
        mediaCenter.notification.show({
            classes: ['fail'],
            text: this.texts.selectFolderToUpload
        });
        return;
    }
    mediaCenter.showUploadProgress();
    let allPromise = [];
    let uids = [];
    for (const file of files) {
        //@ts-ignore
        let id = 'media-center-upload-' + uid();
        uids.push(id);
        //@ts-ignore
        let fileSize = formatFileSize(file.size);
        let formData = new FormData();
        formData.append('file', file);
        formData.append('fileSize', fileSize);
        formData.append('folderId', mediaCenter.selectedFolderId.toString());
        formData.append('storage', mediaCenter.storage);
        if (mediaCenter.folder !== undefined && mediaCenter.folder !== null)
            formData.append('folder', mediaCenter.folder);
        let p = this.xhrRequest({
            id: id,
            url: this.url + '/upload',
            method: 'POST',
            body: formData,
            stringify: false,
            headers: {}
        });
        allPromise.push(p);
    }
    mediaCenter.populateUploadProgress(files, uids);
    const intervalId = setInterval(function () {
        return __awaiter(this, void 0, void 0, function* () {
            let anyProgressMatched = false;
            //@ts-ignore
            let state = xhrState;
            for (const key in state) {
                if (key.includes('media-center-upload-')) {
                    let individualState = state[key];
                    let progress = individualState.progress !== null ? individualState.progress : 0;
                    let hasError = individualState.error === true ? true : false;
                    let progressElement = document.querySelector(`[data-uid="${key}"]`);
                    if (progressElement !== null && progress < 100) {
                        progressElement.setAttribute('style', `--complete:${progress}%`);
                        anyProgressMatched = true;
                    }
                    else if (progressElement !== null && progress >= 100) {
                        progressElement.setAttribute('style', `--complete:${progress}%`);
                        progressElement.removeAttribute('data-uid');
                        progressElement.classList.add('success');
                    }
                    if (hasError === true && progressElement !== null) {
                        progressElement.setAttribute('style', `--complete:100%`);
                        progressElement.removeAttribute('data-uid');
                        progressElement.classList.add('fail');
                    }
                }
            }
            if (anyProgressMatched === false) {
                clearInterval(intervalId);
                mediaCenter.hideUploadProgress();
                mediaCenter.eloader.show(document.querySelector('.media-center'));
                yield mediaCenter.fetchFolders();
                mediaCenter.eloader.hide(document.querySelector('.media-center'));
                mediaCenter.selectTab('media');
                mediaCenter.notification.show({
                    classes: ['success'],
                    text: uploadCompleteText
                });
            }
        });
    }, 1000);
};
MediaCenter.prototype.populateUploadProgress = function (files, uids) {
    let progressCount = document.querySelector('.media-center-progress-box-count');
    let progressList = document.querySelector('.media-center-progress-list');
    let layouts = [];
    let fileIndex = 0;
    for (const file of files) {
        //@ts-ignore
        let size = formatFileSize(file.size);
        let layout = `
			<li style="--complete:0%" data-uid="${uids[fileIndex]}">
				<span class="icon-container">
					${this.icons.solidGenericFile}
					<span class="extension">${file.name.split(".").pop()}</span>
				</span>
				<span class="info">
					<span class="name">${file.name}</span>
					<span class="size">${size}</span>
				</span>
			</li>
		`;
        layouts.push(layout);
        fileIndex++;
    }
    if (progressList !== null)
        progressList.insertAdjacentHTML('afterbegin', layouts.join(''));
    let progressListItems = document.querySelectorAll('.media-center-progress-list li');
    if (progressCount !== null)
        progressCount.innerHTML = `Files: ${progressListItems.length}`;
};
// Folders
MediaCenter.prototype.initFolders = function () {
    let newFolderButton = document.querySelector('.media-center .new-folder-button');
    let newFolderCancelButton = document.querySelector('.media-center .new-folder-container [data-is="cancel-button"]');
    let newFolderForm = document.querySelector('.media-center .new-folder-container .new-folder-form');
    let folderNameInput = document.querySelector('.media-center .new-folder-container [name="folder-name"]');
    if (newFolderButton !== null) {
        newFolderButton.addEventListener('click', function (event) {
            event.preventDefault();
            mediaCenter.showNewFolderSection();
        });
    }
    if (newFolderCancelButton !== null) {
        newFolderCancelButton.addEventListener('click', function (event) {
            event === null || event === void 0 ? void 0 : event.preventDefault();
            mediaCenter.hideNewFolderSection();
        });
    }
    if (newFolderForm !== null) {
        newFolderForm.addEventListener('submit', function (event) {
            return __awaiter(this, void 0, void 0, function* () {
                event.preventDefault();
                let response = yield mediaCenter.saveFolder({ folderName: folderNameInput.value });
                if (response.data.status === 'success') {
                    mediaCenter.hideNewFolderSection();
                    mediaCenter.eloader.show(document.querySelector('.media-center'));
                    let foldersResponse = yield mediaCenter.fetchFolders();
                    mediaCenter.populateFolders(foldersResponse.data);
                    mediaCenter.eloader.hide(document.querySelector('.media-center'));
                }
            });
        });
    }
};
MediaCenter.prototype.showNewFolderSection = function () {
    let newFolderContainer = document.querySelector('.media-center .new-folder-container');
    if (newFolderContainer !== null)
        newFolderContainer.classList.add('active');
};
MediaCenter.prototype.hideNewFolderSection = function () {
    let folderNameInput = document.querySelector('.media-center .new-folder-container [name="folder-name"]');
    let newFolderContainer = document.querySelector('.media-center .new-folder-container');
    if (newFolderContainer !== null)
        newFolderContainer.classList.remove('active');
    if (folderNameInput !== null)
        folderNameInput.value = '';
};
MediaCenter.prototype.fetchFolders = function () {
    return __awaiter(this, void 0, void 0, function* () {
        let response = yield this.xhrRequest({
            url: mediaCenter.url + '/folders/all',
            method: 'GET'
        });
        mediaCenter.folders = response.data;
        return response;
    });
};
MediaCenter.prototype.saveFolder = function (data) {
    return __awaiter(this, void 0, void 0, function* () {
        let saveButton = document.querySelector('.media-center .new-folder-container [data-is="save-button"]');
        let folderNameInput = document.querySelector('.media-center .new-folder-container [name="folder-name"]');
        if (saveButton !== null)
            saveButton.setAttribute('disabled', 'true');
        let n = this.notification.show({
            text: this.texts.saving,
            time: 0
        });
        //@ts-ignore
        let response = yield xhrRequest({
            url: this.url + '/folders/save',
            method: 'POST',
            body: data
        });
        this.notification.hideAndShowDelayed(n.data.id, {
            classes: [response.data.status],
            text: response.data.msg
        });
        if (saveButton !== null)
            saveButton.removeAttribute('disabled');
        if (response.data.status === 'success')
            folderNameInput.value = '';
        return response;
    });
};
MediaCenter.prototype.deleteFolder = function (folderId) {
    return __awaiter(this, void 0, void 0, function* () {
        let deleteFolderTitle = this.texts.deleteFolder;
        this.confirmation.show({
            title: deleteFolderTitle,
            positiveButton: {
                function: function () {
                    return __awaiter(this, void 0, void 0, function* () {
                        mediaCenter.eloader.show(document.querySelector('.media-center'));
                        let response = yield mediaCenter.xhrRequest({
                            url: mediaCenter.url + '/folders/delete/' + folderId,
                            method: 'DELETE'
                        });
                        mediaCenter.notification.show({
                            classes: [response.data.status],
                            text: response.data.msg
                        });
                        if (response.data.status === 'success') {
                            yield mediaCenter.fetchFolders();
                            mediaCenter.populateFolders(mediaCenter.folders);
                            mediaCenter.hideAside();
                        }
                        mediaCenter.eloader.hide(document.querySelector('.media-center'));
                    });
                }
            }
        });
    });
};
MediaCenter.prototype.updateFolder = function () {
    return __awaiter(this, void 0, void 0, function* () {
        let folderTitleInput = document.querySelector('.media-center-aside form [name="folder-title"]');
        if (folderTitleInput === null)
            return;
        let folderName = folderTitleInput.value;
        let postData = {
            folderName: folderName,
            id: mediaCenter.selectedFolderId
        };
        let response = yield mediaCenter.saveFolder(postData);
        if (response.data.status === 'success') {
            mediaCenter.folders.forEach((folder) => {
                if (folder.id == mediaCenter.selectedFolderId)
                    folder.title = folderName;
            });
            mediaCenter.populateFolders(mediaCenter.folders);
            mediaCenter.selectFolder(mediaCenter.selectedFolderId);
        }
    });
};
MediaCenter.prototype.populateFolders = function (folders) {
    let sidebarList = document.querySelector('.media-center-sidebar-list');
    if (sidebarList === null)
        return;
    let layouts = folders.map((folder) => `<li data-folder-id="${folder.id}" onclick="mediaCenter.selectFolder(${folder.id})">${folder.shared == true ? this.icons.solidCloud : this.icons.solidFolder} ${folder.title}</li>`);
    sidebarList.innerHTML = layouts.join('');
};
MediaCenter.prototype.selectFolder = function (folderId) {
    let sidebarListOptions = document.querySelectorAll('.media-center-sidebar-list li');
    let selectedTab = document.querySelector('.media-center-tabs li.active');
    let isMediaTabSelected = false;
    if (selectedTab !== null && selectedTab.dataset.name === 'media')
        isMediaTabSelected = true;
    sidebarListOptions.forEach((option) => {
        let element = option;
        if (element.dataset.folderId == folderId && element.classList.contains('active')) {
            element.classList.remove('active');
            mediaCenter.closeFolder(folderId);
            mediaCenter.selectedFolderId = null;
            if (isMediaTabSelected === true)
                mediaCenter.selectTab('media');
        }
        else if (element.dataset.folderId == folderId && !element.classList.contains('active')) {
            element.classList.add('active');
            mediaCenter.openFolder(folderId);
            mediaCenter.selectedFolderId = folderId;
            if (isMediaTabSelected === true)
                mediaCenter.selectTab('media');
        }
        else
            element.classList.remove('active');
    });
    mediaCenter.hideHeaderToolbar();
};
MediaCenter.prototype.openFolder = function (folderId) {
    let folder = mediaCenter.folders.find((f) => f.id == folderId);
    mediaCenter.showAside('folder', folder);
};
MediaCenter.prototype.closeFolder = function (folderId) {
    mediaCenter.hideAside();
};
// Media
MediaCenter.prototype.findMedia = function (mediaId) {
    let media = null;
    mediaCenter.folders.forEach((folder) => {
        folder.media.forEach((m) => {
            if (m.id == mediaId)
                media = m;
        });
    });
    return media;
};
MediaCenter.prototype.populateMedia = function (folderId) {
    let mediaContainer = document.querySelector('.media-center-media-container');
    let folders = mediaCenter.folders;
    if (folderId !== null)
        folders = folders.filter((folder) => folder.id == folderId);
    let layouts = [];
    folders.map((folder) => {
        folder.media.map((media) => {
            let layout = ``;
            let chunks = media.url.split('.');
            let chunks2 = media.url.split('/');
            let extension = chunks[chunks.length - 1];
            let fileNameOnly = chunks2[chunks2.length - 1];
            fileNameOnly = fileNameOnly.replace('.' + extension, '');
            if (media.type !== null && media.type.toLowerCase().includes('image/') && media.type !== 'image/vnd.adobe.photoshop') {
                //@ts-ignore
                let displayImageURL = BASE_URL + '/storage/' + media.url;
                //@ts-ignore
                if (media.thumbnail !== null)
                    displayImageURL = BASE_URL + '/storage/' + media.thumbnail;
                //@ts-ignore
                if (media.private == 1)
                    displayImageURL = BASE_URL + '/assets/private-120x120.jpg';
                //@ts-ignore
                layout = `<div data-media-id="${media.id}" data-media-type="${media.type}" class="item-container" onclick="mediaCenter.selectMedia(${media.id})"><img class="item-image | lazy" src="${BASE_URL}/assets/10x10-transparent.png" data-src="${displayImageURL}" /></div>`;
            }
            else if (media.type !== null && media.type.toLowerCase().includes('video/')) {
                //@ts-ignore
                let videoURL = BASE_URL + '/storage/' + media.url;
                layout = `
					<div data-media-id="${media.id}" data-media-type="${media.type}" class="item-container" onclick="mediaCenter.selectMedia(${media.id})">
						<video class="item-video">
							<source src="${videoURL}" type="${media.type}" />
						</video>
						<span class="item-sub">Video: ${fileNameOnly}</span>
					</div>`;
                //@ts-ignore
                if (media.private == 1)
                    layout = `<div data-media-id="${media.id}" data-media-type="${media.type}" class="item-container" onclick="mediaCenter.selectMedia(${media.id})"><img class="item-image | lazy" src="${BASE_URL}/assets/10x10-transparent.png" data-src="${BASE_URL}/assets/private-120x120.jpg" /><span class="item-sub">Video: ${fileNameOnly}</span></div>`;
            }
            else {
                //@ts-ignore
                let displayImageURL = mediaCenter.images.plain120x120;
                layout = `<div data-media-id="${media.id}" data-media-type="${media.type}" class="item-container" onclick="mediaCenter.selectMedia(${media.id})">
							<img class="item-image" src="${displayImageURL}" />
							
							<span class="item-sub">${extension}: ${fileNameOnly}</span>
						</div>`;
            }
            layouts.push(layout);
        });
    });
    if (mediaContainer !== null)
        mediaContainer.innerHTML = layouts.join('');
};
MediaCenter.prototype.selectMedia = function (mediaId) {
    let mediaElement = document.querySelector(`[data-media-id="${mediaId}"]`);
    if (mediaElement === null)
        return;
    if (mediaElement.classList.contains('active')) {
        //de-select
        mediaElement.classList.remove('active');
        mediaCenter.hideAside();
    }
    else {
        //select
        let media = mediaCenter.findMedia(mediaId);
        mediaElement.classList.add('active');
        mediaCenter.showAside(media.type, media);
    }
    let selectdMediaIds = mediaCenter.getSelectedMediaIds();
    if (selectdMediaIds.length > 0)
        mediaCenter.showHeaderToolbar();
    else
        mediaCenter.hideHeaderToolbar();
};
MediaCenter.prototype.getSelectedMediaIds = function () {
    let ids = [];
    let selectedItems = document.querySelectorAll('.media-center-media-container .item-container.active');
    selectedItems.forEach((item) => {
        let mediaId = item.dataset.mediaId;
        if (mediaId !== undefined)
            ids.push(mediaId);
    });
    return ids;
};
MediaCenter.prototype.saveImage = function (mediaId) {
    return __awaiter(this, void 0, void 0, function* () {
        let idInput = document.querySelector('.media-center-aside form input[name="id"]');
        let nameInput = document.querySelector('.media-center-aside form input[name="name"]');
        let titleInput = document.querySelector('.media-center-aside form input[name="title"]');
        let altInput = document.querySelector('.media-center-aside form input[name="alt"]');
        let postData = {
            id: mediaId,
            mediaId: idInput !== null ? idInput.value : '',
            name: nameInput !== null ? nameInput.value : '',
            title: titleInput !== null ? titleInput.value : '',
            alt: altInput !== null ? altInput.value : ''
        };
        mediaCenter.eloader.show(document.querySelector('.media-center'));
        let response = yield mediaCenter.xhrRequest({
            method: 'POST',
            url: mediaCenter.url + '/save-media',
            body: postData
        });
        yield mediaCenter.fetchFolders();
        mediaCenter.eloader.hide(document.querySelector('.media-center'));
        mediaCenter.notification.show({
            classes: [response.data.status],
            text: response.data.msg
        });
        if (response.data.status === 'success') {
            let media = mediaCenter.findMedia(mediaId);
            mediaCenter.showAside(media.type, media);
        }
    });
};
MediaCenter.prototype.saveVideo = function (mediaId) {
    return __awaiter(this, void 0, void 0, function* () {
        let idInput = document.querySelector('.media-center-aside form input[name="id"]');
        let nameInput = document.querySelector('.media-center-aside form input[name="name"]');
        let titleInput = document.querySelector('.media-center-aside form input[name="title"]');
        let postData = {
            id: mediaId,
            mediaId: idInput !== null ? idInput.value : '',
            name: nameInput !== null ? nameInput.value : '',
            title: titleInput !== null ? titleInput.value : ''
        };
        mediaCenter.eloader.show(document.querySelector('.media-center'));
        let response = yield mediaCenter.xhrRequest({
            method: 'POST',
            url: mediaCenter.url + '/save-media',
            body: postData
        });
        yield mediaCenter.fetchFolders();
        mediaCenter.eloader.hide(document.querySelector('.media-center'));
        mediaCenter.notification.show({
            classes: [response.data.status],
            text: response.data.msg
        });
        if (response.data.status === 'success') {
            let media = mediaCenter.findMedia(mediaId);
            mediaCenter.showAside(media.type, media);
        }
    });
};
MediaCenter.prototype.moveMediaToFolder = function () {
    return __awaiter(this, void 0, void 0, function* () {
        let mediaIds = mediaCenter.getSelectedMediaIds();
        let folderList = document.querySelector('.media-center-aside form [name="move-folders"]');
        let toFolderId = '-1';
        let toFolderText = '';
        let fromFolderText = '';
        if (folderList !== null) {
            toFolderId = folderList.value;
            toFolderText = folderList.options[folderList.selectedIndex].text;
        }
        let fromFolder = mediaCenter.folders.find((folder) => folder.id == mediaCenter.selectedFolderId);
        if (fromFolder !== undefined)
            fromFolderText = fromFolder === null || fromFolder === void 0 ? void 0 : fromFolder.title;
        if (toFolderId == '-1') {
            this.notification.show({
                classes: ['fail'],
                text: 'Please select a destination folder.'
            });
            mediaCenter.showAside('moveMedia');
            return;
        }
        this.confirmation.show({
            title: 'Move to Folder',
            description: `You are going to move all media files from ${fromFolderText} to ${toFolderText}.`,
            positiveButton: {
                text: 'Move',
                classes: ['button button-warning button-sm'],
                function: function () {
                    return __awaiter(this, void 0, void 0, function* () {
                        mediaCenter.eloader.show(document.querySelector('.media-center'));
                        let response = yield mediaCenter.xhrRequest({
                            url: mediaCenter.url + '/move',
                            method: 'POST',
                            body: {
                                fromFolderId: mediaCenter.selectedFolderId,
                                toFolderId: toFolderId,
                                mediaIds: mediaIds
                            }
                        });
                        mediaCenter.notification.show({
                            classes: [response.data.status],
                            text: response.data.msg
                        });
                        if (response.data.status === 'success') {
                            yield mediaCenter.fetchFolders();
                            mediaCenter.selectFolder(toFolderId);
                        }
                        mediaCenter.eloader.hide(document.querySelector('.media-center'));
                    });
                }
            }
        });
    });
};
MediaCenter.prototype.deleteSelectedMedia = function () {
    mediaCenter.confirmation.show({
        title: 'Delete selected items',
        description: 'You are going to delete selected items. Deleted items can not be recovered.',
        positiveButton: {
            function: function () {
                return __awaiter(this, void 0, void 0, function* () {
                    mediaCenter.eloader.show(document.querySelector('.media-center'));
                    let response = yield mediaCenter.xhrRequest({
                        url: mediaCenter.url + '/delete',
                        method: 'DELETE',
                        body: {
                            ids: mediaCenter.getSelectedMediaIds()
                        }
                    });
                    if (response.data.status === 'success') {
                        yield mediaCenter.fetchFolders();
                        mediaCenter.selectTab('media');
                        mediaCenter.hideHeaderToolbar();
                    }
                    mediaCenter.eloader.hide(document.querySelector('.media-center'));
                    mediaCenter.notification.show({
                        classes: [response.data.status],
                        text: response.data.msg
                    });
                });
            }
        }
    });
};
// Aside
MediaCenter.prototype.showAside = function (asideType = 'folder', data = null) {
    let mediaCenterMainBody = document.querySelector('.media-center-main-body');
    let mediaCenterAside = document.querySelector('.media-center-aside');
    if (mediaCenterMainBody !== null)
        mediaCenterMainBody.classList.add('aside-open');
    if (mediaCenterAside === null)
        return;
    let layout = ``;
    if (asideType.includes('image/') && data !== null && data.type !== 'image/vnd.adobe.photoshop') {
        //@ts-ignore
        let displayImageURL = BASE_URL + '/storage/' + data.url;
        //@ts-ignore
        if (data.thumbnail !== null)
            displayImageURL = BASE_URL + '/storage/' + data.thumbnail;
        //@ts-ignore
        let imageURL = BASE_URL + '/storage/' + data.url;
        let imageName = imageURL.split('/').pop();
        let imageNameOnly = imageName === null || imageName === void 0 ? void 0 : imageName.split('.')[0];
        let options = data.options;
        if (options !== null)
            options = JSON.parse(options);
        else
            options = {};
        let isAvatar = false;
        if (imageURL !== null && imageURL.includes('/avatars/avatar'))
            isAvatar = true;
        layout = `
			<div>
				<img class="media-center-aside-thumbnail | ${data.private == 1 ? 'hide' : ''}" src="${displayImageURL}" alt="thumbnail">
				<p class="media-center-aside-text | ellipsis">${this.texts.file}: <span data-is="file-name">${imageName}</span></p>
				<p class="media-center-aside-text">${this.texts.size}: ${options.size !== undefined ? options.size : ''}</p>
				<p class="media-center-aside-text">${this.texts.url}: <a target="_blank" href="${imageURL}">open</a></p>
			</div>
			
			<div class="margin-top-2">
				<form onsubmit="return false;" action="#">
					<div class="form-group">
						<label class="input-style-1-label">ID:</label>
						<input name="id" type="text" class="input-style-1" placeholder="ID" value="${options.mediaId !== undefined ? options.mediaId : ''}">
					</div>
					<div class="form-group | ${isAvatar === true ? 'hide' : ''}">
						<label class="input-style-1-label">Name:</label>
						<input name="name" type="text" class="input-style-1" placeholder="Image name" value="${imageNameOnly}">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">Title text:</label>
						<input name="title" type="text" class="input-style-1" placeholder="Title" value="${options.title !== undefined ? options.title : ''}">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">alt text:</label>
						<input name="alt" type="text" class="input-style-1" placeholder="alt text" value="${options.alt !== undefined ? options.alt : ''}">
					</div>
					<div class="form-group">
						<button onclick="mediaCenter.saveImage(${data.id})" class="button button-block button-primary">Save</button>
					</div>
				</form>
			</div>
		`;
    }
    else if (asideType.includes('video/') && data !== null) {
        //@ts-ignore
        let videoURL = BASE_URL + '/storage/' + data.url;
        //@ts-ignore
        if (data.private == 1)
            videoURL = BASE_URL + '/private-storage?file=' + data.url;
        let videoName = videoURL.split('/').pop();
        let videoNameOnly = videoName === null || videoName === void 0 ? void 0 : videoName.split('.')[0];
        let options = data.options;
        if (options !== null)
            options = JSON.parse(options);
        else
            options = {};
        layout = `
			<div>
				<video class="media-center-aside-video | ${data.private == 1 ? 'hide' : ''}" controls>
					<source src="${videoURL}" type="${data.type}" />
				</video>
				<p class="media-center-aside-text ellipsis">${this.texts.file}: <span data-is="file-name">${videoName}</span></p>
				<p class="media-center-aside-text">${this.texts.file}: ${options.size !== undefined ? options.size : ''}</p>
				<p class="media-center-aside-text">${this.texts.url}: <a target="_blank" href="${videoURL}">${this.texts.playVideo}</a></p>
			</div>
			
			<div class="margin-top-2">
				<form onsubmit="return false;" action="#">
					<div class="form-group">
						<label class="input-style-1-label">ID:</label>
						<input name="id" type="text" class="input-style-1" placeholder="id" value="${options.mediaId !== undefined ? options.mediaId : ''}">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">Name:</label>
						<input name="name" type="text" class="input-style-1" placeholder="Video name" value="${videoNameOnly}">
					</div>
					<div class="form-group">
						<label class="input-style-1-label">Title text:</label>
						<input name="title" type="text" class="input-style-1" placeholder="Title" value="${options.title !== undefined ? options.title : ''}">
					</div>
					<div class="form-group">
						<button onclick="mediaCenter.saveVideo(${data.id})" class="button button-block button-primary">${this.texts.save}</button>
					</div>
				</form>
			</div>
		`;
    }
    else if (asideType === 'folder' && data !== null) {
        let folderOptionsLayouts = mediaCenter.folders.filter((folder) => folder.id != data.id).map((folder) => `<option value="${folder.id}">${folder.title}</option>`);
        layout = `
			<div>
				<p class="media-center-aside-text">${this.texts.folder}: <span data-is="folder-tite">${data.title}<span></p>
				<p class="media-center-aside-text">${this.texts.items}: ${data.media.length}</p>
			</div>
			<div class="margin-top-2">
				<form onsubmit="return false;" action="#">
					<div class="form-group">
						<input name="folder-title" type="text" class="input-style-1" placeholder="${this.texts.folderName}" value="${data.title}">
					</div>
					<div class="form-group">
						<button onClick="mediaCenter.updateFolder()" type="button" class="button button-block button-primary">${this.texts.update}</button>
					</div>
					<div class="form-group">
						<p class="media-center-aside-text">${this.texts.moveFolderItemsInfo}</p>
					</div>
					<div class="form-group">
						<select name="move-folders" class="input-style-1">
							<option value="-1">${this.texts.chooseFolder}</option>
							${folderOptionsLayouts.join('')}
						</select>
					</div>
					<div class="form-group">
						<button onclick="mediaCenter.moveMediaToFolder()" type="button" class="button button-block">${this.texts.move}</button>
					</div>
					<div class="form-group">
						<div class="section-divider" style="--width:1rem">or</div>
					</div>
					<div class="form-group | margin-top-0">
						<button onclick="mediaCenter.deleteFolder(${data.id})" type="button" class="button button-block button-danger">${this.texts.deleteFolder}</button>
					</div>
				</form>
			</div>
		`;
    }
    else if (asideType === 'moveMedia') {
        let folderOptionsLayouts = mediaCenter.folders.map((folder) => `<option value="${folder.id}">${folder.title}</option>`);
        layout = `
		<div>
			<form onsubmit="return false;" action="#">
				
				
				<div class="form-group">
					<p class="media-center-aside-text">
						${this.texts.moveFolderItemsInfo}
					</p>
				</div>
				<div class="form-group">
					<select name="move-folders" class="input-style-1">
						<option value="-1">Choose folder</option>
						${folderOptionsLayouts.join('')}
					</select>
				</div>
				<div class="form-group">
					<button onclick="mediaCenter.moveMediaToFolder()" type="button" class="button button-primary button-block">${this.texts.move}</button>
				</div>
			</form>
		</div>
		`;
    }
    else {
        //@ts-ignore
        let displayImageURL = mediaCenter.images.defaultImage300x158;
        //@ts-ignore
        let fileURL = BASE_URL + '/storage/' + data.url;
        let fileName = fileURL.split('/').pop();
        let fileNameOnly = fileName === null || fileName === void 0 ? void 0 : fileName.split('.')[0];
        let options = data.options;
        if (options !== null)
            options = JSON.parse(options);
        else
            options = {};
        layout = `
			<div>
				<img class="media-center-aside-thumbnail" src="${displayImageURL}" alt="thumbnail">
				<p class="media-center-aside-text ellipsis">${this.texts.file}: <span data-is="file-name">${fileName}</span></p>
				<p class="media-center-aside-text">${this.texts.size}: ${options.size !== undefined ? options.size : ''}</p>
				<p class="media-center-aside-text">${this.texts.url}: <a target="_blank" href="${fileURL}">${this.texts.open}</a></p>
			</div>
			
			<div class="margin-top-2">
				<form onsubmit="return false;" action="#">
					<div class="form-group">
						<label class="input-style-1-label">Name:</label>
						<input name="name" type="text" class="input-style-1" placeholder="Image name" value="${fileNameOnly}">
					</div>
					<div class="form-group">
						<button onclick="mediaCenter.saveImage(${data.id})" class="button button-block button-primary">${this.texts.save}</button>
					</div>
				</form>
			</div>
		`;
    }
    mediaCenterAside.innerHTML = layout;
};
MediaCenter.prototype.hideAside = function () {
    let mediaCenterMainBody = document.querySelector('.media-center-main-body');
    if (mediaCenterMainBody !== null)
        mediaCenterMainBody.classList.remove('aside-open');
};
// Header Toolbar
MediaCenter.prototype.showHeaderToolbar = function () {
    let toolbar = document.querySelector('.media-center-header-toolbar-list');
    if (toolbar === null)
        return;
    toolbar.classList.add('active');
};
MediaCenter.prototype.hideHeaderToolbar = function () {
    let toolbar = document.querySelector('.media-center-header-toolbar-list');
    if (toolbar === null)
        return;
    toolbar.classList.remove('active');
};
MediaCenter.prototype.onUse = function () {
    let errors = [];
    let max = mediaCenter.useAs.max !== undefined ? mediaCenter.useAs.max : '';
    let mediaType = mediaCenter.useAs.mediaType !== undefined ? mediaCenter.useAs.mediaType : null;
    let mediaIds = mediaCenter.getSelectedMediaIds();
    if (mediaIds.length > parseFloat(max))
        errors.push({ msg: `You can select Maximum: ${max} media.` });
    let media = [];
    mediaIds.forEach((mediaId) => {
        let m = mediaCenter.findMedia(mediaId);
        if (mediaType !== null && m.type.includes(mediaType))
            media.push(m);
        else if (mediaType === null)
            media.push(m);
        else
            errors.push({ msg: 'Only ' + mediaType + ' allowed.' });
    });
    if (errors.length > 0) {
        mediaCenter.notification.show({
            classes: ['fail'],
            text: errors[0].msg
        });
        return;
    }
    mediaCenter.useAs.onUse({
        mediaIds: mediaIds,
        media: media
    });
    mediaCenter.hide();
};
// Media Center
MediaCenter.prototype.init = function (options = {}) {
    mediaCenter.initOverlay();
    mediaCenter.initTabs();
    mediaCenter.initUploadBox();
    mediaCenter.initFolders();
    this.useAs = {};
    this.folders = [];
    this.selectedFolderId = null;
    if (options.icons === undefined)
        this.icons = {};
    else
        this.icons = options.icons;
    if (options.images === undefined)
        this.images = {};
    else
        this.images = options.images;
    if (options.texts === undefined)
        this.texts = {};
    else
        this.texts = options.texts;
    if (options.url === undefined) {
        console.error('URL is not specified');
        return;
    }
    ;
    this.url = options.url;
    //@ts-ignore
    this.xhrRequest = xhrRequest;
    //@ts-ignore
    this.eloader = eLoader();
    //@ts-ignore
    this.notification = Notification;
    //@ts-ignore
    this.confirmation = Confirmation;
};
MediaCenter.prototype.show = function (params = {}) {
    return __awaiter(this, void 0, void 0, function* () {
        mediaCenter.showOverlay();
        mediaCenter.reset();
        let modal = document.querySelector('.media-center');
        let useAsElement = document.querySelector('.media-center-header-toolbar-list [data-is="use-as"]');
        if (modal === null)
            return;
        modal.classList.add('active');
        // fetch & show folders
        mediaCenter.eloader.show(document.querySelector('.media-center'));
        yield mediaCenter.fetchFolders();
        mediaCenter.populateFolders(mediaCenter.folders);
        mediaCenter.eloader.hide(document.querySelector('.media-center'));
        mediaCenter.useAs = params['useAs'] !== undefined ? params['useAs'] : null;
        mediaCenter.storage = params['storage'] !== undefined ? params['storage'] : 'public';
        mediaCenter.folder = params['folder'] !== undefined ? params['folder'] : null;
        if (mediaCenter.useAs === null && useAsElement !== null)
            useAsElement.style.display = 'none';
        else if (mediaCenter.useAs !== null && useAsElement !== null) {
            let titleElement = useAsElement.querySelector('.title');
            if (titleElement !== null)
                titleElement.innerHTML = mediaCenter.useAs.title;
            useAsElement.style.display = 'flex';
        }
    });
};
MediaCenter.prototype.hide = function () {
    mediaCenter.hideOverlay();
    let modal = document.querySelector('.media-center');
    if (modal === null)
        return;
    modal.classList.remove('active');
};
MediaCenter.prototype.reset = function () {
    mediaCenter.selectTab('upload');
    mediaCenter.hideAside();
};
var mediaCenter = new MediaCenter();
