<template>
    <div>
        <v-dialog
            id="_media"
            width="100%"
            max-width="1300px"
            v-model="media_dialog"
        >
            <v-card
                max-height="800"
                min-height="700"
                color="#f9f9f9"
                tile
                id="_mediaCard"
                :loading="loading"
            >
                <v-tabs
                    v-model="active"
                    color="info"
                    left
                    id="_mediaTabs"
                >
                    <v-tab>Media</v-tab>
                    <v-tab>Upload</v-tab>

                    <v-tab-item>
                        <v-container fluid class="pa-0" id="_mainContainer">
                            <div id="innerWrap">
                                <div
                                    :class="'_image ' + getClass(m)"
                                    v-for="(m, i) in media"
                                    :key="i"
                                    @click="chosen(m)"
                                >
                                    <img
                                        v-if="m.url && m.type == 'image'"
                                        :src="m.url"
                                    />
                                    <span
                                        v-if="m.url && (m.format == 'application/pdf' || m.format == 'pdf')"
                                        class="_pdf"
                                    >
                                        <v-icon>mdi-file-pdf-box</v-icon>
                                        {{ m.name }}
                                    </span>
                                    <video
                                        v-if="m.url && m.type == 'video' && m.format != 'embeded'"
                                    >
                                        <source :src="m.url" />
                                    </video>
                                    <div v-html="m.name" v-if="m.url && m.type == 'video' && m.format == 'embeded'"></div>
                                    <div class="_layer"></div>
                                </div>
                            </div>
                        </v-container>
                    </v-tab-item>
                    <v-tab-item
                        eager
                    >
                        <v-container fluid>
                            <div id="imageUploader"></div>
                        </v-container>
                    </v-tab-item>
                </v-tabs>
                    <div id="_mediaFooter"
                        v-if="active == 0"
                    >
                        <div class="flex space-between">
                            <div class="flex">
                                <div class="_paginate">
                                    <v-select
                                        dense
                                        solo
                                        v-model="paginate"
                                        v-if="mediaData &&
                                            mediaData.last_page &&
                                            mediaData.last_page > 1"
                                        :items="getPaginate(mediaData.last_page)"
                                        hide-details
                                    >

                                    </v-select>
                                </div>
                                <div class="_type ml-5">
                                    <v-select
                                        solo
                                        v-model="type"
                                        :items="types"
                                        hide-details
                                    >
                                    </v-select>
                                </div>
                            </div>
                            <v-btn
                                color="info"
                                @click="finishMediaSelection"
                            >
                                Finish
                            </v-btn>
                        </div>
                    </div>
            </v-card>
      </v-dialog>
    </div>
</template>

<script>
import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import XHRUpload from '@uppy/xhr-upload'
import axios from 'axios'
import '@uppy/core/dist/style.min.css'
import '@uppy/dashboard/dist/style.min.css'

