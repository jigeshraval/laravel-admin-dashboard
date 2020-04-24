<template>
    <div>
        <Header
          heading="Page"
        >
            <v-btn
                to="/page/add"
                color="info"
            >
                <v-icon left>mdi-view-list</v-icon>
                Add
            </v-btn>
        </Header>
        <Listing
            endpoint="/page"
            slug="/page"
            component="page"
        >
        </Listing>
    </div>
</template>

<script>
export default {
    data () {
        return {
            obj: [],
            slug: '/page',
            totalPage: null
        }
    }
}
</script>
