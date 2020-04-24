<template>
    <div>
        <Header
          heading="Post Category"
        >
            <v-btn
                to="/blog/category/add"
                color="info"
            >
                <v-icon left>mdi-view-list</v-icon>
                Add
            </v-btn>
        </Header>
        <Listing
            endpoint="/post/category"
            slug="/blog/category"
            component="post_category"
        >
        </Listing>
    </div>
</template>

<script>
export default {
    data () {
        return {
            obj: [],
            slug: '/post/category',
            totalPage: null
        }
    },
    beforeCreate () {

    },

}
</script>
