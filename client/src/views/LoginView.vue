<template>
  <b-container>
    <PageHeader>
      <h1>Innlogging</h1>
    </PageHeader>
    <b-row>
      <b-col cols="12" md="6" offset-md="3">
        <b-form @submit="onSubmit">
          <b-form-group>
            <label>Brukernavn / e-post
              <b-form-input type="text"
                            v-model="form.username"
                            required>
              </b-form-input>
            </label>
          </b-form-group>
          <b-form-group>

            <label>Passord
              <b-form-input id="exampleInput2"
                            type="password"
                            v-model="form.password"
                            required>
              </b-form-input>
            </label>
          </b-form-group>
          <p v-if="error" >
            Obs: {{error}}
          </p>
          <p class="d-flex justify-content-between">
            <b-button type="submit" variant="primary">Logg inn</b-button>
            <b-link href="#" class="ml-auto">Glemt passord?</b-link>
          </p>
        </b-form>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
  import PageHeader from '../components/PageHeader';
  import { mapActions } from 'vuex';

  export default {
    name: 'LoginView',
    components: {PageHeader},
    data() {
      return {
        form: {
          username: '',
          password: '',
        },
        error: null
      };
    },
    methods: {
      ...mapActions('account', ['login']),
      async onSubmit(evt) {
        this.error = null;
        evt.preventDefault();
        await this.login(this.form)
        if (this.$store.getters['account/user'].loaded) {
          this.$router.push({name: 'my_page'});
        } else {
          this.error = "Feil brukernavn/passord";
        }

      },
    },
  };
</script>

<style scoped>
  label {
    width: 100%;
  }
</style>
