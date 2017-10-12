import React, { Component } from 'react';
import { Card, Icon, Image } from 'semantic-ui-react';
import './Testimonial.css';

export default class Testimonial extends Component {
    render() {
        return (
            <Card className="testimonial-card">
                <div id="testimonialImageDiv">
                    <Image src={this.props.imagePath} shape="circular" centered size="small"/>
                </div>

                    <Card.Content>
                <Card.Description className="testimonialText">
                {this.props.testimonialText}
                </Card.Description>
                    <p className="testimonialText">
                <Icon name={this.props.gender}/> {this.props.name}
                    </p>
                    </Card.Content>



            </Card>
        );
    }
}
