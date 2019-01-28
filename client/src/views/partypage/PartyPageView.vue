<template>
    <div id="party_page_view">



        <transition
                v-on:leave="overlay_leave"
                v-bind:css="false">
            <div v-if="show" id="overlay" class="hue-anim">
                <h1 class="Title deepshadow">Vektor Party</h1>
            </div>
        </transition>

        <transition
                v-bind:css="false">
            <button v-if="show" id="btn-intro" v-on:click="btn_intro_click" class="deepshadow">My body is ready!</button>
        </transition>

        <div class="animatedBackground color-anim" v-bind:style="bgc">
            <div class = "Title color-anim">
                <h1 class="deepshadow" v-bind:style="tc">Vektor Party</h1>
            </div>

            <div id="applicants_div" >
                <h1 class="elegantshadow color-anim" id="applicants_number" v-bind:style="tc2"> {{ animatedNumber }}</h1>
            </div>

            <transition name="slide-fade">
                <h2 v-show="show_newest_applicant" v-if="show_newest_applicant" id="newest_applicant" class = "insetshadow">{{ newest_applicant }} er Vektors nyeste søker!</h2>
            </transition>

            <div id="CountDown">
                <CountDown></CountDown>
            </div>

            <div id="Dev-tools">
                <button id="btn-test1" v-on:click="devTestUser">Run test user</button>
                <button id="btn-last-1" v-on:click="devReplayLast1">Rerun 1</button>
                <button id="btn-last-2" v-on:click="devReplayLast2">Rerun 2</button>
                <button id="btn-last-3" v-on:click="devReplayLast3">Rerun 3</button>
                <button id="btn-last-4" v-on:click="devReplayLast4">Rerun 4</button>
                <button id="btn-last-5" v-on:click="devReplayLast5">Rerun 5</button>
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

    export default {
        name: "PartyPageView",
        components: {CountDown},
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
                test_users: [
                    {firstName: 'Sigurd', lastName: 'Totland'},
                    {firstName: 'John', lastName: 'Cena'},
                    {firstName: 'Navn', lastName: 'Navnesen'},
                    {firstName: 'Sara', lastName: 'Assistent'},
                    {firstName: 'Tore', lastName: 'Fjell'},
                    {firstName: 'Karoline', lastName: 'Solgård'},
                    {firstName: 'Magnus', lastName: 'Jespersen'},
                ],

                bgc: {
                    backgroundColor: '',
                },

                tc: {
                    color: '',
                },

                tc2: {
                    color: '',
                    fontSize: '',
                }
            }

        },

        computed: {
            animatedNumber: function() {
                return this.sliding_number_of_applicants.toFixed(0);
            },
        },

        methods:{
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

            inc_number_of_applicants_anim: function (initValue, animLength) {
                TweenLite.fromTo(this.$data, animLength, {sliding_number_of_applicants: this.sliding_number_of_applicants},{ sliding_number_of_applicants: initValue });
                this.sliding_number_of_applicants = initValue;

            },

            play_notification_sound: function(firstname, lastname) {
                let sound = new Audio(require('../../assets/johncenaintro.mp3'));
                let sound2 = new Audio('http://159.65.58.116/'+ firstname + ' ' +lastname);
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

                sound3.addEventListener('ended', function(){
                    this.show_newest_applicant = false;
                });

            },

            display_user_info: function(user){
                this.newest_applicant = ('' + user.firstName + ' ' + user.lastName);
                this.show_newest_applicant = true;
                let colors = ["#ff6d4b","#ffc527","#a7ff42","#2cfff3","#f953ff"];
                for(let i=0; i<150; i++){
                    window.setTimeout(()=>{
                        this.bgc.backgroundColor = colors[i % colors.length];
                        this.tc.color = colors[(i+2) % colors.length];
                        this.tc2.color = colors[3*(i+2) % colors.length];
                        this.tc2.fontSize = (150+ 50*Math.sin(6.28318530718/16.25*i)).toString() + "px";
                    }, 100*i);
                }
                window.setTimeout(()=>{
                    this.bgc.backgroundColor = "#fafdff";
                    this.tc.color ="#6fcfec";
                    this.tc2.color ="#6fcfec";
                    this.tc2.fontSize = "150px";
                    this.show_newest_applicant=false;

                }, 100*150);
            },

            show_users: function(number) {
                axios
                    .get('/api/party/newest_applications/1/')
                    .then( response =>  {
                        let limit = number > response.data.length ? response.data.length : number;
                        for (let i = 0; i < limit; i++) {
                            this.new_users.push(response.data[i].user);
                        }
                        for (let i = 0; i < this.new_users.length; i++) {
                            let user = this.new_users.pop();
                            this.play_notification_sound(user.firstName, user.lastName);
                            this.display_user_info(user);
                        }
                    });
            },

            overlay_leave: function (el, done) {
                Velocity(el, {opacity: 0}, { complete: done })
            },

            devTestUser:  function() {
                //let user = randomElement(this.test_users);
                let user = this.test_users[Math.floor(Math.random()*this.test_users.length)];
                this.play_notification_sound(user.firstName, user.lastName);
                this.display_user_info(user);

            },

            devReplayLast1: function(){
                this.show_users(1)
            },
            devReplayLast2: function(){
                this.show_users(2)
            },
            devReplayLast3: function(){
                this.show_users(3)
            },
            devReplayLast4: function(){
                this.show_users(4)
            },
            devReplayLast5: function(){
                this.show_users(5)
            },
            blastConfetti() {
                window.setTimeout(() => {
                    for (let i = 0; i < 8; i++) {
                        window.setTimeout(() => {
                            const confettiDiv = document.querySelector(".center-point");
                            confetti(confettiDiv, {angle: 67.5 + 45 * Math.random()});
                        }, 1500 * i);
                    }
                }, 3000);

            }



        },

        mounted () {
            window.setInterval(()=>{
                axios
                    .get('/api/party/application_count/1/')
                    .then(response => {
                        if(this.fetching_api && this.last_number_of_applicants !== response.data){
                            let new_applicants = response.data - this.last_number_of_applicants;
                            this.show_users(new_applicants);
                            this.inc_number_of_applicants_anim(response.data, 3);
                            this.last_number_of_applicants = response.data;
                            this.blastConfetti();
                        }
                    });
            }, 3000);
        },

    }





</script>

<style lang="scss">

    #party_page_view{

        .Title {
            color: #6fcfec;
        }


        #applicants_number{
            font-size: 150px;
            color: #6fcfec;
            position: center;
            margin-top: -80px;
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

            &.elegantshadow {
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
            &.deepshadow {
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
            z-index: 10;
            background-color: #022346;
        }


        #btn-intro{
            font-family: "Avant Garde", Avantgarde, "Century Gothic", CenturyGothic, "AppleGothic", sans-serif;
            position: fixed;
            z-index: 11;
            width: 20em;
            height: 7.5em;
            top: calc(50% - 3em);
            left: calc(50% - 10em);
            border-radius: 30px;
            background-color: #4caf50;
        }

        #newest_applicant {
            font-family: "Avant Garde", Avantgarde, "Century Gothic", CenturyGothic, "AppleGothic", sans-serif;
            text-align: center;
            position: fixed;
            z-index: 20;
            width: 20em;
            height: 10em;
            top: calc(30%);
            left: calc(50% - 10em);
            color : #575757;
            font-size: 32.5px;

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





    }



</style>