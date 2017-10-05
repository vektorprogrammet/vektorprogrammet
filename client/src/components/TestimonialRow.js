import React, { Component } from 'react';
import './TestimonialRow.css';
import Testimonial from './Testimonial';

export default class TestimonialRow extends Component {
    render() {
        return (
            <div className="testimonialRow">
                <Testimonial
                    testimonialText="Jeg likte vektor fordi vi gjorde
                                     så mye gøy. En gang så dro vi på
                                     bowling, det likte jeg best."
                    gender="female"
                    name="Alice"
                    imagePath="https://via.placeholder.com/200x200"
                    />
                <Testimonial
                    testimonialText="Jeg er flink i matte, derfor er jeg vektor"
                    gender="male"
                    name="Bob"
                    imagePath="https://via.placeholder.com/200x200"
                    />
                <Testimonial
                    testimonialText="Jeg liker å lære bort ting."
                    gender="male"
                    name="Charlie"
                    imagePath="https://via.placeholder.com/200x200"
                    />
            </div>
        );
    }
}
