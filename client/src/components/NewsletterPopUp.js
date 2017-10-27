import React, {Component} from 'react';
import './NewsletterPopUp.css';
import { Button, Form, Icon } from 'semantic-ui-react';
import Geolocation from '../components/Geolocation';
import {NewsletterApi} from '../api/NewsletterApi';
import {DepartmentApi} from '../api/DepartmentApi';

class NewsletterPopUp extends Component {
    constructor(props){
        super(props);
        this.state = {
            department:'',
            newsletter:'',
            postData : {
                email : '',
                newsletterId : ''
            }
        };
        this.handleChange = this.handleChange.bind(this);
    }

    handleSubmit() {
        const postData = {...this.state.postData};
        postData.newsletterId = this.state.newsletter.id;
        this.setState({
            postData
        });
        NewsletterApi.post(this.state.postData);
    };

    handleChange(event) {
        const postData = {...this.state.postData};
        postData[event.target.name] = event.target.value;
        this.setState({
            postData
        });
    };

    async getClosestDepartment(departmentId) {
        const newsletter = await NewsletterApi.getByDepartment(departmentId);
        const department = await DepartmentApi.get(departmentId);
        this.setState({
            newsletter,
            department
        });
    };

    render() {
        const department = this.state.department.short_name;
        return (
            <div className={'newsletterPopUp' + (this.props.show ? ' visible' : '')}>
                <Geolocation closestDepartment={ this.getClosestDepartment } />

                <Icon name={'remove'} onClick={this.props.onClose} className="newsletterIcon"/>
                <br/>
                <p className="newsletterText">Du virker interessert i Vektorprogrammet. <br/> Vil du melde deg på {department} sitt nyhetsbrev?</p>
                <Form onSubmit={this.handleSubmit}>
                    <Form.Field><input name='email' placeholder='E-post' value={this.state.postData.email} onChange={this.handleChange} required/></Form.Field>
                    <Button size='mini'>Meld meg på!</Button>
                </Form>
            </div>
        )
    }
}

export default NewsletterPopUp;
