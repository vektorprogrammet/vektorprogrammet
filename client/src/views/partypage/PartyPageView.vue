<template>
    <div>
        <div id="Title">
            <h1>Vektor Party</h1>
        </div>

        <div id="animated-number-demo">
            <input v-model.number="number" type="number" step="20">
            <p>{{ animatedNumber }}</p>
        </div>

        <div id="number-of-applicants">
            <p>{{ number_of_applicants }}</p>
        </div>
    </div>

</template>

<script>
    import TweenLite from 'gsap'
    import axios from 'axios'
    export default {
        name: "PartyPageView",
        data() {
            return {
                number: 0,
                tweenedNumber: 0,
                number_of_applicants: 0
            }
        },

        computed: {
            animatedNumber: function() {
                return this.tweenedNumber.toFixed(0);
            },
        },
        watch: {
            number: function(newValue) {
                TweenLite.to(this.$data, 10, { tweenedNumber: newValue });
            }
        },

        methods:{
            init_number_of_applicants_anim: function (initValue) {
                TweenLite.fromTo(this.$data, 10, {tweenedNumber: 0},{ tweenedNumber: initValue });
            }
        },

        mounted () {
            axios
                .get('localhost:8000/')
                .then(this.tweenedNumber = 100)
                .then(response => ( dataa = response ))

            this.init_number_of_applicants_anim(this.tweenedNumber);



        }
    }
</script>

<style scoped lang="scss">

</style>