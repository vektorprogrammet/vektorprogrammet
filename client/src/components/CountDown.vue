<template>
    <div id="countdown">
        <flip-countdown deadline = "2019-08-28 23:59:59"></flip-countdown>
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

    #countdown {

        .flip-card__top,
        .flip-card__bottom,
        .flip-card__back-bottom,
        .flip-card__back::before,
        .flip-card__back::after {
            color: #CCC !important;
        }

        .flip-card__bottom,
        .flip-card__back-bottom {
            color: #FFF !important;
        }

    }



</style>
