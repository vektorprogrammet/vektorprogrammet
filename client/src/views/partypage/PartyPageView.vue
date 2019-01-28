<template>
    <div id="party_page_view">
        <transition
                v-on:leave="overlay_leave"
                v-bind:css="false">
            <div v-if="show" id="overlay" v-bind:style="bgcIntro">
                <h1 class="Title deepshadow">
                    <svg id="vektor-logo" baseProfile="tiny"
                                                  xmlns="http://www.w3.org/2000/svg"
                                                  viewBox="0 0 935 547">
                        <path id="vektor-logo-fill1" v-bind:style="vectorlogo1" fill="#A9DDF1" d="M625.7 438c-6.3 5.7-11.9 11.1-17.8 16.1-17.2 14.5-35.1 28.1-55.6 37.7-8.4 3.9-17 6.8-26.5 4.1-13.2-3.7-21.2-16.7-18.2-30 4.4-19.4 13.5-36.8 22.7-54.1 2.5-4.7 5.2-9.3 7.8-13.9.8-1.5 2.7-2.9-.1-4.9-3-2.1-3.8.2-5 1.8-20 25.9-42.1 49.8-65.3 72.9-11.7 11.6-24.6 21.4-40.4 26.8-5.1 1.8-10.3 2.7-15.6 2.9-12.2.5-19.7-8-18.1-20.1 1.4-11.1 5.5-21.6 7.1-32.6.5-3.3.9-6.7.2-9.9-1-5.1-4.9-6.7-9.4-3.9-3.6 2.3-6.1 5.6-8.7 8.8-13.1 15.7-26 31.7-39.7 46.9-7.4 8.3-16 12-27 10.5-8.5-1.2-11.5-5.3-8.8-13.4 5.5-16.1 13.4-31.1 21.3-46.1 9.6-18.1 19.5-35.9 29.2-54 .9-1.7 4.3-3.6.6-6.2-3.8-2.7-4.2 1.1-5.4 2.7-16.3 21.5-32.6 43-50.9 62.9-18.8 20.4-39.8 38.4-65.7 49.7-13.5 5.9-27.4 9.3-42.3 6.4-19.1-3.7-34.9-25.9-32-45.1.7-4.9 1-10 4.5-14 1.4-1.6.4-2.6-.6-3.8-1.3-1.5-2.4-1.4-3.7-.1-.9.9-2.1 1.6-3.2 2.4-2.5 2-4.8 2.1-7-.5s-3.6-5.5-.7-8.4c3.2-3.2 6.9-5.9 9.8-9.2 10.1-11.3 19.7-23 30-34.1 16.6-17.7 34.8-33.5 57.3-43.5 10.5-4.7 21.4-7.3 33-4.6 17.8 4.1 27.3 22 20.6 39-3.2 8.2-8.2 15.2-13.9 21.8-19.4 22.3-42.7 39-71 48.2-2.8.9-3.9 2.2-4 5.2-.5 16.2 8.6 23.8 24.4 20.4 12.4-2.7 22.4-9.6 32.1-17.4 20.9-16.8 38.1-37.1 55.9-56.9 29.9-33.3 56.1-69.3 75.7-109.7 5.5-11.4 9.7-23.4 16.2-34.2 2.1-3.4 4.2-6.8 7.1-9.6 2.7-2.8 5.9-3.4 9-1 10.8 8.3 17.9 19.1 20.5 32.6.5 2.6-.4 5.2-1.6 7.7-18.9 39.4-37.7 78.9-56.6 118.3-.9 1.9-1.8 3.7-2.9 6 3.6-.2 5-3 6.8-4.9 18.8-19.1 37.9-37.9 59.6-53.8 5.9-4.3 12.1-8.2 19-10.9 7.8-3 15.1-1.4 21.9 2.9 6.1 3.9 6.6 10.1 5.2 16.4-1.8 7.9-5.3 15.1-10.6 21.3-7.5 8.8-16.4 11.1-27.2 4.1-10.5-6.8-18.2-4.1-26.4 2.9-5.9 5.1-11.9 10.1-17.4 16 5.1-.8 10.2-1.5 15.4-2.3 17.4-2.7 19.8-.1 16.6 16.9-1.2 6.3-2.8 12.7-1.7 19.3 1.7 10.1 9.3 13.7 17.7 8 10.3-6.9 19.2-15.5 27.4-24.7 19-21.6 37.7-43.5 56.5-65.4.3-.4.5-.8 1.3-2-2.9 0-5.3.1-7.7 0-2.9-.2-5.9-1-6.4-4.4-.6-3.6-.1-7.1 3.6-9.1 4.3-2.4 9.3-3.7 14-3 9.9 1.4 14.9-4.4 19.3-11.7 8-13.4 16.1-26.8 24.1-40.2 5.9-9.9 9-10.3 16.6-1.5 4.3 5 9.3 9.6 11.6 16.1 3.5 9.6.1 18.3-3.9 26.9-1.6 3.3-3.3 6.5-5.3 10.3 6.7.3 12.9.6 19.2 1 4.3.3 10.5-.4 11.9 3.1 1.5 3.8-3.7 7.6-6.1 11.2-1.8 2.6-5 2.3-7.8 2.3-7.7.1-15.3.1-23 0-2.3 0-3.5.8-4.7 2.7-15.8 25.7-29.8 52.3-42.9 79.4-2.1 4.2-3.5 8.7-3.3 13.4.3 6.4 4.5 9.6 10.7 8.4 4.8-1 9.1-2.9 12.9-6.1 5.7-4.9 11.5-9.6 17.5-14.2 11.9-9.2 19.7-22.1 29.6-33 18.3-20 36.1-40.6 58.6-56.2 11-7.6 22.7-13.7 36.4-15.2 21.5-2.4 37.3 11.4 37.5 33.3.2 17.2-5.5 33-12.3 48.5-1 2.3-2.3 4.5-3.2 6.8-1.4 3.6-5.2 7 .4 10.8 2.4 1.6 1 5.7 1.6 8.7 7.6-1.2 13.7-4.5 18.8-10.2 17.9-20 35.1-40.6 49-63.6 1.7-2.7 2.3-4.4-.7-6.9-5.3-4.3-7-10.5-5.9-17.1 1.4-8.3 4.7-16 10.4-22.4 5.9-6.5 13.4-9.2 22.1-7.7 6.2 1 9.3 4.7 9.5 11 .2 5.5-1.2 10.6-3.7 15.4-1.5 2.9-.6 4.1 2 5.2 6.8 3 13.7 2.7 20.6 1.1 8.1-1.9 16-4.6 24-6.7 14.1-3.6 17.5 3.5 15.3 15.7-.7 3.7-2.6 7.2-4.3 10.6-11.5 22.5-21.5 45.7-31.9 68.7-1.7 3.8-2.9 7.8-2.9 12.1 0 6.3 3.5 8.8 9.5 6.8 2.2-.8 4.2-2 5.9-3.7 8.3-8.2 16.6-16.4 24.8-24.6 1.3-1.3 2.7-2.4 4.5-3 2.7-.9 4.8-.2 6.6 2 2 2.3.5 4-.7 5.7-7.9 10.8-17 20.5-26.6 29.8-14.3 13.9-30 25.9-47.9 34.7-7.6 3.7-15.5 6-24.1 4.4-14.2-2.8-23.3-16.3-20.2-30.5 2.3-10.5 7.5-19.9 13-29 7.5-12.4 16.5-23.8 25.4-35.2 4.8-6.2 9.2-12.7 11-20.5 1.7-7.5-1.4-12.8-8.8-14.8-8.4-2.2-9.6-1.9-15 5.1-15.7 20.3-31.8 40.2-48.6 59.6-10.6 12.1-22.3 22.4-38.8 25.6-4.2.8-8.6 1.3-12.2-.8-3.9-2.2-5.3-.4-7.4 2.3-9.4 12-20.2 22.6-33.7 30-10.3 5.6-21.2 8.7-33 8.7-21.7.1-36.7-14.3-37.9-36.1-.4-8.5.2-16.1 1.6-24.4zm123.7-66.8c.3-6.4-.2-11.3-5-14.1-4.7-2.7-9.6-2.3-14.3 0-1.8.9-3.5 2-5 3.3-3 2.9-6.1 5.7-8.7 9-15.6 20.3-27.6 42.7-37 66.4-2.1 5.4-3.5 11.1-3.1 17.1.8 11.5 11.8 17.9 22 12.7 8-4 11.6-11.6 15.5-19 2.2-4.3-1.5-7.1-2.6-10.6-5.2-16.9 2.1-26.3 19.7-24.9.5 0 1 .1 1.5.2 1.3.3 2.1-.1 2.7-1.4 6.4-12.8 13-25.5 14.3-38.7zm-470.1-1c-.1-3.8.2-7.3-3.3-9.3-3.8-2.1-7.1-.5-10.2 1.6-1.8 1.2-3.4 2.6-5 4.2-8.8 8.9-16.4 18.7-23.4 29.1-2.3 3.4-4.2 7.2-4.6 11.4-.6 6.8 4.3 10.5 10.8 8.3 2.4-.8 4.5-2.2 6.4-3.8 7.3-5.8 14-12.3 20-19.4 5.6-6.5 9.1-13.9 9.3-22.1z"></path>

                        <path id="vektor-logo-fill2"v-bind:style="vectorlogo2"  fill="#6FCCEA" d="M20.5 92.2c17.2-23.4 31-48.4 43.1-75.4C74.1 44.4 86 70.6 103.3 95.4c-5.5-2.1-10.1-3.9-16-6.2 0 7.7.2 14.8 0 21.8-1.3 40.4-2.6 80.8-4 121.2-1 28.6-1.9 57.2-3 85.8-1.6 41.4-3.5 82.8-6.6 124.1-.9 11.4-2 22.9-3.1 34.3-.2 2-.7 3.8 2 4.8 2.6 1 3.5-.4 4.7-2.2 36-55.1 77.4-106.3 118.3-157.8 38.9-48.9 77.6-97.9 116.3-146.8 3.7-4.6 3.7-4.6-1.9-6.5-2.5-.8-5-1.7-7.5-2.6-.5-.2-.9-.4-2.3-1.2 28.6-7.7 54.8-19.3 80.8-33.6-8.4 28.4-14.4 56.2-15.6 85.8-3.6-3.9-5.5-7.9-7.6-11.6-1.8-3.1-2.8-4.2-5.7-.7-22.2 27.5-44.5 54.8-66.8 82.2-13.7 16.8-27.3 33.6-41 50.4-1.3 1.6-2.5 3.1-4.4 4-26 14-46.7 34-65.5 56.3-8 9.5-15.8 19.2-25.5 27.4-8.1 6.8-5 17.1 6.5 21.2-5.2 6.4-10.2 12.7-15.3 19-19 23.4-38.1 46.7-57 70.2-2.1 2.6-3.8 3.7-7.4 2.3-15.1-5.6-30.3-10.9-45.6-16.1-3.1-1.1-4-2.6-3.9-5.8.9-32.4 1.6-64.9 2.4-97.3.7-29 1.3-57.9 2-86.9.7-29 1.3-57.9 2-86.9l3-130.8c.2-7.5.2-15 .6-22.5.1-3-.4-3.8-3.4-2.7-4 1.7-8.2 2.8-12.3 4.2z"></path>
                    </svg> Party</h1>
            </div>
        </transition>

        <transition
                v-bind:css="false">
            <button v-if="show" id="btn-intro" v-on:click="btn_intro_click" class="bdeepshadow insetshadow">My body is ready!</button>
        </transition>

        <div class="animatedBackground color-anim" v-bind:style="bgc">
            <div class = "Title color-anim">
                <h1 class="deepshadow" v-bind:style="tc">
                    <svg id="vektor-logo" baseProfile="tiny"
                         xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 935 547">
                        <path id="vektor-logo-fill1" v-bind:style="vectorlogo1" fill="#A9DDF1" d="M625.7 438c-6.3 5.7-11.9 11.1-17.8 16.1-17.2 14.5-35.1 28.1-55.6 37.7-8.4 3.9-17 6.8-26.5 4.1-13.2-3.7-21.2-16.7-18.2-30 4.4-19.4 13.5-36.8 22.7-54.1 2.5-4.7 5.2-9.3 7.8-13.9.8-1.5 2.7-2.9-.1-4.9-3-2.1-3.8.2-5 1.8-20 25.9-42.1 49.8-65.3 72.9-11.7 11.6-24.6 21.4-40.4 26.8-5.1 1.8-10.3 2.7-15.6 2.9-12.2.5-19.7-8-18.1-20.1 1.4-11.1 5.5-21.6 7.1-32.6.5-3.3.9-6.7.2-9.9-1-5.1-4.9-6.7-9.4-3.9-3.6 2.3-6.1 5.6-8.7 8.8-13.1 15.7-26 31.7-39.7 46.9-7.4 8.3-16 12-27 10.5-8.5-1.2-11.5-5.3-8.8-13.4 5.5-16.1 13.4-31.1 21.3-46.1 9.6-18.1 19.5-35.9 29.2-54 .9-1.7 4.3-3.6.6-6.2-3.8-2.7-4.2 1.1-5.4 2.7-16.3 21.5-32.6 43-50.9 62.9-18.8 20.4-39.8 38.4-65.7 49.7-13.5 5.9-27.4 9.3-42.3 6.4-19.1-3.7-34.9-25.9-32-45.1.7-4.9 1-10 4.5-14 1.4-1.6.4-2.6-.6-3.8-1.3-1.5-2.4-1.4-3.7-.1-.9.9-2.1 1.6-3.2 2.4-2.5 2-4.8 2.1-7-.5s-3.6-5.5-.7-8.4c3.2-3.2 6.9-5.9 9.8-9.2 10.1-11.3 19.7-23 30-34.1 16.6-17.7 34.8-33.5 57.3-43.5 10.5-4.7 21.4-7.3 33-4.6 17.8 4.1 27.3 22 20.6 39-3.2 8.2-8.2 15.2-13.9 21.8-19.4 22.3-42.7 39-71 48.2-2.8.9-3.9 2.2-4 5.2-.5 16.2 8.6 23.8 24.4 20.4 12.4-2.7 22.4-9.6 32.1-17.4 20.9-16.8 38.1-37.1 55.9-56.9 29.9-33.3 56.1-69.3 75.7-109.7 5.5-11.4 9.7-23.4 16.2-34.2 2.1-3.4 4.2-6.8 7.1-9.6 2.7-2.8 5.9-3.4 9-1 10.8 8.3 17.9 19.1 20.5 32.6.5 2.6-.4 5.2-1.6 7.7-18.9 39.4-37.7 78.9-56.6 118.3-.9 1.9-1.8 3.7-2.9 6 3.6-.2 5-3 6.8-4.9 18.8-19.1 37.9-37.9 59.6-53.8 5.9-4.3 12.1-8.2 19-10.9 7.8-3 15.1-1.4 21.9 2.9 6.1 3.9 6.6 10.1 5.2 16.4-1.8 7.9-5.3 15.1-10.6 21.3-7.5 8.8-16.4 11.1-27.2 4.1-10.5-6.8-18.2-4.1-26.4 2.9-5.9 5.1-11.9 10.1-17.4 16 5.1-.8 10.2-1.5 15.4-2.3 17.4-2.7 19.8-.1 16.6 16.9-1.2 6.3-2.8 12.7-1.7 19.3 1.7 10.1 9.3 13.7 17.7 8 10.3-6.9 19.2-15.5 27.4-24.7 19-21.6 37.7-43.5 56.5-65.4.3-.4.5-.8 1.3-2-2.9 0-5.3.1-7.7 0-2.9-.2-5.9-1-6.4-4.4-.6-3.6-.1-7.1 3.6-9.1 4.3-2.4 9.3-3.7 14-3 9.9 1.4 14.9-4.4 19.3-11.7 8-13.4 16.1-26.8 24.1-40.2 5.9-9.9 9-10.3 16.6-1.5 4.3 5 9.3 9.6 11.6 16.1 3.5 9.6.1 18.3-3.9 26.9-1.6 3.3-3.3 6.5-5.3 10.3 6.7.3 12.9.6 19.2 1 4.3.3 10.5-.4 11.9 3.1 1.5 3.8-3.7 7.6-6.1 11.2-1.8 2.6-5 2.3-7.8 2.3-7.7.1-15.3.1-23 0-2.3 0-3.5.8-4.7 2.7-15.8 25.7-29.8 52.3-42.9 79.4-2.1 4.2-3.5 8.7-3.3 13.4.3 6.4 4.5 9.6 10.7 8.4 4.8-1 9.1-2.9 12.9-6.1 5.7-4.9 11.5-9.6 17.5-14.2 11.9-9.2 19.7-22.1 29.6-33 18.3-20 36.1-40.6 58.6-56.2 11-7.6 22.7-13.7 36.4-15.2 21.5-2.4 37.3 11.4 37.5 33.3.2 17.2-5.5 33-12.3 48.5-1 2.3-2.3 4.5-3.2 6.8-1.4 3.6-5.2 7 .4 10.8 2.4 1.6 1 5.7 1.6 8.7 7.6-1.2 13.7-4.5 18.8-10.2 17.9-20 35.1-40.6 49-63.6 1.7-2.7 2.3-4.4-.7-6.9-5.3-4.3-7-10.5-5.9-17.1 1.4-8.3 4.7-16 10.4-22.4 5.9-6.5 13.4-9.2 22.1-7.7 6.2 1 9.3 4.7 9.5 11 .2 5.5-1.2 10.6-3.7 15.4-1.5 2.9-.6 4.1 2 5.2 6.8 3 13.7 2.7 20.6 1.1 8.1-1.9 16-4.6 24-6.7 14.1-3.6 17.5 3.5 15.3 15.7-.7 3.7-2.6 7.2-4.3 10.6-11.5 22.5-21.5 45.7-31.9 68.7-1.7 3.8-2.9 7.8-2.9 12.1 0 6.3 3.5 8.8 9.5 6.8 2.2-.8 4.2-2 5.9-3.7 8.3-8.2 16.6-16.4 24.8-24.6 1.3-1.3 2.7-2.4 4.5-3 2.7-.9 4.8-.2 6.6 2 2 2.3.5 4-.7 5.7-7.9 10.8-17 20.5-26.6 29.8-14.3 13.9-30 25.9-47.9 34.7-7.6 3.7-15.5 6-24.1 4.4-14.2-2.8-23.3-16.3-20.2-30.5 2.3-10.5 7.5-19.9 13-29 7.5-12.4 16.5-23.8 25.4-35.2 4.8-6.2 9.2-12.7 11-20.5 1.7-7.5-1.4-12.8-8.8-14.8-8.4-2.2-9.6-1.9-15 5.1-15.7 20.3-31.8 40.2-48.6 59.6-10.6 12.1-22.3 22.4-38.8 25.6-4.2.8-8.6 1.3-12.2-.8-3.9-2.2-5.3-.4-7.4 2.3-9.4 12-20.2 22.6-33.7 30-10.3 5.6-21.2 8.7-33 8.7-21.7.1-36.7-14.3-37.9-36.1-.4-8.5.2-16.1 1.6-24.4zm123.7-66.8c.3-6.4-.2-11.3-5-14.1-4.7-2.7-9.6-2.3-14.3 0-1.8.9-3.5 2-5 3.3-3 2.9-6.1 5.7-8.7 9-15.6 20.3-27.6 42.7-37 66.4-2.1 5.4-3.5 11.1-3.1 17.1.8 11.5 11.8 17.9 22 12.7 8-4 11.6-11.6 15.5-19 2.2-4.3-1.5-7.1-2.6-10.6-5.2-16.9 2.1-26.3 19.7-24.9.5 0 1 .1 1.5.2 1.3.3 2.1-.1 2.7-1.4 6.4-12.8 13-25.5 14.3-38.7zm-470.1-1c-.1-3.8.2-7.3-3.3-9.3-3.8-2.1-7.1-.5-10.2 1.6-1.8 1.2-3.4 2.6-5 4.2-8.8 8.9-16.4 18.7-23.4 29.1-2.3 3.4-4.2 7.2-4.6 11.4-.6 6.8 4.3 10.5 10.8 8.3 2.4-.8 4.5-2.2 6.4-3.8 7.3-5.8 14-12.3 20-19.4 5.6-6.5 9.1-13.9 9.3-22.1z"></path>
                        <path id="vektor-logo-fill2"v-bind:style="vectorlogo2"  fill="#6FCCEA" d="M20.5 92.2c17.2-23.4 31-48.4 43.1-75.4C74.1 44.4 86 70.6 103.3 95.4c-5.5-2.1-10.1-3.9-16-6.2 0 7.7.2 14.8 0 21.8-1.3 40.4-2.6 80.8-4 121.2-1 28.6-1.9 57.2-3 85.8-1.6 41.4-3.5 82.8-6.6 124.1-.9 11.4-2 22.9-3.1 34.3-.2 2-.7 3.8 2 4.8 2.6 1 3.5-.4 4.7-2.2 36-55.1 77.4-106.3 118.3-157.8 38.9-48.9 77.6-97.9 116.3-146.8 3.7-4.6 3.7-4.6-1.9-6.5-2.5-.8-5-1.7-7.5-2.6-.5-.2-.9-.4-2.3-1.2 28.6-7.7 54.8-19.3 80.8-33.6-8.4 28.4-14.4 56.2-15.6 85.8-3.6-3.9-5.5-7.9-7.6-11.6-1.8-3.1-2.8-4.2-5.7-.7-22.2 27.5-44.5 54.8-66.8 82.2-13.7 16.8-27.3 33.6-41 50.4-1.3 1.6-2.5 3.1-4.4 4-26 14-46.7 34-65.5 56.3-8 9.5-15.8 19.2-25.5 27.4-8.1 6.8-5 17.1 6.5 21.2-5.2 6.4-10.2 12.7-15.3 19-19 23.4-38.1 46.7-57 70.2-2.1 2.6-3.8 3.7-7.4 2.3-15.1-5.6-30.3-10.9-45.6-16.1-3.1-1.1-4-2.6-3.9-5.8.9-32.4 1.6-64.9 2.4-97.3.7-29 1.3-57.9 2-86.9.7-29 1.3-57.9 2-86.9l3-130.8c.2-7.5.2-15 .6-22.5.1-3-.4-3.8-3.4-2.7-4 1.7-8.2 2.8-12.3 4.2z"></path>
                    </svg>
                    Party</h1>
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
                        this.vectorlogo1.fill = colors[(i+2) % colors.length];
                        this.vectorlogo2.fill = colors[3*(i) % colors.length];


                        this.tc2.fontSize = (150+ 50*Math.sin(6.28318530718/16.25*i)).toString() + "px";
                    }, 100*i);
                }
                window.setTimeout(()=>{
                    this.bgc.backgroundColor = "#fafdff";
                    this.tc.color ="#6fcfec";
                    this.tc2.color ="#6fcfec";
                    this.tc2.fontSize = "150px";
                    this.vectorlogo1.fill = "#A9DDF1";
                    this.vectorlogo2.fill = "#6FCCEA";

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
            margin-top: -90px;
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