<template>
    <div>
        <v-form id="postCategory" ref="postCategory" @submit.prevent="postCategory" autocomplete="nope">
            <Header
              :heading="getHeading()"
            >
            <v-switch
                hide-details
                class="mr-8"
                v-model="post_category.status"
                inset
                name="status"
                label="Status"
                color="success"
                :value="post_category.status"
            ></v-switch>
              <v-btn
                  min-width="130px"
                  color="success"
                  class="mr-5"
                  type="submit"
                  :loading="$store.state.loading"
              >
                  <v-icon left>mdi-content-save-outline</v-icon>
                  Save
              </v-btn>
              <v-btn
                  to="/blog/category"
                  color="info"
              >
                  <v-icon left>mdi-view-list</v-icon>
                  list
              </v-btn>
            </Header>
            <v-card class="full">
                 <v-tabs
                   background-color="white"
                   color="info accent-4"
                   left
                 >
                 <v-tab>Information</v-tab>
                 <v-tab>SEO</v-tab>
                 <v-tab-item class="pa-5" :eager="true">
                   <v-text-field
                     v-model="post_category.title"
                     label="Name*"
                     name="name"
                     required
                     :rules="validateRules"
                     autocomplete="nope"
                   ></v-text-field>

                   <v-text-field
                     v-model="post_category.url"
                     label="Url*"
                     type="text"
                     required
                     :rules="validateRules"
                     name="url"
                     autocomplete="nope"
                   ></v-text-field>

                 </v-tab-item>

              <v-tab-item :eager="true" class="pa-5">
                <SEO
                    :meta="post_category"
                >
                </SEO>
              </v-tab-item>
            </v-tabs>
          </v-card>
      </v-form>
    </div>
</template>
<script>
import Vue from 'vue'

  export default {
    beforeCreate() {
      var url = '/post/category/add';
      if (this.$router.history.current.params && this.$router.history.current.params.id) {
        url = '/post/category/edit/' + this.$router.history.current.params.id;
      }
      return this.$axiosx.get(url)
      .then((res) => {
        this.post_category = res.data.post_category;

        if (this.post_category.status == 1) {
          this.post_category.status == true;
        } else {
          this.post_category.status == false;
        }

      });
    },
    data () {
      return {
        post_category: [],
        validateRules: [
          v => !!v || 'This field is required'
        ],
        emailRules: [
          v => !!v || 'E-mail is required',
          v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
        ]
      }
    },
    watch : {
      '$route.query.added' : function (val) {
          this.getData();
      }
    },
    methods: {
      getData () {
        if (this.$route.params && this.$route.params.id) {
              var url = '/post/edit/' + this.$route.params.id;
              return this.$axiosx.get(url)
              .then((res) => {
                  this.plans = res.data.plans;
              });
          }
      },
      getHeading () {
          if (this.post && this.post.name) {
                return 'Post Category: ' + this.post.name;
          }

          return 'Add Category';
      },
      postCategory () {
        if (this.$refs.postCategory.validate() == false) {
            this.$store.commit('snackbar', {
                status: 'error',
                text: 'Please supply mandatory fields.'
            });
            return true;
        }
        var fd = new FormData(this.$refs.postCategory.$el);
        this.dialog = true;
        var url = '/post/category/add';
        if (this.$router.history.current.params && this.$router.history.current.params.id) {
          url = '/post/category/edit/' + this.$router.history.current.params.id;
        }
        this.$axiosx.post(url, fd).then((res) => {
          if (res.data.status == 'error') {
            this.$store.commit('snackbar', res.data);
          }

          if (res.data.status == 'redirect') {
            this.$router.push({
              path: res.data.text,
              query: { added: 'true' }
            });
            this.added();
          }
          if (res.data.status == 'success') {
              this.$store.commit('snackbar', res.data);
          }
        });
      },
      added() {
        if (this.$router.history.current.query.added == 'true') {
          this.$store.commit('snackbar', {
            status: 'success',
            text: 'Post Category Added Successfully'
          });
        }
      }
    }
  }
</script>
