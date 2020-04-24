<template>
    <div>
        <v-form id="postAdd" ref="postAdd" @submit.prevent="postAdd" autocomplete="nope">
            <Header
              :heading="getHeading()"
            >
              <v-switch
                  hide-details
                  class="mr-8"
                  v-model="post.published"
                  inset
                  name="published"
                  :label="!post.published ? 'Draft' : 'Publish'"
                  color="success"
                  :value="post.published"
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
                    to="/post"
                    color="info"
                >
                    <v-icon left>mdi-view-list</v-icon>
                    list
                </v-btn>
            </Header>
            <v-layout class="row wrap">
              <v-flex class="col sm12 xs12 md9 xl9">
                <v-card class="full">

                  <v-tabs
                    background-color="white"
                    color="info accent-4"
                    left
                  >
                    <v-tab>Information</v-tab>
                    <v-tab>SEO</v-tab>
                    <v-tab-item class="pa-5" :eager="true">
                      <div class="full">
                        <v-text-field
                          v-model="post.title"
                          label="Title*"
                          name="title"
                          required
                          :rules="validateRules"
                          autocomplete="nope"
                        ></v-text-field>

                        <v-text-field
                          v-model="post.url"
                          label="Url*"
                          type="text"
                          :rules="validateRules"
                          required
                          name="url"
                          autocomplete="nope"
                        ></v-text-field>

                        <Textarea
                            autocomplete="nope"
                            outlined
                            name="content"
                            label="Content"
                            :value="post.content"
                        ></Textarea>
                      </div>
                    </v-tab-item>

                   <v-tab-item class="pa-5" :eager="true">
                     <SEO
                        :meta="post"
                     >
                     </SEO>
                   </v-tab-item>
                 </v-tabs>

                </v-card>

              </v-flex>

              <v-flex class="col sm12 xs12 md3 xl3">
                <div justify="center">
                  <v-expansion-panels
                  accordion
                  v-model="panel">

                    <v-expansion-panel class="_collapse">
                      <v-expansion-panel-header>Post Date</v-expansion-panel-header>
                      <v-expansion-panel-content :eager="true">
                        <v-date-picker
                           v-model="post.post_date"
                           color="green lighten-1"
                         ></v-date-picker>
                         <input type="hidden" name="post_date" :value="post.post_date" />
                      </v-expansion-panel-content>
                    </v-expansion-panel>

                    <v-expansion-panel class="_collapse">
                        <v-expansion-panel-header>Categories</v-expansion-panel-header>
                        <v-expansion-panel-content :eager="true">
                          <v-checkbox
                            v-for="(cat, i) in post.category"
                            :key="i"
                            hide-details
                            :value="cat.id"
                            name="category[]"
                            v-model="post.selected_category"
                            :label="cat.name"
                          ></v-checkbox>

                        </v-expansion-panel-content>
                    </v-expansion-panel>

                    <v-expansion-panel>
                      <v-expansion-panel-header>Media</v-expansion-panel-header>
                      <v-expansion-panel-content :eager="true">
                          <File
                              block
                              cls="_block"
                              :value="post.image"
                              text="Choose Image"
                              name="id_media"
                              :multiple="false"
                          ></File>
                      </v-expansion-panel-content>
                    </v-expansion-panel>

                  </v-expansion-panels>
                </div>
              </v-flex>
            </v-layout>
      </v-form>
    </div>
</template>
<script>
  export default {
    beforeCreate() {
      var url = '/post/add';
      if (this.$router.history.current.params && this.$router.history.current.params.id) {
        url = '/post/edit/' + this.$router.history.current.params.id;
      }
      return this.$axiosx.get(url)
      .then((res) => {
          this.post = res.data.post;
          if (this.post.display_home) {
            this.post.display_home == true;
          }

          if (this.post.main_post) {
            this.post.main_post == true;
          }

          console.log(res.data.post);

      });
    },
    data () {
      return {
        post: [],
        panel:0,
        validateRules: [
          v => !!v || 'This field is required'
        ],
        items: [],
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
        getName (val) {
            console.log(val);
        },
        getID (val) {
            console.log(val);

        },
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
          if (this.post && this.post.title) {
                return 'Post: ' + this.post.title;
          }

          return 'Add Post';
      },
      postAdd () {
        if (this.$refs.postAdd.validate() == false) {
                this.$store.commit('snackbar', {
                  status: 'error',
                  text: 'Please supply mandatory fields.'
                });
                return true;
        }
        var fd = new FormData(this.$refs.postAdd.$el);
        this.dialog = true;
        var url = '/post/add';
        if (this.$router.history.current.params && this.$router.history.current.params.id) {
          url = '/post/edit/' + this.$router.history.current.params.id;
        }
        this.$axiosx.post(url, fd).then((res) => {
          if (res.data.status == 'error') {
            this.$store.commit('snackbar', res.data);
            this.dialog = false;
          }

          if (res.data.status == 'redirect') {
            this.$router.push({
              path: res.data.text,
              query: { added: 'true' }
            });
            this.dialog = false;
            this.addedManufacturer();
          }
          if (res.data.status == 'success') {
              this.$store.commit('snackbar', res.data);
              this.dialog = false;
          }
        });
      },
      saveManufacturer () {
        this.$refs.postAdd.submit();
      },
      addedManufacturer() {
        if (this.$router.history.current.query.added == 'true') {
          this.$store.commit('snackbar', {
            status: 'success',
            text: 'Post Added Successfully'
          });
        }
      }
    }
  }
</script>
