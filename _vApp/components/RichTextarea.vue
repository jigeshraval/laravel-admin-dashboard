<template>
    <div class="mb-5 mt-5">
        <div class="v-label theme--light mb-3" v-text="label"></div>
        <textarea
            :id="name"
            class="editor"
            :name="name"
            v-html="value"
        >
        </textarea>
    </div>
</template>

<script>
import $ from 'jquery'
import summernote from 'summernote/dist/summernote-lite.js'
import 'summernote/dist/summernote-lite.css'

export default {
    props: {
        label: {
            default: 'Content'
        },
        name: {
            default: 'content'
        },
        value: {}
    },
    watch: {
        value (val) {
            if (val) {
                $('#' + this.name).summernote('code', val);
            }
        }
    },
    mounted () {
        $('#' + this.name).summernote({
            tooltip: false,
            height: 'auto',
            minHeight: '200px',
            maxHeight: '700px'
        });
    }
}
</script>
