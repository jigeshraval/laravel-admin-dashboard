<template>
    <v-simple-table
        fixed-header
    >
        <thead>
            <tr>
                <th 
                    v-for="(column, index) in interfaces"
                    :key="index"
                    v-text="column"
                >
                </th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr class="filters my-5">
                <td 
                    v-for="(filter, i) in interfaces"
                    :key="i"
                >
                    <v-text-field
                        hide-details
                        width="50px"
                        solo
                        @input="filterSearch(i)"
                        autocomplete="nope"
                        :placeholder="filter"
                    >
                    </v-text-field>
                </td>
                <td></td>
            </tr>
            <tr v-for="(d, i) in source.data" :key="i">
                <td v-for="(int, index) in interfaces" :key="index">
                    {{ d[index] }}
                </td>
                <td>
                    <v-menu
                        left
                        bottom
                    >
                        <template v-slot:activator="{ on }">
                        <v-btn icon v-on="on">
                            <v-icon>mdi-dots-horizontal</v-icon>
                        </v-btn>
                        </template>
                        <v-list>
                            <v-list-item :to="slug + '/' + d['id']">
                                <v-list-item-title>
                                    <v-icon left>mdi-pencil-outline</v-icon>
                                    Edit
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item color="error" :to="slug + '/delete/' + d['id']">
                                <v-list-item-title>
                                    <v-icon left>mdi-delete-outline</v-icon>
                                    Delete
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </td>
            </tr>
        </tbody>
    </v-simple-table>
</template>

<script>
export default {
    props: {
        interface: {
            default: []
        },
        source: {},
        filters: {},
        slug: null
    },
    data () {
        return {
            interfaces: this.interfaces
        }
    },
    watch: {
        interface (list) {
            this.interfaces = list;
        }
    },
    methods: {
        filterSearch (column) {
            console.log(column);
        }
    }
}
</script>