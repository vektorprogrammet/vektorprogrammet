<template>
    <div class="adminbaseview">
        <AdminNav></AdminNav>
        <router-view></router-view>
    </div>
</template>

<script>
import AdminNav from '../../components/AdminNav';

export default {
  name: 'AdminBaseView',
  components: { AdminNav },
};
</script>
