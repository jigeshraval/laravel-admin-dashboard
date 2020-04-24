<template>
    <div>
        <Header
          heading="Post"
        >
            <v-btn
                to="/post/add"
                color="info"
            >
                <v-icon left>mdi-view-list</v-icon>
                Add
            </v-btn>
        </Header>
        <Listing
            endpoint="/post"
            slug="/post"
            component="post"
        >
        </Listing>
    </div>
</template>

<script>
export default {
    data () {
        return {
            obj: [],
            slug: '/post',
            totalPage: null
        }
    }
}
</script>
