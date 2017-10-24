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
            departments: [],
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
        this.handleDepartmentChange = this.handleDepartmentChange.bind(this);
        this.handleFieldOfStudyChange = this.handleFieldOfStudyChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.showConfirmation = this.showConfirmation.bind(this);
        this.hideConfirmation = this.hideConfirmation.bind(this);
        this.getClosestDepartment = this.getClosestDepartment.bind(this);
    }

    handleChange(event){
        this.setState({[event.target.name]: event.target.value});
        console.log(event.target.value);
    }

    handleDepartmentChange(event){
        const id = event.target.value;
        const department = this.state.departments.find(department => department.id == id);
        const fieldOfStudies = department.field_of_study;
        this.setState({
            department : department,
            fieldOfStudies : fieldOfStudies
        });
    }

    handleFieldOfStudyChange(event){
        const id = event.target.value;
        const fieldOfStudy = this.state.fieldOfStudies.find(fieldOfStudy => fieldOfStudy.id == id);
        this.setState({
            [event.target.name] : fieldOfStudy
        })
    }

    handleSubmit(){
        const { department, departments, confirmationBoxVisible, fieldOfStudies, ...newState } = this.state; // TODO: Lage objekt
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

    async componentDidMount(){
        const departments = await DepartmentApi.getAll();
        this.setState({
            departments
        });
    }


    render() {
        const departments = this.state.departments.map(department =>{
            return (
                <option key={department.id} value={department.id} >
                    {department.short_name}
                </option>

            );
        });

        const fieldOfStudy = this.state.fieldOfStudies.map(fieldOfStudy =>{
            return (
                <option key={fieldOfStudy.id} value={fieldOfStudy.id} >
                    {fieldOfStudy.name}
                </option>
            );
        });


        return (
            <div>
                <ConfirmationBox show={this.state.confirmationBoxVisible} onClose={this.hideConfirmation}/>
                <Geolocation closestDepartment={ this.getClosestDepartment } />
                <Form onSubmit={this.handleSubmit} >
                    <Form.Group widths='equal'>
                        <Form.Field><input name="application[user][firstName]" placeholder="Fornavn" value={this.state['application[user][firstName']} onChange={this.handleChange} required/></Form.Field>
                        <Form.Field><input name="application[user][lastName]" placeholder='Etternavn' value={this.state['application[user]lastName']} onChange={this.handleChange} required/></Form.Field>
                    </Form.Group>

                    <Form.Group widths='equal'>
                        <Form.Field><input name="application[user][phone]" placeholder='Telefonnummer' value={this.state['application[user][phone]']} onChange={this.handleChange} type="number" required/></Form.Field>
                        <Form.Field><input name="application[user][email]" placeholder='E-post' value={this.state['application[user][email]']} onChange={this.handleChange} required/></Form.Field>
                    </Form.Group>

                    <Form.Group widths='equal'>
                        <Form.Field><select name="department" label='Avdeling' value={this.state.department} onChange={this.handleDepartmentChange}>
                            {departments}
                        </select></Form.Field>
                        <Form.Field><select name="application[user][fieldOfStudy]" label='Linje' value={this.state['application[user][fieldOfStudy]']} onChange={this.handleFieldOfStudyChange}>
                            {fieldOfStudy}
                        </select></Form.Field>

                    </Form.Group>

                    <Form.Field><Button id='submitBtn'>Send inn</Button></Form.Field>
                </Form>

            </div>
        )
    }
}

export default ApplicationForm;
