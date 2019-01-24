<template>
    <div>
        <flip-countdown deadline = "2019-01-29 23:59:59"></flip-countdown>
    </div>
    
</template>

<script>

    import FlipCountdown from 'vue2-flip-countdown'

    export default {
        name: "CountDown",
        components: {FlipCountdown},
    }
</script>

<style scoped>
    .flip-clock__piece flip{
        color: #0b93d5;
    }

</style>