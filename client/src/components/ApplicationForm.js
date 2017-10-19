import React, {Component} from 'react';
import {Button, Form} from 'semantic-ui-react';
import {DepartmentApi} from '../api/DepartmentApi';
import {ApplicationApi} from '../api/ApplicationApi';
import Geolocation from '../components/Geolocation'
import ConfirmationBox from '../components/ConfirmationBox';

class ApplicationForm extends Component {

    constructor(props){
        super(props);
        this.state = {
            department: '',
            confirmationBoxVisible: false,
            fieldOfStudies : [],
            'application[user][fieldOfStudy]': '',
            'application[user][firstName]' : '',
            'application[user][lastName]' : '',
            'application[user][phone]' : '',
            'application[user][email]' : '',
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.showConfirmation = this.showConfirmation.bind(this);
        this.hideConfirmation = this.hideConfirmation.bind(this);
        this.getClosestDepartment = this.getClosestDepartment.bind(this);
    }

    handleChange(application){
        this.setState({[application.target.name]: application.target.value});
    }

    handleSubmit(){
        const { department, confirmationBoxVisible, fieldOfStudies, ...newState } = this.state; // Fjerner departments og...
        ApplicationApi.post(newState);
        this.showConfirmation();
        // TODO: sende mail
    }

    showConfirmation(){
        this.setState({confirmationBoxVisible : true});
    }

    hideConfirmation(){
        this.setState({confirmationBoxVisible : false});
    }

    async getClosestDepartment(shortName){
        const department = await DepartmentApi.getByShortName(shortName);
        const fieldOfStudies = department.field_of_study;
        this.setState({
            department,
            fieldOfStudies
        });
    }


    render() {
        const fieldOfStudy = this.state.fieldOfStudies.map(fieldOfStudy =>{ // TODO: legg til departments
            return (
                <option key={fieldOfStudy.id} value={fieldOfStudy} >
                    {fieldOfStudy.name}
                </option>
            );
        });

        return (
            <div>
                <ConfirmationBox show={this.state.confirmationBoxVisible} onClose={this.hideConfirmation}/>
                <Form onSubmit={this.handleSubmit} >
                    <Form.Group widths='equal'>
                        <Form.Field><input name="application[user][firstName]" placeholder="Fornavn" value={this.state.firstName} onChange={this.handleChange} required/></Form.Field>
                        <Form.Field><input name="application[user][lastName]" placeholder='Etternavn' value={this.state.lastName} onChange={this.handleChange} required/></Form.Field>
                    </Form.Group>

                    <Form.Group widths='equal'>
                        <Form.Field><input name="application[user][phone]" placeholder='Telefonnummer' value={this.state.phone} onChange={this.handleChange} type="number" required/></Form.Field>
                        <Form.Field><input name="application[user][email]" placeholder='E-post' value={this.state.email} onChange={this.handleChange} required/></Form.Field>
                    </Form.Group>

                    <Form.Group widths='equal'>
                        <Form.Field><select name="application[user][fieldOfStudy]" label='Linje' value={this.state.fieldOfStudy} onChange={this.handleChange}>
                            {fieldOfStudy}
                        </select></Form.Field>

                    </Form.Group>

                    <Form.Field><Button id='submitBtn'>Send inn</Button></Form.Field>
                </Form>

                <Geolocation closestDepartment={ this.getClosestDepartment } />

            </div>
        )
    }
}

export default ApplicationForm;
