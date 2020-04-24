import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        loading: false,
        test: 'jigesh',
        snackbar: {
            status: false,
            text: '',
            color: 'error'
        },
        api: 'http://127.0.0.1:8000',
        media_dialog: false,
        selectedMedia: [],
        selectedMediaIds: [],
        selectMultipleMedia: true,
        mediaSelectionFinished: false,
        mediaType: 'Image',
        drawerVisibility: true
    },
    mutations: {
        setDrawerVisibility(state, val) {
            state.drawerVisibility = val;
        },
        setMediaType(state, type) {
            state.mediaType = type;
        },
        snackbar(state, data) {

            state.snackbar.status = data.status;

            if (!data.text) {
                data.text = '';
            }

            state.snackbar.text = data.text;

            if (data && data.status && data.status == 'success') {
                state.snackbar.color = "success";
            }

        },
        loading(state, status) {
            state.loading = status;
        },
        setSelectMultipleMedia(state, data) {
            state.selectMultipleMedia = data;
        },
        mediaSelectionFinished(state, data) {
            state.mediaSelectionFinished = data;
        },
        setSelectedMedia(state, array) {
            state.selectedMedia = array;
        },
        setSelectedMediaIds(state, array) {
            state.selectedMediaIds = array;
        },
        media_dialog(state, status) {
            state.media_dialog = status;
        },
    }
});

Vue.prototype.$store = store;

export default store;
