import React, {Component} from 'react';
import './NewsletterPopUp.css';
import { Button, Form, Icon } from 'semantic-ui-react';
import Geolocation from '../components/Geolocation';
import {NewsletterApi} from '../api/NewsletterApi';

class NewsletterPopUp extends Component {
    constructor(props){
        super(props);
        this.state = {
            email:'',
            newsletterId:''
        };
    }

    handleSubmit = () => {
  //      NewsletterApi.post(this.state.email); // TODO: finne ut av dette.
    };

    handleChange = newsletter => {
        this.setState({[newsletter.target.id]: newsletter.target.value});
    };

    getClosestDepartment = async shortName => {
        const newsletterId = await NewsletterApi.getByDepartmentShortName(shortName);
        this.setState({
            newsletterId:  newsletterId,
        });
    };

    render() {
        return (
            <div className={'newsletterPopUp' + (this.props.show ? ' visible' : '')}>
                <Icon name={'remove'} onClick={this.props.onClose} className="newsletterIcon"/>
                <br/>
                <p className="newsletterText">Du virker interessert i Vektorprogrammet. <br/> Vil du melde deg på vårt nyhetsbrev?</p>
                <Form onSubmit={this.handleSubmit}>
                    <Form.Field><input id='email' placeholder='E-post' value={this.state.email} onChange={this.handleChange} required/></Form.Field>
                    <Button size='mini'>Meld meg på!</Button>
                </Form>
                <Geolocation closestDepartment={ this.getClosestDepartment } />
            </div>
        )
    }
}

export default NewsletterPopUp;
