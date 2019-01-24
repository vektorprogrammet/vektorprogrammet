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

<style lang="scss">

    .flip-card__top,
    .flip-card__bottom,
    .flip-card__back-bottom,
    .flip-card__back::before,
    .flip-card__back::after {
        color: #0982c4 !important;
    }

    .flip-card__bottom,
    .flip-card__back-bottom {
        color: #0b93d5 !important;
    }

</style>