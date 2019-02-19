<template>
  <b-container>
    <PageHeader>
      <h1>Innlogging</h1>
    </PageHeader>
    <b-row>
      <b-col cols="12" md="6" offset-md="3">
        <b-form @submit="onSubmit">
          <b-form-group>
            <label
              >Brukernavn / e-post
              <b-form-input type="text" v-model="form.username" required>
              </b-form-input>
            </label>
          </b-form-group>
          <b-form-group>
            <label
              >Passord
              <b-form-input
                id="exampleInput2"
                type="password"
                v-model="form.password"
                required
              >
              </b-form-input>
            </label>
          </b-form-group>
          <p class="d-flex justify-content-between">
            <b-button type="submit" variant="primary">Logg inn</b-button>
            <b-link href="#" class="ml-auto">Glemt passord?</b-link>
          </p>
        </b-form>
      </b-col>
    </b-row>
  </b-container>
</template>

<script lang="ts">
import PageHeader from '../components/PageHeader.vue';
import Vue from 'vue';
import {Component} from 'vue-property-decorator';
import {Action} from 'vuex-class';
import {Credentials} from '../store/account.module';

@Component({
  components: {PageHeader},
})
export default class LoginView extends Vue {
  @Action('login', {namespace: 'account'}) public login: any;
  public form: Credentials = {
    username: '',
    password: '',
  };
  public onSubmit(evt: any) {
    evt.preventDefault();
    this.login(this.form).then(() => {
      this.$router.push({name: 'my_page'});
    });
  }
}
</script>

<style scoped>
label {
  width: 100%;
}
</style>
