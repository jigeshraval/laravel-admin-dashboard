<template>
    <div>
        <v-text-field
            v-model="meta.meta_title"
            label="Meta Title"
            name="meta_title"
            autocomplete="nope"
        ></v-text-field>
        <v-textarea
            v-model="meta.meta_description"
            label="Meta Description"
            name="meta_description"
            autocomplete="nope"
        ></v-textarea>
    </div>
</template>

<script>
export default {
    props: {
        meta: {
            meta_title: '',
            meta_description: ''
        }
    }
}
</script>