export default {
    data () {
        return {
            paginate: 1,
            url: null,
            loading: true,
            mediaData: [],
            media: [],
            active: 0,
            selected: this.$store.state.selectedMedia,
            selectedIds: this.$store.state.selectedMediaIds,
            media_dialog: this.$store.state.media_dialog,
            dashboard_initialised: false,
            types: ['Image', 'Video', 'PDF'],
            type: this.$store.state.mediaType,
            media_fetched: false,
            selectionFinished: false
        }
    },
    methods: {

        getPaginate (end) {
            var start = 1;
            return new Array(end - start).fill().map((d, i) => i + start);
        },
        finishMediaSelection() {

            this.selectionFinished = true;
            this.$store.commit('mediaSelectionFinished', true);
            this.selected = [];
            this.getClass();
            this.media_dialog = false;

            setTimeout(() => {
                
                /* 
                | Keep selection open for the next time
                | Fixed bug for not able to select media after the first selection
                */    
                this.selectionFinished = false;

            }, 100);


        },
        chosen (media) {

            var selected = this.selected;
            var index =  this.selectedIds.indexOf(media.id);
            var multiple = this.$store.state.selectMultipleMedia;

            // If image exists in the array -> Remove it
            if (index > -1) {

                //this removes matched index
                this.selected.splice(index, 1);
                this.getClass(media);

                return;
            }

            // If the selection is not multiple (Means single) then it resets this.selected array so that previously selected object (media) will be flushed
            if (!multiple && selected && selected.length) {

                this.selected = [];

            }

            //Finally adding media to the array and adding class selected
            this.selected.push(media);
            this.getClass(media);

        },
        getClass (media) {

            if (media) {
                var selected = this.selected;
                for (var key in selected) {
                    if (selected && selected[key]) {
                        if (media.id == selected[key]['id']) {
                            return 'selected';
                        }
                    }
                }
            }

            return '';

        },
        getAllMedia (page = 1) {
            this.loading = true;
            this.$axiosx.get('/media?page=' + page + '&type=' + this.type.toLowerCase())
            .then((res) => {
                this.loading = false;
                this.media = res.data.data;
                this.mediaData = res.data;
            });
        },
        initUppy () {
            var url = '/' + ADMIN_API_ROUTE + '/media/upload';
            Uppy().use(Dashboard, {
                target: "#imageUploader",
                id: 'Dashboard',
                trigger: '#uppy-select-files',
                inline: true,
                width: '100%',
                height: '100vh'
            }).use(XHRUpload, {
                endpoint: url,
                getResponseData: ((responseText, response) => {
                    var res = JSON.parse(responseText);
                    if (res.status == 'error') {
                        this.$store.commit('snackbar', { status: true, message: res.message });
                    }

                    this.getAllMedia();
                    this.active = 0;
                    this.paginate = 1;

                    return {
                        url: res.url
                    }
                })
            })
        }
    },
    watch: {
        selected (list) {

            // If this.selected has a length then it will update this.selectedIds
            if (list && list.length) {

                //Before pushing arrays into ids, resetting this.selectedIds to remove any duplication
                this.selectedIds = [];

                //Finally mapping list to this.selectedIds
                list.map((l) => {
                    this.selectedIds.push(l.id);
                });

            } else {

                //If list has no length then resetting this.selectedIds array
                this.selectedIds = [];

            }

            //If selection finshed button is triggered then it should not update the change since this.selected & this.selectedIds would be resetted
            if (!this.selectionFinished) {

                this.$store.commit('setSelectedMedia', this.selected);
                this.$store.commit('setSelectedMediaIds', this.selectedIds);

            }

        },
        type (val) {
            this.getAllMedia();
        },
        '$store.state.mediaType' (type) {
            this.type = type;
        },
        paginate (number) {
            this.getAllMedia(number);
        },
        media_dialog (val) {

            if (val) {

                // If media is not already fetched
                if (!this.media_fetched) {
                    this.getAllMedia();
                }

                this.selected = this.$store.state.selectedMedia;
                this.selectedIds = this.$store.state.selectedMediaIds;

                //if media dialog is initialised and dashboard (uppy) is not initialised then initialise it
                var t = this;

                if (!this.dashboard_initialised) {

                    //Setting timeout as the function need to be awaited for the element to be rendered
                    setTimeout(function () {
                        t.initUppy();
                    }, 100);

                    this.dashboard_initialised = true;

                }
            }

            if (!val) {
                this.$store.commit('media_dialog', false);
            }

        },
        '$store.state.media_dialog' (val) {
            if (val) {
                this.media_dialog = val;
            }
        },
    }
}
</script>

<style>
    a.uppy-Dashboard-poweredBy {
      display: none;
    }

    #myUploader {
      position: relative;
      background-color: #fafafa;
      max-width: 100%;
      max-height: 100%;
      min-height: 450px;
      outline: none;
      border: 1px solid #eaeaea;
      border-radius: 5px;
    }

    div#innerWrap {
        display: flex;
        flex-flow: row wrap;
        background: transparent;
        padding: 0;
        min-height: 560px;
        overflow: auto;
    }

    ._image {
        display: flex;
        flex-flow: column;
        align-items: center;
        justify-content: center;
        margin: 15px;
        cursor: pointer;
        position: relative;
        border: 1px solid #ccc;
        width: calc(12.5% - 30px);
        overflow: hidden;
        height: 100px;
    }

    ._addTrigger._image {
        border: 1px solid #ccc;
    }

  ._image img {
        width: 100%;
        object-fit: cover;
        object-position: center;
        height: 100px;
    }

    ._addTrigger > ._inner {
      width: 48px;
    }

    ._addTrigger i {
      font-size: 48px;
      color: #999;
    }

    ._cover {
      position: absolute !important;
      bottom: 10px;
    }

    .bg-gray {
      background: #fafafa;
    }

    body .uppy-Dashboard-inner {
      border-radius: 0;
      max-height: 550px;
    }

    button._delete.v-btn.v-btn--floating.v-btn--small {
        position: absolute;
        top: -15px;
        right: -15px;
        visibility: hidden;
        opacity: 0;
        transition: .3s;
    }

    ._image:hover button._delete.v-btn.v-btn--floating.v-btn--small {
      visibility: visible;
      opacity: 1;
    }

    @media(max-width: 768px) {
      ._image {
          width: calc(25% - 30px);
          margin: 15px 0;
      }
    }

    @media (max-width: 640px) {
      ._image {
          width: 100%;
      }
    }

    #_media .v-dialog {
        border-radius: 0;
    }

    #_mainContainer {
        background: #f9f9f9;
        border-top: 1px solid #f0f0f0;
    }

    #_mediaCard.v-card--loading {
        overflow: visible;
    }

    ._image.selected {
        outline: 2px solid #3398f3;
        border: 1px solid transparent;
    }

    ._pdf {
        text-align: center;
        text-decoration: none;
        display: flex;
        flex-flow: column;
        height: 100%;
        font-size: 13px;
    }

    ._pdf .v-icon.v-icon {
        font-size: 80px;
        color: #f64545;
        background: #fff;
        height: 80%;
    }

    ._image video {
        max-width: 100%;
    }

    ._paginate {
        max-width: 80px;
    }

    ._layer {
        position: absolute;
        z-index: 0;
        background: #000;
        opacity: 0.2;
        height: 100%;
        width: 100%;
    }
</style>
