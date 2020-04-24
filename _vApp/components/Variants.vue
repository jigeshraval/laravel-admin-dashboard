<template>
    <div class="_list _variant_list" v-if="variants">
        <div class="_inner_list" v-for="(variant, index) in variants" :key="index">
            <div class="flex space-between">
                <h3 v-text="variant.label"></h3>
                <!-- <v-icon>mdi-delete</v-icon> -->
            </div>
            <p v-text="variant.desc"></p>
            <v-simple-table>
                <thead>
                    <tr>
                        <th v-for="(head, ind) in variant.option.heads" :key="ind" v-text="head"></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>


                    <tr>

                        <td v-for="(input, ind) in variant.option.inputs" :key="ind">
                            {{ variant }} 
                            <v-select
                                hide-details
                                outlined
                                flat
                                v-if="input.type == 'select'"
                                :name="input.name"
                                :items="input.options"
                                item-text="value"
                                item-value="id"
                            >
                            </v-select>

                            <v-text-field
                                hide-details
                                outlined
                                v-if="input.type == 'text'"
                                :name="input.name"
                                :suffix="input.measurement_name"
                            >

                            </v-text-field>
                        </td>
                        <td>
                            <v-icon>mdi-delete</v-icon>
                        </td>

                    </tr>
                </tbody>
                <tfoot>

                </tfoot>
            </v-simple-table>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        variants: {
            default: null
        }
    }
}
</script>

<style>
    ._variant_list ._inner_list {
        border: 1px solid #f0f0f0;
        margin-bottom: 15px;
        padding: 15px;
        background: #f6f6f6;
    }

    ._variant_list h3 {
        font-size: 16px;
        font-weight: bold;
        margin: 0;
    }

    ._inner_list table th {
        border: 1px solid #ddd;
        background: #fff;
        font-size: 16px;
        font-weight: normal;
        color: #000 !important;
    }

    ._inner_list table td {
        border: 1px solid #ddd;
        padding: 15px;
        background: #fff;
    }
</style>
