<template>
    <div id="party_page_view">

        <transition
                v-on:leave="overlay_leave"
                v-bind:css="false">
            <div v-if="show" id="overlay">
                <h1 id="overlay-title">Vektor Party</h1>
            </div>
        </transition>

        <transition
                v-on:leave="button_leave"
                v-bind:css="false">
            <button v-if="show" id="btn-intro" v-on:click="btn_intro_click">My body is ready!</button>
        </transition>

        <div id="Title">
            <h1 class="deepshadow">Vektor Party</h1>
        </div>

        <div id="applicants_div" >
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
    import Velocity from 'velocity-animate';

    export default {
        name: "PartyPageView",
        components: {CountDown},
        data() {
            return {
                number: 0,
                number_of_applicants: 0,
                show: true,
                fetching_api: false,
                last_number_of_applicants: 0,
            }
        },

        computed: {
            animatedNumber: function() {
                return this.number_of_applicants.toFixed(0);
            },
        },

        methods:{
            btn_intro_click: function(){
                let overlay = document.getElementById('overlay');
                //overlay.style.display = "none";
                this.show = false;
                axios
                    .get('http://10.22.20.43:8080/api/party/application_count/1/')
                    .then(response => {
                        this.inc_number_of_applicants_anim(response.data, 10);
                        this.fetching_api = true;
                        this.last_number_of_applicants = response.data;
                    });
            },

            inc_number_of_applicants_anim: function (initValue, animLength) {
                TweenLite.fromTo(this.$data, animLength, {number_of_applicants: this.number_of_applicants},{ number_of_applicants: initValue });
                this.number_of_applicants = initValue;
            },

            play_notification_sound: function(){
                let sound = new Audio(require('../../assets/johncenaintro.mp3'));
                let sound2 = new Audio('http://159.65.58.116/Sigurd%20%20%20%20');
                let sound3 = new Audio(require('../../assets/johncenaout.mp3'));


                sound2.addEventListener('loadeddata', function(){
                    if(sound2.readyState === 4){
                        sound.play();
                    }
                });

                sound.addEventListener('ended', function(){
                    sound2.play();
                });

                sound2.addEventListener('ended', function(){
                    sound3.play();
                });
            },


            button_leave: function (el, done) {
                Velocity(el, { translateX: '15px', rotateZ: '50deg' }, { duration: 200 })
                Velocity(el, { rotateZ: '100deg' }, { loop: 2 })
                Velocity(el, {
                    rotateZ: '45deg',
                    translateY: '30px',
                    translateX: '30px',
                    opacity: 0
                }, { complete: done })
            },

            overlay_leave: function (el, done) {
                Velocity(el, {opacity: 0}, { complete: done })
            },


        },

        mounted () {
            window.setInterval(()=>{
                axios
                    .get('http://10.22.20.43:8080/api/party/application_count/1/')
                    .then(response => {
                        if(this.fetching_api && response.data !== this.last_number_of_applicants){
                            this.last_number_of_applicants = response.data;
                            this.inc_number_of_applicants_anim(response.data, 3);
                            this.play_notification_sound();
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
                color: rgba(73, 72, 77, 0.52);
               // background-color: #333;
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
               // background-color: #2d2d2d;
                letter-spacing: .1em;
                text-shadow:
                        -1px -1px 1px #111,
                        2px 2px 1px #363636;
            }

        }

        #overlay{
            height: 100vh;
            width: 100vw;
            position: fixed;
            background-color: #6FCDEE;
            z-index: 10;
        }


        #btn-intro{
            position: fixed;
            z-index: 11;
            width: 20em;
            height: 10em;
            top: calc(50% - 3em);
            left: calc(50% - 10em);
        }



    }


</style>