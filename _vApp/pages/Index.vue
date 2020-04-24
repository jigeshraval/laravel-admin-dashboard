<template>
    <v-app class="main-bg">
        <div class="flex">
            <Media></Media>
            <Drawer
              v-if="$store.state.drawerVisibility"
            ></Drawer>
            <div class="flex column">
                <div id="_wrapper" class="pa-5">
                    <router-view></router-view>
                </div>
            </div>
            <div class="UppyProgressBar"></div>
            <v-snackbar
                v-model="snackbar.status"
                :color="snackbar.color"
                :timeout="timeout"
                :top="false"
                :bottom="true"
            >
                {{ snackbar.text }}
                <v-btn
                    color="#fff"
                    text
                    @click="snackbar.status = false"
                >
                    Close
                </v-btn>
            </v-snackbar>
        </div>
    </v-app>
</template>

<script>
import Vue from 'vue'
import Drawer from '../components/Drawer.vue'
import Header from '../components/Header.vue'
import Media from '../components/MediaDialog.vue'
import File from '../components/File.vue'
import Textarea from '../components/RichTextarea.vue'
import Geo from '../components/GeoComplete.vue'
import Paginate from '../components/Paginate.vue'
import Listing from '../components/Listing.vue'

Vue.component('Drawer', Drawer)
Vue.component('Header', Header)
Vue.component('Media', Media)
Vue.component('File', File)
Vue.component('Textarea', Textarea)
Vue.component('Geo', Geo)
Vue.component('Paginate', Paginate)
Vue.component('Listing', Listing)

export default {
    data () {
        return {
            loading: true,
            timeout: 6000,
            media: Media,
            snackbar: {
                text: this.$store.state.snackbar.text,
                status: this.$store.state.snackbar.status,
                color: this.$store.state.snackbar.color
            },
            media_dialog: this.$store.state.media_dialog
        }
    },
    watch: {
        media_dialog (val) {
            if (!val) {
                this.$store.commit('media_dialog', false);
            }
        },
        '$store.state.media_dialog' (val) {
            if (val) {
                this.media_dialog = val;
            }
        },
        '$store.state.snackbar.color' (val) {
            this.snackbar.color = val;
        },
        '$store.state.snackbar.text' (val) {
            console.log(val);
            this.snackbar.text = val;
        },
        '$store.state.snackbar.status' (val) {
            if (val) {
                this.snackbar.status = true;
            }
        },
        'snackbar.status' (val) {
            if (!val) {
                this.$store.commit('snackbar', { status: false });
            }
        }
    }
}
</script>

<style lang="scss">
    $theme_color: #ed4b3f;
    $main_bg: #f2f7fb;
    $font: 'Muli', sans-serif;

    html body .theme--light.v-application, body
    .theme--light.v-application .title, html *
    {
        font-family: $font !Important;
    }

    body .theme--light.v-application ._900 {
        font-weight: 900;
    }

    body .theme--light.v-application {
        background: $main_bg;
    }
    .main-bg {
        background: $main_bg;
    }

    .flex {
        display: flex;
    }

    .flex.column {
        flex-flow: column;
    }

    #_mainAppBar {
        border-color: #ccc !important;
        border-bottom: 1px solid;
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 3;
        margin: -20px !important;
        width: auto;
        max-width: calc(100% + 40px);
        margin-bottom: 16px !important;
    }

    #_logo {
        max-height: 63px;
    }

    .filters {
        height: 70px;
        background: $main_bg
    }

    #_tdID {
        width: 100px;
    }

    #_metas {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    #_metas .caption {
        margin-left: 10px;
    }

    .flex.flex-end {
        justify-content: flex-end;
    }

    .flex.space-between {
        justify-content: space-between;
    }

    #_metasBottom {
        margin-top: 15px;
    }

    .flex.space-around {
        justify-content: space-around
    }

    ._row {
        margin-left: -7.5px;
        margin-right: -7.5px;
    }

    ._row > div {
        margin: 7.5px;
    }

    .fill {
        width: 100%;
    }

    ._componentField {
        padding: 15px;
        border: 1px solid lightblue;
        margin-top: 15px;
        background: #f2f7fb;
    }

    div#_bottomAction {
        position: -webkit-sticky;
        position: sticky;
        bottom: 0;
        width: auto;
        display: flex;
        justify-content: space-between;
        padding: 15px;
        background: #fff;
        border-top: 1px solid #ccc;
        margin-left: -20px;
        margin-right: -20px;
        z-index: 1;
        margin-bottom: -20px;
    }

    #_wrapper {
        position: relative;
    }

    .filters > td:first-of-type {
        width: 100px;
    }

    div#_mediaFooter {
        bottom: 0;
        width: 100%;
        padding: 20px;
        background: #fff;
        border-top: 1px solid #ccc;
        position: sticky;
    }

    #_mediaTabs .v-tabs-bar {
        position: sticky;
        top: 0;
        z-index: 1;
        border-bottom: 1px solid #ccc;
    }

    .flex.row-wrap {
        flex-flow: row wrap;
    }

    .full {
        max-width: 100%;
        width: 100%;
    }
    ._collapse .v-expansion-panel-content__wrap {
        min-height: 100px;
        max-height: 300px;
        overflow: auto;
    }
    body ._filter {
        max-width: 200px;
        width: 100%;
    }

    // #_sideList {
    //     height: 100vh;
    //     overflow-y: auto;
    //     position: sticky;
    //     top: 63px;
    // }

    // #_mainDrawer .v-navigation-drawer__content {
    //     overflow: visible;
    // }

    // #_mainDrawer.v-navigation-drawer--mini-variant {
    //     overflow: visible;
    // }
    #_mainDrawer {
        overflow: visible;
        height: 100vh !important;
        position: sticky;
        top: 0 !important;
        overflow: visible;
    }

    #_sideList .v-list-group__items {
        border-left: 5px solid;
    }

    #_sideList.v-list--dense .v-list-group__items .v-list-item .v-list-item__title {
        font-size: 14px;
        color: #555;
    }

    #_sideList.v-list--dense .v-list-item .v-list-item__title {
      font-size: 16px;
    }

    ._100vh {
      height: 100vh;
    }
</style>
