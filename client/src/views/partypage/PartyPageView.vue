<template>
    <div id="party_page_view">
        <transition
                v-on:leave="overlay_leave"
                v-bind:css="false">
            <div v-if="show" id="overlay" v-bind:style="bgcIntro">
                <Vektorlogo></Vektorlogo>
                <h1 class="Title deepshadow">Party</h1>
            </div>
        </transition>

        <transition
                v-bind:css="false">
            <button v-if="show" id="btn-intro" v-on:click="btn_intro_click" class="bdeepshadow insetshadow">My body is ready!</button>
        </transition>

        <div class="animatedBackground color-anim" v-bind:style="bgc">
            <div class = "Title color-anim">
                <Vektorlogo class="vlogo" ref="mainlogo"></Vektorlogo>
                <h1 class="deepshadow" v-bind:style="tc">Party</h1>
            </div>

            <div id="applicants_div" >
                <h1 class="elegantshadow color-anim" id="applicants_number" v-bind:style="tc2"> {{ animatedNumber }}</h1>
            </div>

            <transition name="slide-fade">
                <h1 v-show="show_newest_applicant" v-if="show_newest_applicant" id="newest_applicant" class="deepshadow">{{ newest_applicant }} er Vektors nyeste søker!</h1>
            </transition>

            <div id="CountDown">
                <CountDown></CountDown>
            </div>

            <div id="Dev-tools">
                <button id="btn-last-1" v-on:click="devReplayLast1">Add 1</button>
                <button id="btn-last-2" v-on:click="devReplayLast2">Add 2</button>
                <button id="btn-last-3" v-on:click="devReplayLast3">Add 3</button>
                <button id="btn-last-4" v-on:click="devReplayLast4">Add 4</button>
                <button id="btn-last-5" v-on:click="devReplayLast5">Add 5</button>
            </div>

        </div>


        <div class="center-point"></div>
    </div>

</template>

