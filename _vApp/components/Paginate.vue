<template>
    <div class="text-center" v-if="totalPage">
        <v-pagination
          v-model="paginate"
          :total-visible="7"
          :length="totalPage"
        ></v-pagination>
    </div>

</template>

<script>
export default {
    props: {
        current_page: {
            default: 1
        },
        length: {
            default: null
        },
        totalPage: {
            default: null
        }
    },
    data () {
        return {
            paginate: +this.current_page
        }
    },
    methods: {
        getPaginate (end) {
            var end = +end + 1;
            var start = 1;
            return new Array(end - start).fill().map((d, i) => i + start);
        },
    },
    watch: {
        current_page (page) {
            this.paginate = page;
        },
        paginate (val) {
            this.$emit('paginate', val);
        }
    }
}
</script>
