import React, {Component} from 'react';
import {Button, Form} from 'semantic-ui-react';
import {DepartmentApi} from '../api/DepartmentApi';
import {ApplicationApi} from '../api/ApplicationApi';
import ConfirmationBox from '../components/ConfirmationBox';

class ApplicationForm extends Component {

    constructor(props){
        super(props);
        this.state = {
            departments:[],
            confirmationBoxVisible: false,
            department: '',
            firstName : '',
            lastName : '',
            phone : '',
            email : '',
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.showConfirmation = this.showConfirmation.bind(this);
        this.hideConfirmation = this.hideConfirmation.bind(this);
    }

    handleChange(application){
        this.setState({[application.target.id]: application.target.value});
    }

    handleSubmit(){
        const { departments, confirmationBoxVisible, ...newState } = this.state; // Fjerner departments og...
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

    async componentDidMount() {
        const department = DepartmentApi.getByActiveSemester();
        this.setState({
            departments: await department,
        });
    }

    render() {
        const department = this.state.departments.map(department =>{
            return (
                <option key={department.id} value={department.id} >
                    {department.short_name}
                </option>
            );
        });

        return (
            <div>
                <ConfirmationBox show={this.state.confirmationBoxVisible} onClose={this.hideConfirmation}/>
                <Form onSubmit={this.handleSubmit} >
                    <Form.Group widths='equal'>
                        <Form.Field><input id="firstName" placeholder="Fornavn" value={this.state.firstName} onChange={this.handleChange} required/></Form.Field>
                        <Form.Field><input id="lastName" placeholder='Etternavn' value={this.state.lastName} onChange={this.handleChange} required/></Form.Field>
                    </Form.Group>

                    <Form.Group widths='equal'>
                        <Form.Field><input id="phone" placeholder='Telefonnummer' value={this.state.phone} onChange={this.handleChange} type="number" required/></Form.Field>
                        <Form.Field><input id="email" placeholder='E-post' value={this.state.email} onChange={this.handleChange} required/></Form.Field>
                    </Form.Group>

                    <Form.Group widths='equal'>
                        <Form.Field><select id="department" label='Avdeling' value={this.state.department} onChange={this.handleChange}>
                            {department}
                        </select></Form.Field>

                    </Form.Group>

                    <Form.Field><Button id='submitBtn'>Send inn</Button></Form.Field>
                </Form>

            </div>
        )
    }
}

export default ApplicationForm;
