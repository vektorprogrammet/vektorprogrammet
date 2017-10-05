import React, { Component } from 'react';
import './TestimonialRow.css';
import Testimonial from './Testimonial'

export default class TestimonialRow extends Component {
    render() {
        return (
            <div className="testimonialRow">
                <Testimonial />
                <Testimonial />
                <Testimonial />
            </div>
        )
    }
}