<script>
    import TweenLite from 'gsap'
    import axios from 'axios'
    import CountDown from "../../components/CountDown";
    import Velocity from 'velocity-animate';
    import { confetti } from 'dom-confetti';
    import Vektorlogo from "../../components/Vektorlogo";

    export default {
        name: "PartyPageView",
        components: {Vektorlogo, CountDown},
        data() {
            return {
                number: 0,
                sliding_number_of_applicants: 0,
                show: true,
                fetching_api: false,
                last_number_of_applicants: 0,
                new_users: [],
                newest_applicant: 'Viktor Johansen',
                show_newest_applicant: false,

                bgcIntro: {
                    backgroundColor: '',
                },

                bgc: {
                    backgroundColor: '',
                },

                tc: {
                    color: '',
                },

                tc2: {
                    color: '',
                    fontSize: '',
                },

                vectorlogo1: {
                    fill: '',
                },

                vectorlogo2: {
                    fill: '',
                }
            }

        },

        computed: {
            animatedNumber: function() {
                return this.sliding_number_of_applicants.toFixed(0);
            },
        },

        watch: {
            fetching_api: function () {
                this.fetch_applicants();
                this.pop_applicants();

            }
        },

        methods:{
            fetch_applicants: function(){
                window.setInterval(()=>{
                    axios
                        .get('/api/party/application_count/1/')
                        .then(response => {
                            if(this.last_number_of_applicants !== response.data){
                                let new_applicants = response.data - this.last_number_of_applicants;
                                this.add_users(new_applicants, this.last_number_of_applicants);
                                this.inc_number_of_applicants_anim(response.data, 3);
                                this.last_number_of_applicants = response.data;
                            }
                        });
                }, 3000);
            },

            pop_applicants: function(){
                window.setInterval(() => {
                    if (this.new_users.length > 0 && !this.show_newest_applicant){
                        this.show_newest_applicant = true;
                        let [user, applicant_number] = this.new_users.pop();
                        if (applicant_number % 10 === 0) {
                            this.play_10s_notification_sound(user.firstName, user.lastName);
                        } else {
                            //make:
                            //this.play_regular_notification_sound(user.firstName, user.lastName);
                            this.play_10s_notification_sound(user.firstName, user.lastName);
                        }
                        this.display_user_info(user);
                    }
                }, 1000);
            },



            btn_intro_click: function(){
                //let overlay = document.getElementById('overlay');
                //overlay.style.display = "none";
                this.show = false;
                axios
                    .get('/api/party/application_count/1/')
                    .then(response => {
                        this.inc_number_of_applicants_anim(response.data, 2); //should be 10
                        this.fetching_api = true;
                        this.last_number_of_applicants = response.data;
                    });
            },

            intro_background_animate: function(){
                window.setInterval(() => {
                    this.bgcIntro.backgroundColor = "#022346";
                    window.setTimeout(()=>{
                        this.bgcIntro.backgroundColor = "#025576";
                    }, 5000);
                }, 10000);
            },

            inc_number_of_applicants_anim: function (initValue, animLength) {
                TweenLite.fromTo(this.$data, animLength, {sliding_number_of_applicants: this.sliding_number_of_applicants},{ sliding_number_of_applicants: initValue });
                this.sliding_number_of_applicants = initValue;

            },

            play_10s_notification_sound: function(firstname, lastname) {
                let sound = new Audio(require('../../assets/johncenaintro.mp3'));
                let sound2 = new Audio('http://159.65.58.116/'+ firstname + ' ' +lastname);
                let sound3 = new Audio(require('../../assets/johncenaout.mp3'));

                sound.volume = 0.6;
                sound2.volume = 1.0;
                sound3.volume = 0.6;

                let self = this;

                sound2.addEventListener('loadeddata', function(){
                    if(sound2.readyState === 4){
                        sound.play();
                        self.colorParty();

                    }
                });

                sound.addEventListener('ended', function(){
                    sound2.play();
                });

                sound2.addEventListener('ended', function(){
                    sound3.play();
                    self.blastConfetti();
                });

            },

            display_user_info: function(user){
                this.newest_applicant = ('' + user.firstName + ' ' + user.lastName);
            },

            colorParty: function(){
                let colors = ["#ff6d4b","#ffc527","#a7ff42","#2cfff3","#f953ff"];
                for(let i=0; i<150; i++){
                    window.setTimeout(()=>{
                        this.bgc.backgroundColor = colors[i % colors.length];
                        this.tc.color = colors[(i+2) % colors.length];
                        this.tc2.color = colors[3*(i+2) % colors.length];
                        this.$refs.mainlogo.vectorlogo1.fill = colors[(i+2) % colors.length];
                        this.$refs.mainlogo.vectorlogo2.fill = colors[3*(i) % colors.length];


                        this.tc2.fontSize = (150+ 50*Math.sin(6.28318530718/16.25*i)).toString() + "px";
                    }, 100*i);
                }
                window.setTimeout(()=>{
                    this.bgc.backgroundColor = "#fafdff";
                    this.tc.color ="#6fcfec";
                    this.tc2.color ="#6fcfec";
                    this.tc2.fontSize = "150px";
                    this.$refs.mainlogo.vectorlogo1.fill = "#A9DDF1";
                    this.$refs.mainlogo.vectorlogo2.fill = "#6FCCEA";

                    this.show_newest_applicant=false;

                }, 100*150);
            },

            add_users: function(number, old_applicant_number) {
                axios
                    .get('/api/party/newest_applications/1/')
                    .then( response =>  {
                        //API allows for maximum 5 last entries:
                        let limit = number > response.data.length ? response.data.length : number;
                        for (let i = 0; i < limit; i++) {
                            this.new_users.push([response.data[i].user, old_applicant_number + i]);
                        }
                    });
            },

            overlay_leave: function (el, done) {
                Velocity(el, {opacity: 0}, { complete: done })
            },

            devReplayLast1: function(){
                this.add_users(1, 15);
            },
            devReplayLast2: function(){
                this.add_users(2, 15)
            },
            devReplayLast3: function(){
                this.add_users(3, 15)
            },
            devReplayLast4: function(){
                this.add_users(4, 15)
            },
            devReplayLast5: function(){
                this.add_users(5, 15)
            },
            blastConfetti: function() {
                for (let i = 0; i < 8; i++) {
                    window.setTimeout(() => {
                        const confettiDiv = document.querySelector(".center-point");
                        confetti(confettiDiv, {
                          angle: 45 + 90 * Math.random(),
                          startVelocity: 70,
                        });
                    }, 1500 * i);
                }
            }
        },

        mounted () {
            if(!this.fetching_api){
                this.intro_background_animate();
            }
        },

    }


</script>

