<template>
    <textarea v-bind:id="id" v-bind:value="value" rows=30 cols=80></textarea>
</template>

<script>

    //TODO: Import CKEDITOR
    export default {
        props: {
            value: {
                type: String
            },
            id: {
                type: String,
                'default': 'editor'
            }
        },
        beforeUpdate: function beforeUpdate() {
            let ckeditorId = this.id;

            if (this.value !== CKEDITOR.instances[ckeditorId].getData()) {
                CKEDITOR.instances[ckeditorId].setData(this.value);
            }
        },
        methods: {
            onInput: function onInput(event) {
                console.log(event);
                this.$emit('input', true);
            }
        },
        mounted: function mounted() {
            let _this = this;

            let ckeditorId = this.id;
            console.log(this.value);
            let config = {};
            config.toolbarGroups = [{
                name: 'clipboard',
                groups: ['clipboard', 'undo']
            }, {
                name: 'basicstyles',
                groups: ['basicstyles', 'cleanup']
            }, {
                name: 'links',
                groups: ['links']
            }, {
                name: 'styles',
                groups: ['styles']
            }, {
                name: 'paragraph',
                groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
            }, {
                name: 'editing',
                groups: ['find', 'selection', 'spellchecker', 'editing']
            }, {
                name: 'insert',
                groups: ['insert']
            }, {
                name: 'forms',
                groups: ['forms']
            }, {
                name: 'tools',
                groups: ['tools']
            }, {
                name: 'document',
                groups: ['mode', 'document', 'doctools']
            }, {
                name: 'others',
                groups: ['others']
            }, '/', {
                name: 'colors',
                groups: ['colors']
            }, {
                name: 'about',
                groups: ['about']
            }];
            config.removeButtons = 'Subscript,Superscript,PasteText,PasteFromWord,Undo,Redo,Scayt,Anchor,Image,Maximize,Source,HorizontalRule,Table,SpecialChar,Strike,RemoveFormat,NumberedList,Blockquote,About,BulletedList'; //Disallow tags, classes and attributes

            config.disallowedContent = 'img script *[on*] *[style]'; // Set the most common block elements.

            config.format_tags = 'p;h1;h2;h3;h4;pre'; // Simplify the dialog windows.

            config.removeDialogTabs = 'image:advanced;link:advanced';
            config.resize_enabled = true;
            //config.extraPlugins = 'html5audio,html5video';
            config.extraPlugins = 'videodetector';

            let editor = CKEDITOR.replace(ckeditorId, config); //CKEDITOR.disableAutoInline = true;
            //CKEDITOR.inline(ckeditorId, config);

            editor.onembedvideo = function (url, data) {
                that.$emit('embedvideo', url, data)
            };

            CKEDITOR.instances[ckeditorId].setData(this.value);
            let that = this;
            CKEDITOR.instances[ckeditorId].on('change', function () {
                let ckeditorData = CKEDITOR.instances[ckeditorId].getData();

                if (ckeditorData !== _this.value) {
                    that.$emit('input', ckeditorData);
                }
            });
        },
        destroyed: function destroyed() {
            let ckeditorId = this.id;

            if (CKEDITOR.instances[ckeditorId]) {
                CKEDITOR.instances[ckeditorId].destroy();
            }
        }
    }
</script>
