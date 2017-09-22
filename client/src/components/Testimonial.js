import React, { Component } from 'react';
import { Card, Icon, Image } from 'semantic-ui-react'
import './Testimonial.css';

export default class Testimonial extends Component {
    render() {
        return (
            <div className="testimonial">
                <Image className="testimonial" size="small" src='https://via.placeholder.com/200x200' shape='circular'/>
                <p>Vektorprogrammet er kult! <br/> Jeg likte det.</p>
                <p>~ Assistentmannen</p>
            </div>
        )
    }
}

const CardExampleCard = () => (
    <Card>
        <Image src='https://via.placeholder.com/200x200' shape='circular' />
        <Card.Content>
            <Card.Header>
                Matthew
            </Card.Header>
            <Card.Meta>
    <span className='date'>
      Joined in 2015
    </span>
            </Card.Meta>
            <Card.Description>
                Matthew is a musician living in Nashville.
            </Card.Description>
        </Card.Content>
        <Card.Content extra>
            <a>
                <Icon name='user' />
                22 Friends
            </a>
        </Card.Content>
    </Card>
);