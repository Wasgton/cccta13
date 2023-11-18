<script setup lang="ts">
  import axios from "axios";
  import {ref} from "vue";

  const input = ref({
    name:"",
    email:"",
    cpf:"",
    isPassenger:false,
    isDriver: false,
  });
  const accountId = ref("");
  async function signUp(){
    const response = await axios.post("http://localhost/signup", input.value);
    const output = response.data;
    accountId.value = output.accountId;
  }
</script>
<template>
  <div>
    <h2 class="signup-title">SignUp</h2>
    <input class="signup-name" v-model="input.name">
    <input class="signup-email" v-model="input.email">
    <input class="signup-cpf" v-model="input.cpf">
    <input class="signup-is-passenger" type="checkbox" v-model="input.isPassenger">
    <button class="signup-submit" @click="signUp()"></button>
    <span class="signup-account-id">{{AccountId}}</span>
  </div>
</template>

<style scoped>
</style>