<style lang="scss">

    #party_page_view{

        .Title {
            color: #6fcfec;
            margin-top: 20px;
        }

        #vektor-logo{
            width: 300px;
            filter:
                drop-shadow(0px  0.8px #2e2e2e)
                drop-shadow(0px  1.3px #2c2c2c)
                drop-shadow(0px  1.55px #2a2a2a)
                drop-shadow(0px  1.8px #282828)
                drop-shadow(0px  1.9px #262626)
                drop-shadow(0px  1.95px #242424)
                drop-shadow(0px  2.0px #222)
                drop-shadow(0px  2.05px 0.5px #202020)
                drop-shadow(0px  2.1px 1px #1e1e1e)
                drop-shadow(0px  2.1px 7px #1e1e1e);
        }



        #applicants_number{
            font-size: 150px;
            color: #6fcfec;
            position: fixed;
            width:100%;
            top:calc(50% - 170px);
        }

        .color-anim{
            transition: 0.1s;
            transition-timing-function: linear;

        }

        .animatedBackground{
            position: fixed;
            background-color: #fafdff;

            height:100vh;
            width: 100vw;
        }

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

        }



        #overlay{
            height: 100vh;
            width: 100vw;
            position: fixed;
            z-index: 10;
            background-color: #022346;
            transition: 10s;
            transition-timing-function: linear;
            transition-property: background-color;
        }



        #btn-intro{
            font-family: "Avant Garde", Avantgarde, "Century Gothic", CenturyGothic, "AppleGothic", sans-serif;
            position: fixed;
            font-weight: bold;
            z-index: 11;
            width: 20em;
            height: 7.5em;
            top: calc(50% - 3em);
            left: calc(50% - 10em);
            border-radius: 30px;
            background-color: #4caf50;
            color : #deebdf;
            outline:none;
            transition: 0.5s;
        }

        #btn-intro:active{
            transition: 0.1s;
            background-color: #4caf50;
            outline: none;
            margin-top: 12px;

            &.bdeepshadow {
                box-shadow:
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
            }
        }

        #newest_applicant {
            font-family: "Avant Garde", Avantgarde, "Century Gothic", CenturyGothic, "AppleGothic", sans-serif;
            text-align: center;
            position: fixed;
            z-index: 20;
            width: 20em;
            height: 10em;
            top: calc(15%);
            left: calc(50% - 10em);
            font-size: 32.5px;
            color: #fff;


        }

        #user_info_background {
            height: 100vh;
            width: 100vw;
            position: fixed;
            background-color: #6FCDEE;
            z-index: 8;
        }

        .slide-fade-enter-active {
            transition: all .3s ease;
        }
        .slide-fade-leave-active {
            transition: all .3s ease;
        }
        .slide-fade-enter{
            transform: translateX(100px);
            opacity: 0;
        }


        //not working:
        .slide-fade-leave-to
            /* .slide-fade-leave-active below version 2.1.8 */ {
            transform: translateX(100px);
            opacity: 0;
        }


        #CountDown{
            bottom:50px;
            position: fixed;
            width: 500px;
            left: calc(50% - 250px);
        }

        .center-point {
            position: fixed;
            left:50%;
            top:50%;
        }


        .elegantshadow {
            color: #fff;
            letter-spacing: .15em;
            text-shadow:
                    1px -1px 0 rgba(0,0,0,0.5),
                    -1px 2px 1px rgba(0,0,0,0.38),
                    -2px 4px 1px rgba(0,0,0,0.36),
                    -3px 6px 1px rgba(0,0,0,0.34),
                    -4px 8px 1px rgba(0,0,0,0.32),
                    -5px 10px 1px rgba(0,0,0,0.30),
                    -6px 12px 1px rgba(0,0,0,0.28),
                    -7px 14px 1px rgba(0,0,0,0.26),
                    -8px 16px 1px rgba(0,0,0,0.22),
                    -9px 18px 1px rgba(0,0,0,0.20),
                    -10px 20px 1px rgba(0,0,0,0.18),
                    -11px 22px 1px rgba(0,0,0,0.16),
                    -12px 24px 1px rgba(0,0,0,0.14),
                    -13px 26px 1px rgba(0,0,0,0.12),
                    -14px 28px 1px rgba(0,0,0,0.10),
                    -15px 30px 1px rgba(0,0,0,0.09),
                    -16px 32px 1px rgba(0,0,0,0.08),
                    -17px 34px 1px rgba(0,0,0,0.07),
                    -18px 36px 1px rgba(0,0,0,0.06),
                    -19px 38px 1px rgba(0,0,0,0.05),
                    -20px 40px 1px rgba(0,0,0,0.04),
                    -21px 42px 1px rgba(0,0,0,0.03),
                    -22px 44px 1px rgba(0,0,0,0.02),
                    -23px 46px 1px rgba(0,0,0,0.01),
                    -24px 48px 1px rgba(0,0,0,0.005),
                    -25px 50px 1px rgba(0,0,0,0.0),
                    -26px 52px 1px rgba(0,0,0,0),
                    -27px 54px 1px rgba(0,0,0,0),
                    -28px 56px 1px rgba(0,0,0,0),
        }
        .deepshadow {
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

        .bdeepshadow {
            letter-spacing: .1em;
            box-shadow:
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
        .insetshadow {
            color: #202020;
            // background-color: #2d2d2d;
            letter-spacing: .1em;
            text-shadow:
                    -1px -1px 1px rgba(0, 0, 0, 0.3),
                    2px 2px 1px rgba(0, 0, 0, 0.1);
        }


    }



</style>