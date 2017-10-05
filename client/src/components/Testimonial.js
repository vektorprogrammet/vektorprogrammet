import React, { Component } from 'react';
import { Card, Icon, Image } from 'semantic-ui-react'
import './Testimonial.css';

export default class Testimonial extends Component {
    render() {
        return (
            <Card className="testimonial-card">
                <div>
                    <Image src='https://via.placeholder.com/200x200' shape="circular" centered size="small"/>
                </div>

                    <Card.Content>
                <Card.Description className="testimonialText">
                    Jeg liker det sosiale og det ikke sosiale og det alternative!
                </Card.Description>
                    <p className="testimonialText">
                        <Icon name="female"/> Alice
                    </p>
                    </Card.Content>



            </Card>
        )
    }
}
