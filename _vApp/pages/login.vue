<template>
  <v-container class="flex justify-center align-center _100vh column">
    <div class="text-center mb-10">    
        <h1 style="color: #777">AdLara</h1>
        <small>3.0.1</small>
    </div>
    <v-card width="650">

      <v-card-text class="pa-8">
          <v-form ref="loginForm" @submit.prevent="logIn">
              <v-text-field
                label="E-Mail Address*"
                required
                v-model="email"
                :rules="validateRules"
                type="email"
                class="mb-3"
              >
              </v-text-field>
              <v-text-field
                type="password"
                label="Password*"
                :rules="validateRules"
                required
                v-model="password"
              >
              </v-text-field>

              <v-checkbox
                label="Keep me logged in"
                v-model="keep_active"
                color="success"
              ></v-checkbox>

              <v-btn
                type="submit"
                large
                class="mt-3 mb-3 pl-8 pr-8"
                color="info"
                :loading="loading"
              >Sign-In</v-btn>
          </v-form>

      </v-card-text>

    </v-card>

  </v-container>
</template>

<script>
export default {
  data () {
    return {
      email: null,
      password: null,
      loading: false,
      keep_active: false,
      validateRules: [
        v => !!v || 'This field is required'
      ],
    }
  },
  beforeCreate () {
    this.$store.commit('setDrawerVisibility', false);

    this.$axiosx.get('/employee/check/login')
    .then((res) => {

       if (res.data.status == 'success') {
         this.$store.commit('setDrawerVisibility', true);
         this.$router.push('/pages')
       }

    })
  },
  methods: {
    logIn () {

      if (!this.$refs.loginForm.validate()) {
          this.$store.commit('snackbar', {
            status: 'error',
            text: 'Please supply mandatory fields.'
          });
          return true;
      }

      this.loading = true;

      var data = {
          email: this.email,
          password: this.password,
          keep_active: this.keep_active
      };

      this.$axiosx.post('/employee/login', data)
      .then((res) => {

          this.loading = false;

          if (res.data.status == 'success') {

            this.$store.commit('setDrawerVisibility', true);
            this.$router.push('/pages');
            return true;

          }

          this.$store.commit('snackbar', {
            status: 'error',
            text: res.data.message
          });

      }).catch((e) => {

        this.loading = false;

        var res = e.response;
        console.log(res);

      });


    }
  }
}
</script>
