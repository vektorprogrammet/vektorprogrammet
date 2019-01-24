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

        <div>
            <CountDown></CountDown>
        </div>
    </div>

</template>

<script>
    import TweenLite from 'gsap'
    import axios from 'axios'
    import CountDown from "../../components/CountDown";
    export default {
        name: "PartyPageView",
        components: {CountDown},
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
            inc_number_of_applicants_anim: function (initValue, animLength) {
                TweenLite.fromTo(this.$data, animLength, {tweenedNumber: this.tweenedNumber},{ tweenedNumber: initValue });
                this.tweenedNumber = initValue;
            }
        },



        mounted () {
            axios
                .get('http://10.22.20.43:8080/api/party/application_count/1/')
                .then(response => {
                    if(response.data !== c){
                        this.inc_number_of_applicants_anim(response.data, 10);

                    }
                });

            let c = this.tweenedNumber;
            window.setInterval(()=>{
                axios
                    .get('http://10.22.20.43:8080/api/party/application_count/1/')
                    .then(response => {
                        if(response.data !== c){
                            this.inc_number_of_applicants_anim(response.data, 1);
                            c = this.tweenedNumber;

                        }
                    });
            }, 2000);
        },
    }


</script>

<style scoped lang="scss">

</style>