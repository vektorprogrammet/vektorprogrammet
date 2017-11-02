import React, {Component} from 'react';
import './NewsletterPopUp.css';
import { Button, Form, Icon } from 'semantic-ui-react';
//import Geolocation from '../components/Geolocation';
import {NewsletterApi} from '../api/NewsletterApi';

class NewsletterPopUp extends Component {
    constructor(props){
        super(props);
        this.state = {
            departmentShortName:'',
            submitted: false,
            postData : {
                name : '',
                email : '',
                newsletterId : ''
            }
        };
    }

    handleSubmit = () =>{
        this.setState({submitted:true});
        NewsletterApi.post(this.state.postData);
    };

    handleChange = (event) => {
        const postData = {...this.state.postData};
        postData[event.target.name] = event.target.value;
        this.setState({
            postData
        });
    };

    /*
    closestDepartment = async (departmentId) => {
        const newsletter = await NewsletterApi.getByDepartment(departmentId);
        const postData = {...this.state.postData};
        postData.newsletterId = newsletter.id;
        this.setState({
            departmentShortName : newsletter.department.short_name,
            postData
        });
    };
    */

    async componentDidMount(){
        const newsletter = await NewsletterApi.getByDepartment(1); // NTNU
        const postData = {...this.state.postData};
        postData.newsletterId = newsletter.id;
        this.setState({
            departmentShortName : newsletter.department.short_name,
            postData
        });
    }


    render() {
        const department = this.state.departmentShortName;
        const isSubmitted = this.state.submitted;

        return (
            <div className={'newsletterPopUp' + (this.props.show ? ' visible' : '')}>
                {/* <Geolocation closestDepartment={ this.closestDepartment } /> */}

                <Icon name={'remove'} onClick={this.props.onClose} className="newsletterIcon"/>
                <br/>
                <p className="newsletterText">Du virker interessert i Vektorprogrammet. <br/> Vil du melde deg på <b>{department}</b> sitt nyhetsbrev?</p>
                <Form onSubmit={this.handleSubmit}>
                    <Form.Field><input name='name' placeholder='Navn' value={this.state.postData.name} onChange={this.handleChange} required/></Form.Field>
                    <Form.Field><input name='email' placeholder='E-post' value={this.state.postData.email} onChange={this.handleChange} type="email" required/></Form.Field>
                    <Button size='mini'>Meld meg på!</Button>
                </Form>
                {isSubmitted ? <p className="newsletterSubmittedTxt">Takk!</p> : '' }
            </div>
        )
    }
}

export default NewsletterPopUp;
