/**
 * Created by ander on 12/10/18.
 */

import Session from '../lib/session';
import { Asset } from '../lib/amount';
import { License, LICENSE } from '../lib/license';
import {
    jsonify,
    jsonstring,
    getParameterByName,
    humanFileSize,
    cancelEventPropagation,
    cleanArray,
    removeEmojis,
    toPermalink,
    normalizeTag,
} from '../lib/util';
import {
    catchError,
    CONSTANTS,
    uploadToIpfs,
    resizeImage,
    parsePost,
    showPost,
    requireRoleKey,
} from '../common/common';

import validations from '../common/validation';

//Import components
import CKEditor from '../components/CKEditor';
import { SELECTABLE_CATEGORIES } from '../lib/categories';

(function () {
    const MAX_BENEFICIARIES = 10;
    //Load components
    Vue.component('ckeditor', CKEditor);

    let session, account;
    let publishContainer;
    let postUploads = {};

    function setUp(editablePost, session, account) {
        let downloadFile = {
            price: 0,
            currency: 'CREA',
        };
        let featuredImage = {};
        let sharedImage = {};
        let license = editablePost
            ? License.fromFlag(editablePost.metadata.license)
            : License.fromFlag(LICENSE.NO_LICENSE.flag);

        if (editablePost) {
            //
            //downloadFile = editablePost.download;
            let mFi = editablePost.metadata.featuredImage;
            let mSi = editablePost.metadata.sharedImage;

            featuredImage = mFi && mFi.url ? mFi : featuredImage;
            sharedImage = mSi && mSi.url ? mSi : sharedImage;
        }

        if (!publishContainer) {
            publishContainer = new Vue({
                el: '#publish-container',
                name: 'publish-container',
                data: {
                    lang: lang,
                    account: account,
                    session: session,
                    LICENSE: LICENSE,
                    CONSTANTS: CONSTANTS,
                    selectableCategories: SELECTABLE_CATEGORIES,
                    step: 1,
                    editablePost: editablePost,
                    bodyElements: editablePost ? editablePost.body : [],
                    tags: [],
                    uploadedFiles: [],
                    updatingIndex: -1,
                    editor: {
                        editing: false,
                        show: false,
                    },
                    featuredImage: featuredImage,
                    sharedImage: sharedImage,
                    title: editablePost ? editablePost.title : null,
                    description: editablePost ? editablePost.metadata.description : '',
                    adult: editablePost ? editablePost.metadata.adult : false,
                    nftLink: editablePost && editablePost.metadata.other ? editablePost.metadata.other.nftLink : null,
                    downloadFile: downloadFile,
                    publicDomain: license.has(LICENSE.FREE_CONTENT.flag)
                        ? LICENSE.FREE_CONTENT.flag
                        : LICENSE.NO_LICENSE.flag,
                    share: license.has(LICENSE.SHARE_ALIKE.flag)
                        ? LICENSE.SHARE_ALIKE.flag
                        : license.has(LICENSE.NON_DERIVATES.flag)
                        ? LICENSE.NON_DERIVATES.flag
                        : LICENSE.NO_LICENSE.flag,
                    commercial: license.has(LICENSE.NON_COMMERCIAL.flag)
                        ? LICENSE.NON_COMMERCIAL.flag
                        : LICENSE.NO_LICENSE.flag,
                    noLicense: license.has(LICENSE.NON_PERMISSION.flag)
                        ? LICENSE.NON_PERMISSION.flag
                        : LICENSE.NO_LICENSE.flag,
                    showEditor: false,
                    mainCategory: '',
                    mainBeneficiary: {
                        account: account.user.name,
                        weight: 100,
                    },
                    beneficiaries: [],
                    tagsConfig: {
                        init: false,
                        addedEvents: false,
                    },
                    error: null,
                },
                mounted: function mounted() {
                    //creaEvents.emit('crea.dom.ready', 'publish');
                },
                updated: function updated() {
                    console.log('updating');

                    if (this.step !== 2) {
                        this.tagsConfig.init = false;
                        this.tagsConfig.addedEvents = false;
                    }

                    if (this.step === 2) {
                        let inputTags = $('#publish-tags');
                        let that = this;

                        if (!this.tagsConfig.init) {
                            inputTags.tagsinput({
                                maxTags: CONSTANTS.MAX_TAGS,
                                maxChars: CONSTANTS.TEXT_MAX_SIZE.TAG,
                                delimiter: ' ',
                            });
                            this.tagsConfig.init = true;
                        }

                        if (!this.tagsConfig.addedEvents) {
                            inputTags.on('beforeItemAdd', function (event) {
                                if (!that.tags.includes(event.item)) {
                                    that.tags.push(event.item);
                                }
                            });
                            inputTags.on('itemRemoved', function (event) {
                                let i = that.tags.indexOf(event.item);

                                if (i > -1) {
                                    that.tags.splice(i, 1);
                                }
                            });
                            this.tagsConfig.addedEvents = true;

                            if (editablePost) {
                                let tags = editablePost.metadata.tags;
                                tags.forEach(function (t) {
                                    inputTags.tagsinput('add', t);
                                });
                            }
                        }

                        if (that.tags.length > 0) {
                            that.tags.forEach(function (t) {
                                inputTags.tagsinput('add', t);
                            });
                        }
                    }
                },
                methods: {
                    getLicense: function getLicense() {
                        let license;

                        if (this.noLicense === LICENSE.NON_PERMISSION.flag) {
                            license = License.fromFlag(this.noLicense);
                        } else if (this.publicDomain === LICENSE.FREE_CONTENT.flag) {
                            license = License.fromFlag(this.publicDomain);
                        } else {
                            license =
                                LICENSE.CREATIVE_COMMONS.flag | LICENSE.ATTRIBUTION.flag | this.share | this.commercial;
                            license = License.fromFlag(license);
                        }

                        return license;
                    },
                    toStep: function toStep(_toStep) {
                        if (!this.editor.show && this.step > _toStep) {
                            this.step = _toStep;
                        }
                    },
                    hasGoodBeneficiaries: function () {
                        if (this.beneficiaries.length) {
                            for (let x = 0; x < this.beneficiaries.length; x++) {
                                let b = this.beneficiaries[x];
                                if (b.weight <= 0 || !b.account) {
                                    return false;
                                }
                            }
                        }

                        return this.mainBeneficiary.weight >= 0;
                    },
                    nextStep: function nextStep() {
                        let that = this;

                        if (!this.editor.show) {
                            //Check errors before continue
                            switch (this.step) {
                                case 1:
                                    this.bodyElements = cleanArray(this.bodyElements);
                                    this.error =
                                        this.bodyElements.length > 0 ? null : this.lang.PUBLISH.NO_ELEMENTS_ERROR;
                                    break;

                                case 2:
                                    if (
                                        !this.featuredImage.hash ||
                                        !this.title ||
                                        (!this.mainCategory && this.tags.length === 0)
                                    ) {
                                        this.error = this.lang.PUBLISH.NO_TITLE_TAG_OR_IMAGE;
                                    } else if (!this.hasGoodBeneficiaries()) {
                                        this.error = this.lang.PUBLISH.NO_BENEFICIARY_FILLED;
                                    } else if (!validateLink(this.nftLink)) {
                                        this.error = this.lang.PUBLISH.INVALID_NFT_LINK;
                                    } else {
                                        this.error = null;
                                    }
                                    break;

                                case 3:
                                    if (
                                        this.editablePost &&
                                        this.editablePost.download.size &&
                                        !this.downloadFile.size
                                    ) {
                                        this.error = String.format(
                                            this.lang.PUBLISH.RELOAD_DOWNLOAD_FILE,
                                            this.editablePost.download.size.name
                                        );
                                    } else {
                                        this.error = null;
                                    }
                                    break;
                            }

                            if (!this.error) {
                                this.step += 1;
                            }
                        }
                    },
                    loadFile: function loadFile(event) {
                        if (!this.editor.show) {
                            let elem = this.$refs.publishInputFile;
                            elem.click();
                        }
                    },
                    loadFeaturedImage: function loadFeaturedImage(event) {
                        let elem = this.$refs.publishInputCover;
                        elem.click();
                    },
                    onInputDownloadFile: function onInputDownloadFile(event) {
                        let files = event.target.files;
                        let that = this;

                        if (files.length > 0) {
                            globalLoading.show = true;
                            let loadedFile = files[0];
                            let maximumSize = CONSTANTS.FILE_MAX_SIZE.POST_BODY.DOWNLOAD;
                            uploadToIpfs(loadedFile, maximumSize, function (err, file) {
                                globalLoading.show = false;

                                if (!catchError(err)) {
                                    file.resource = file.url;
                                    that.downloadFile = Object.assign(that.downloadFile, jsonify(jsonstring(file)));
                                    if (that.editablePost) {
                                        that.editablePost.downloadUploaded = file.size > 0;
                                    }
                                }
                            });
                        }
                    },
                    onLoadFile: function onLoadFile(event) {
                        let that = this;
                        let files = event.target.files;
                        let loadedFile = files[0];

                        console.log('File loading', loadedFile);
                        if (files.length > 0) {
                            globalLoading.show = true;

                            let [fileType, fileFormat] = loadedFile.type.toUpperCase().split('/');
                            let maximumSize = CONSTANTS.FILE_MAX_SIZE.POST_BODY[fileType];

                            //Reset maximum size of video files to allow only webm or mo4
                            if (fileType.includes('VIDEO')) {
                                maximumSize = 0;
                            }

                            //Set specific file format sizes
                            if (CONSTANTS.FILE_MAX_SIZE.POST_BODY[fileFormat]) {
                                maximumSize = CONSTANTS.FILE_MAX_SIZE.POST_BODY[fileFormat];
                            }

                            //Show alert for video formats not allowed
                            if (fileType.includes('VIDEO') && maximumSize <= 0) {
                                globalLoading.show = false;
                                return catchError({
                                    TITLE: lang.PUBLISH.FILE_NOT_ALLOWED,
                                    BODY: [lang.PUBLISH.ALLOWED_VIDEO_FORMATS],
                                });
                            }

                            console.log(
                                'file:',
                                loadedFile,
                                'MaxSize:',
                                maximumSize,
                                'isGif',
                                loadedFile.type.toLowerCase().includes('image/gif')
                            );
                            uploadToIpfs(loadedFile, maximumSize, function (err, file) {
                                globalLoading.show = false;

                                if (err) {
                                    that.error = catchError(err, false);
                                } else {
                                    that.bodyElements.push(file);
                                    postUploads[file.hash] = loadedFile;
                                    that.error = null;

                                    if (file.type.indexOf('image/') > -1 && !that.sharedImage.hash) {
                                        uploadToIpfs(
                                            loadedFile,
                                            CONSTANTS.FILE_MAX_SIZE.POST_BODY[
                                                loadedFile.type.toUpperCase().split('/')[0]
                                            ],
                                            function (err, uploadedPreview) {
                                                if (!err) {
                                                    that.sharedImage = uploadedPreview;
                                                    console.log('Featured image loaded!');
                                                } else {
                                                    console.error(err, loadedFile);
                                                }
                                            }
                                        );
                                    }

                                    resizeImage(loadedFile, function (resizedFile) {
                                        let maximumPreviewSize =
                                            CONSTANTS.FILE_MAX_SIZE.POST_PREVIEW[
                                                loadedFile.type.toUpperCase().split('/')[0]
                                            ];
                                        postUploads[file.hash] = {
                                            original: loadedFile,
                                            resized: resizedFile,
                                        };

                                        //Set first loaded image as preview
                                        if (file.type.indexOf('image/') > -1 && !that.featuredImage.hash) {
                                            uploadToIpfs(
                                                resizedFile,
                                                maximumPreviewSize,
                                                function (err, uploadedPreview) {
                                                    if (!err) {
                                                        that.featuredImage = uploadedPreview;
                                                        console.log('Featured image loaded!');
                                                    } else {
                                                        console.error(err, resizedFile);
                                                    }
                                                }
                                            );
                                        }
                                    });

                                    //Clear input
                                    let elem = that.$refs.publishInputFile;
                                    $(elem).val('');
                                }
                            });
                        }
                    },
                    onLoadFeaturedImage: function onLoadFeaturedImage(event) {
                        let that = this;
                        let files = event.target.files;

                        if (files.length > 0) {
                            globalLoading.show = true;
                            let loadedFile = files[0];

                            uploadToIpfs(
                                loadedFile,
                                CONSTANTS.FILE_MAX_SIZE.POST_BODY[loadedFile.type.toUpperCase().split('/')[0]],
                                function (err, uploadedPreview) {
                                    if (!err) {
                                        that.sharedImage = uploadedPreview;
                                        console.log('Featured image loaded!');
                                    } else {
                                        console.error(err, loadedFile);
                                    }
                                }
                            );

                            let maximumSize =
                                CONSTANTS.FILE_MAX_SIZE.POST_PREVIEW[loadedFile.type.toUpperCase().split('/')[1]];
                            if (!maximumSize) {
                                maximumSize = CONSTANTS.FILE_MAX_SIZE.POST_PREVIEW.IMAGE;
                            }

                            if (loadedFile.size <= maximumSize) {
                                uploadToIpfs(loadedFile, maximumSize, function (err, file) {
                                    globalLoading.show = false;

                                    if (!catchError(err)) {
                                        that.featuredImage = file;
                                        postUploads[file.hash] = loadedFile;
                                        that.error = null;
                                    }
                                });
                            } else {
                                resizeImage(loadedFile, function (resizedFile) {
                                    console.log('ResizedFile', resizedFile)
                                    uploadToIpfs(resizedFile, maximumSize, function (err, file) {
                                        globalLoading.show = false;

                                        if (!catchError(err)) {
                                            that.featuredImage = file;
                                            postUploads[file.hash] = loadedFile;
                                            that.error = null;
                                        }
                                    });
                                });
                            }

                        }
                    },
                    toggleEditor: function toggleEditor(event) {
                        cancelEventPropagation(event);

                        if (!this.editor.show) {
                            this.editor.show = !this.editor.show;
                        }
                    },
                    editorInput: function editorInput(data) {
                        this.editor.editing = data.length > 0;
                    },
                    editorEmbedVideo: function (url, data) {
                        let embedElement = {
                            type: 'embed/' + data.reproductor,
                            value: url,
                            player: data.reproductor,
                        };

                        this.bodyElements.push(embedElement);
                    },
                    addBeneficiary: function () {
                        if (!this.beneficiaries.length) {
                            this.beneficiaries.push({
                                account: '',
                                weight: 0,
                            });
                        } else if (this.beneficiaries.length < MAX_BENEFICIARIES) {
                            let lastBeneficiary = this.beneficiaries[this.beneficiaries.length - 1];
                            if (lastBeneficiary.account && lastBeneficiary.weight > 0) {
                                this.beneficiaries.push({
                                    account: '',
                                    weight: 0,
                                });
                            } else {
                                this.error = this.lang.PUBLISH.NO_BENEFICIARY_FILLED;
                            }
                        } else {
                            this.error = String.format(this.lang.PUBLISH.MAX_BENEFICIARIES_REACHED, MAX_BENEFICIARIES);
                        }
                    },
                    updateBeneficiariesWeight: function () {
                        let totalSumBeneficiaries = 0;
                        this.beneficiaries.forEach((b) => {
                            totalSumBeneficiaries += parseFloat(b.weight);
                            //console.log('tsb', totalSumBeneficiaries, b.account, b.weight);
                        });
                        this.mainBeneficiary.weight = 100 - totalSumBeneficiaries;
                        if (totalSumBeneficiaries > 100) {
                            this.error = this.lang.PUBLISH.BENEFICIARY_WEIGHT_OVERFLOW;
                        }
                    },
                    deleteBeneficiary: function (event, b) {
                        cancelEventPropagation(event);
                        this.beneficiaries.splice(b, 1);
                        this.updateBeneficiariesWeight();
                    },
                    updateText: updateText,
                    removeTitleEmojis: removeTitleEmojis,
                    removeDescriptionEmojis: removeDescriptionEmojis,
                    editText: editText,
                    removeElement: removeElement,
                    addVideo: addVideo,
                    makePublication: makePublication,
                    humanFileSize: humanFileSize,
                    stringFormat: String.format,
                },
            });
        } else {
            publishContainer.session = session;
            publishContainer.account = account;
            publishContainer.$forceUpdate();
        }
    }

    function validateLink(value) {
        if (value) {
            return validations.url(value);
        }

        return true;
    }

    function removeTitleEmojis(event) {
        let target = event.target;
        publishContainer.title = removeEmojis(target.value);
    }

    function removeDescriptionEmojis(event) {
        let target = event.target;
        publishContainer.description = removeEmojis(target.value);
    }

    function updateText() {
        let index = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : -1;
        let editor = CKEDITOR.instances['editor'];
        let text = editor.getData();

        if (!text.isEmpty()) {
            if (index > -1) {
                publishContainer.bodyElements[index].value = text;
            } else {
                publishContainer.bodyElements.push({
                    value: text,
                    type: 'text/html',
                });
            }

            publishContainer.updatingIndex = -1;
            editor.setData('');
            publishContainer.editor.editing = false;
            publishContainer.editor.show = false;
        }
    }

    function editText(index) {
        console.log('Editing text', index);

        if (index > -1) {
            publishContainer.editor.show = true;
            setTimeout(function () {
                let editor = CKEDITOR.instances['editor'];
                let text = publishContainer.bodyElements[index].value;
                editor.setData(text);
                publishContainer.updatingIndex = index;
                publishContainer.editor.editing = true;
            }, 500);
        }
    }

    function removeElement(index) {
        if (index > -1 && index <= publishContainer.bodyElements.length - 1) {
            let element = publishContainer.bodyElements[index];
            publishContainer.bodyElements.splice(index, 1);

            //If preview image = element, so set preview image next image in post
            if (element.type.includes('image/')) {
                let files = postUploads[element.hash];
                if (
                    files.resized.name === publishContainer.featuredImage.name &&
                    files.resized.size === publishContainer.featuredImage.size
                ) {
                    publishContainer.featuredImage = {};
                    publishContainer.sharedImage = {};
                    delete postUploads[element.hash];
                }
            }

            if (!publishContainer.featuredImage.hash) {
                //Set first image of post body as featuredImage
                for (let x = 0; x < publishContainer.bodyElements.length; x++) {
                    let bodyEl = publishContainer.bodyElements[x];
                    if (bodyEl.type.includes('image/')) {
                        let maximumPreviewSize = CONSTANTS.FILE_MAX_SIZE.POST_PREVIEW['IMAGE'];
                        let newFiles = postUploads[bodyEl.hash];
                        console.log('Selected new featured image', newFiles.resized.name, bodyEl.hash);
                        uploadToIpfs(newFiles.resized, maximumPreviewSize, function (err, uploadedPreview) {
                            if (!err) {
                                console.log('Preview uploaded', uploadedPreview);
                                publishContainer.featuredImage = uploadedPreview;
                                publishContainer.sharedImage = uploadedPreview;
                                console.log('Featured image loaded!');
                            } else {
                                console.error(err, newFiles.resized);
                            }
                        });

                        break;
                    }
                }
            }

            publishContainer.$forceUpdate();
        }
    }

    function addVideo() {
        let url = prompt('Youtube, Vimeo, Dailymotion URL');
        let id = '';
        let reproductor = '';
        let url_comprobar = '';

        if (url.indexOf('youtu.be') >= 0) {
            reproductor = 'youtube';
            id = url.substring(url.lastIndexOf('/') + 1, url.length);
        } else if (url.indexOf('youtube') >= 0) {
            reproductor = 'youtube';
            if (url.indexOf('</iframe>') >= 0) {
                let fin = url.substring(url.indexOf('embed/') + 6, url.length);
                id = fin.substring(fin.indexOf('"'), 0);
            } else {
                if (url.indexOf('&') >= 0) id = url.substring(url.indexOf('?v=') + 3, url.indexOf('&'));
                else id = url.substring(url.indexOf('?v=') + 3, url.length);
            }
            url_comprobar = 'https://gdata.youtube.com/feeds/api/videos/' + id + '?v=2&alt=json';
            //"https://gdata.youtube.com/feeds/api/videos/" + id + "?v=2&alt=json"
        } else if (url.indexOf('vimeo') >= 0) {
            reproductor = 'vimeo';
            if (url.indexOf('</iframe>') >= 0) {
                var fin = url.substring(url.lastIndexOf('vimeo.com/"') + 6, url.indexOf('>'));
                id = fin.substring(fin.lastIndexOf('/') + 1, fin.indexOf('"', fin.lastIndexOf('/') + 1));
            } else {
                id = url.substring(url.lastIndexOf('/') + 1, url.length);
            }
            url_comprobar = 'http://vimeo.com/api/v2/video/' + id + '.json';
            //'http://vimeo.com/api/v2/video/' + video_id + '.json';
        } else if (url.indexOf('dai.ly') >= 0) {
            reproductor = 'dailymotion';
            id = url.substring(url.lastIndexOf('/') + 1, url.length);
        } else if (url.indexOf('dailymotion') >= 0) {
            reproductor = 'dailymotion';
            if (url.indexOf('</iframe>') >= 0) {
                let fin = url.substring(url.indexOf('dailymotion.com/') + 16, url.indexOf('></iframe>'));
                id = fin.substring(fin.lastIndexOf('/') + 1, fin.lastIndexOf('"'));
            } else {
                if (url.indexOf('_') >= 0) id = url.substring(url.lastIndexOf('/') + 1, url.indexOf('_'));
                else id = url.substring(url.lastIndexOf('/') + 1, url.length);
            }
            url_comprobar = 'https://api.dailymotion.com/video/' + id;
            // https://api.dailymotion.com/video/x26ezrb
        }

        switch (reproductor) {
            case 'youtube':
                url = 'https://www.youtube.com/embed/' + id + '?autohide=1&controls=1&showinfo=0';
                break;
            case 'vimeo':
                url = 'https://player.vimeo.com/video/' + id + '?portrait=0';
                break;
            case 'dailymotion':
                url = 'https://www.dailymotion.com/embed/video/' + id;
                break;
            default:
                catchError(lang.ERROR.UNSUPPORTED_VIDEO_PLATFORM);
                return;
        }

        publishContainer.editorEmbedVideo(url, { reproductor, id_video: id });
    }

    function makePublication(event) {
        cancelEventPropagation(event);
        let username = session.account.username;
        requireRoleKey(username, 'posting', function (postingKey) {
            let _crea$broadcast;

            //All tags must be lowercase;
            globalLoading.show = true;
            let tags = publishContainer.tags;

            let nTags = [];
            //Add MainCategory
            if (publishContainer.mainCategory) {
                nTags.push(normalizeTag(publishContainer.mainCategory));
            }

            for (let x = 0; x < tags.length; x++) {
                let t = normalizeTag(tags[x]);
                if (!nTags.includes(t)) {
                    nTags.push(t);
                }
            }

            let metadata = {
                description: publishContainer.description,
                tags: nTags,
                adult: publishContainer.adult,
                featuredImage: publishContainer.featuredImage,
                sharedImage: publishContainer.sharedImage,
                license: publishContainer.getLicense().getFlag(),
                app: 'creary',
                version: '1.0.0',
                other: {
                    nftLink: publishContainer.nftLink
                }
            };
            let download = publishContainer.downloadFile;

            if (!download.price) {
                download.price = 0;
            }

            download.price = Asset.parseString(download.price + ' ' + download.currency).toFriendlyString(null, false);

            if (!download.resource) {
                download = '';
            }

            //Build body
            let body = jsonstring(publishContainer.bodyElements);
            let title = publishContainer.title;
            let isEditing = publishContainer.editablePost !== null && publishContainer.editablePost !== undefined;
            let permlink = isEditing ? publishContainer.editablePost.permlink : toPermalink(title); //Add category to tags if is editing

            let publishPost = function () {
                if (isEditing && publishContainer.editablePost.metadata.tags) {
                    let category = publishContainer.editablePost.metadata.tags[0];

                    if (category && !metadata.tags.includes(category)) {
                        metadata.tags.unshift(category);
                    }
                }

                let operations = [];
                operations.push(
                    crea.broadcast.commentBuilder(
                        '',
                        toPermalink(metadata.tags[0]),
                        username,
                        permlink,
                        title,
                        body,
                        jsonstring(download),
                        jsonstring(metadata)
                    )
                );
                //Build beneficiaries

                if (!isEditing) {
                    //Update beneficiaries
                    let extensions = [];
                    let beneficiaries = [];
                    publishContainer.beneficiaries.forEach((b) => {
                        beneficiaries.push({
                            account: b.account,
                            weight: b.weight * 100,
                        });
                    });

                    if (beneficiaries.length) {
                        extensions.push([0, { beneficiaries }]);
                    }

                    let rewards = account.user.metadata.post_rewards;
                    switch (rewards) {
                        case '0':
                            operations.push(
                                crea.broadcast.commentOptionsBuilder(
                                    username,
                                    permlink,
                                    '0.000 CBD',
                                    10000,
                                    true,
                                    true,
                                    extensions
                                )
                            );
                            break;
                        case '50':
                            break;
                        case '100':
                        default:
                            operations.push(
                                crea.broadcast.commentOptionsBuilder(
                                    username,
                                    permlink,
                                    '1000000.000 CBD',
                                    0,
                                    true,
                                    true,
                                    extensions
                                )
                            );
                            break;
                    }
                }
                let keys = [postingKey];

                crea.broadcast.sendOperations(keys, ...operations, function (err, result) {
                    if (!catchError(err)) {
                        console.log(result);
                        let post = {
                            url: '/' + toPermalink(metadata.tags[0]) + '/@' + session.account.username + '/' + permlink,
                        };
                        globalLoading.show = true;
                        setTimeout(() => {
                            showPost(post);
                        }, 3 * 1e3);
                    } else {
                        globalLoading.show = false;
                    }
                });
            };

            if (!isEditing) {
                //Check if already has a post with same permlink
                crea.api.getDiscussion(username, permlink, function (err, result) {
                    console.log(err, result);
                    if (!err) {
                        //If id is 0, post not exists, so publish
                        if (result.id !== 0) {
                            catchError(lang.ERROR.PERMLINK_ALREADY_EXISTS);
                            globalLoading.show = false;
                        } else {
                            publishPost();
                        }
                    }
                });
            } else {
                publishPost();
            }
        });
    }

    creaEvents.on('crea.session.login', function (s, a) {
        session = s;
        account = a;

        let edit = getParameterByName('edit');

        if (edit) {
            let author = edit.split('/')[0];
            let permlink = edit.split('/')[1]; //Check if author is the user

            let s = Session.getAlive();

            if (s && s.account.username === author) {
                crea.api.getDiscussion(author, permlink, function (err, post) {
                    if (!catchError(err)) {
                        post = parsePost(post);
                        let price = Asset.parse(post.download.price);
                        post.download.price = parseFloat(price.toPlainString());
                        post.download.currency = price.asset.symbol;
                        post.downloadUploaded = false;
                        setUp(post, session, account);
                    }
                });
            } else {
                //TODO: SHOW EDIT ERROR
            }
        } else {
            setUp(null, session, account);
        }

        creaEvents.emit('crea.dom.ready');
    });
})();
