<template>
    <div id="party_page_view">
        <div id="Title">
            <h1 class="deepshadow">Vektor Party</h1>
        </div>

        <div id="applicants_div">
            <h1 class="elegantshadow">{{ animatedNumber }}</h1>
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
                number_of_applicants: 0
            }
        },

        computed: {
            animatedNumber: function() {
                return this.number_of_applicants.toFixed(0);
            },
        },

        methods:{
            inc_number_of_applicants_anim: function (initValue, animLength) {
                TweenLite.fromTo(this.$data, animLength, {number_of_applicants: this.number_of_applicants},{ number_of_applicants: initValue });
                this.number_of_applicants = initValue;
            }
        },



        mounted () {

            let c = this.number_of_applicants;
            window.setInterval(()=>{
                axios
                    .get('http://10.22.20.43:8080/api/party/application_count/1/')
                    .then(response => {
                        if(response.data !== c){
                            this.inc_number_of_applicants_anim(response.data, 3);
                            c = this.number_of_applicants;

                        }
                    });
            }, 3010);
        },
    }


</script>

<style scoped lang="scss">

    #party_page_view{
        background-color: #e7e5e4;
        height:100vh;
        p {
            text-align: center;
            font-size: 10em;
        }

        h1 {
            font-family: "Avant Garde", Avantgarde, "Century Gothic", CenturyGothic, "AppleGothic", sans-serif;
            font-size: 92px;
            padding: 80px 50px;
            text-align: center;
            text-transform: uppercase;
            text-rendering: optimizeLegibility;

            &.elegantshadow {
                color: #131313;
                background-color: #e7e5e4;
                letter-spacing: .15em;
                text-shadow:
                        1px -1px 0 #767676,
                        -1px 2px 1px #737272,
                        -2px 4px 1px #767474,
                        -3px 6px 1px #787777,
                        -4px 8px 1px #7b7a7a,
                        -5px 10px 1px #7f7d7d,
                        -6px 12px 1px #828181,
                        -7px 14px 1px #868585,
                        -8px 16px 1px #8b8a89,
                        -9px 18px 1px #8f8e8d,
                        -10px 20px 1px #949392,
                        -11px 22px 1px #999897,
                        -12px 24px 1px #9e9c9c,
                        -13px 26px 1px #a3a1a1,
                        -14px 28px 1px #a8a6a6,
                        -15px 30px 1px #adabab,
                        -16px 32px 1px #b2b1b0,
                        -17px 34px 1px #b7b6b5,
                        -18px 36px 1px #bcbbba,
                        -19px 38px 1px #c1bfbf,
                        -20px 40px 1px #c6c4c4,
                        -21px 42px 1px #cbc9c8,
                        -22px 44px 1px #cfcdcd,
                        -23px 46px 1px #d4d2d1,
                        -24px 48px 1px #d8d6d5,
                        -25px 50px 1px #dbdad9,
                        -26px 52px 1px #dfdddc,
                        -27px 54px 1px #e2e0df,
                        -28px 56px 1px #e4e3e2;
            }
            &.deepshadow {
                color: #e0dfdc;
                background-color: #333;
                letter-spacing: .1em;
                text-shadow:
                        0 -1px 0 #fff,
                        0 1px 0 #2e2e2e,
                        0 2px 0 #2c2c2c,
                        0 3px 0 #2a2a2a,
                        0 4px 0 #282828,
                        0 5px 0 #262626,
                        0 6px 0 #242424,
                        0 7px 0 #222,
                        0 8px 0 #202020,
                        0 9px 0 #1e1e1e,
                        0 10px 0 #1c1c1c,
                        0 11px 0 #1a1a1a,
                        0 12px 0 #181818,
                        0 13px 0 #161616,
                        0 14px 0 #141414,
                        0 15px 0 #121212,
                        0 22px 30px rgba(0, 0, 0, 0.9);
            }
            &.insetshadow {
                color: #202020;
                background-color: #2d2d2d;
                letter-spacing: .1em;
                text-shadow:
                        -1px -1px 1px #111,
                        2px 2px 1px #363636;
            }

        }

    }


</style>