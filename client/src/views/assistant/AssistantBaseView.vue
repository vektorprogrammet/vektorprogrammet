<template>
    <div class="assisstant-base">
        <router-view></router-view>
    </div>
</template>


<script>
    import AssistantNav from '../../components/AssistantNav.vue'
    export default {
        name: 'AssistantBaseView',
        components: {AssistantNav},
    }
</script>
