import React, {Component} from 'react';
import './NewsletterPopUp.css';
import { Button, Form, Icon } from 'semantic-ui-react';
import Geolocation from '../components/Geolocation';
import {NewsletterApi} from '../api/NewsletterApi';

class NewsletterPopUp extends Component {
    constructor(props){
        super(props);
        this.state = {
            newsletter:'',
            departmentShortName:'',
            postData : {
                name : '',
                email : '',
                newsletterId : ''
            }
        };
        this.closestDepartment = this.closestDepartment.bind(this);
    }

    handleSubmit = () =>{
        const postData = {...this.state.postData};
        postData.newsletterId = this.state.newsletter.id;
        this.setState({
            postData
        });
        NewsletterApi.post(this.state.postData);
    };

    handleChange = (event) => {
        const postData = {...this.state.postData};
        postData[event.target.name] = event.target.value;
        this.setState({
            postData
        });
    };

    async closestDepartment(departmentId){
        const newsletter = await NewsletterApi.getByDepartment(departmentId);
        console.log(newsletter);
        this.setState({
            newsletter,
            departmentShortName : newsletter.department.short_name
        });
    };


    render() {
        const department = this.state.departmentShortName;
        return (
            <div className={'newsletterPopUp' + (this.props.show ? ' visible' : '')}>
                <Geolocation closestDepartment={ this.closestDepartment } />

                <Icon name={'remove'} onClick={this.props.onClose} className="newsletterIcon"/>
                <br/>
                <p className="newsletterText">Du virker interessert i Vektorprogrammet. <br/> Vil du melde deg på <b>{department}</b> sitt nyhetsbrev?</p>
                <Form onSubmit={this.handleSubmit}>
                    <Form.Field><input name='name' placeholder='Navn' value={this.state.postData.name} onChange={this.handleChange} required/></Form.Field>
                    <Form.Field><input name='email' placeholder='E-post' value={this.state.postData.email} onChange={this.handleChange} required/></Form.Field>
                    <Button size='mini'>Meld meg på!</Button>
                </Form>
            </div>
        )
    }
}

export default NewsletterPopUp;
