<template>
    <div class="mb-5 mt-5">
        <v-btn
            large
            color="#444"
            tile
            outlined
            @click="mediaDialog"
            :block="block"
        >
            <v-icon left>mdi-cloud-upload-outline</v-icon>
            {{ text }}
        </v-btn>
        <div
            class="_fileZone flex row-wrap"
            v-if="mediaList && mediaList.length"
        >
            <div
                :class="'_zone ' + cls"
                v-for="(m, i) in mediaList"
                :key="i"
            >
                <input v-if="name" type="hidden" :name="getName()" :value="m.id">
                <a
                    v-if="m.type == 'application' && (m.format == 'application/pdf' || m.format == 'pdf')"
                    :href="m.url"
                    class="_pdf"
                    target="_blank"
                >
                    <v-icon>mdi-file-pdf-box</v-icon>
                    {{ m.name }}
                </a>
                <img
                    v-if="m.type == 'image'"
                    :src="m.url"
                    :alt="m.name"
                >
                <video
                    controls
                    v-if="m.url && m.type == 'video' && m.format != 'embeded'"
                >
                    <source :src="m.url" />
                </video>
                <div v-html="m.name" v-if="m.url && m.type == 'video' && m.format == 'embeded'">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MediaDialog from './MediaDialog'

export default {
    props: {
        multiple: {
            default: true
        },
        text: {
            default: 'Select Media'
        },
        name: {

        },
        block: {
            default: false
        },
        cls: {
            default: ''
        },
        type: {
            default: 'Image'
        },
        value: {
            default: function () {
                return '';
            }
        }
    },
    data () {
        return {
            active: false,
            mediaList: [],
            mediaListIds: []
        }
    },
    mounted () {
        this.iterateMediaList();
    },
    methods: {
        iterateMediaList (list = null) {

            if (!list) {
                list = this.value;
            }

            if (list) {

                if (!this.multiple) {
                    var mediaList = new Array();
                    mediaList.push(list);
                } else {
                    var mediaList = list;
                }

                this.mediaList = mediaList;
            }

        },
        getName () {
            if (this.multiple) {
                return this.name + '[]';
            }

            return this.name;
        },
        mediaDialog () {
            this.active = true;
            this.$store.commit('setSelectMultipleMedia', this.multiple);
            this.$store.commit('setSelectedMedia', this.mediaList);
            this.$store.commit('setSelectedMediaIds', this.mediaListIds);
            this.$store.commit('setMediaType', this.type);
            this.$store.commit('media_dialog', true);
        }
    },
    watch: {
        '$store.state.mediaSelectionFinished' (state) {
            if (state && this.active) {
                this.mediaList = this.$store.state.selectedMedia;
                this.mediaListIds = this.$store.state.selectedMediaIds;
                this.active = false;
                this.$store.commit('mediaSelectionFinished', false);
            }
        },
        value (list) {
            this.iterateMediaList(list);
        },
        mediaList (list) {

        }
    }

}
</script>

<style lang="scss">
._fileZone ._zone {
    padding: 10px;
    border: 1px solid #ccc;
    margin: 7.5px;
    display: flex;
    height: 200px;
    align-items: center;
    overflow: hidden;
    width: calc(20% - 15px);
}

._fileZone ._zone img {
    max-width: 100%;
    margin: auto;
    display: block;
    height: 100%;
}

._fileZone {
    margin: 10px -7.5px;
    overflow: auto;
    display: flex;
    align-items: center;
    max-width: 200%;
}
a._pdf {
    width: 100%;
    color: #444 !important;
}

._zone video, ._zone iframe {
    max-width: 100%;
}

._fileZone ._zone._block {
    width: 100%;
}
</style>
