import React, {Component} from 'react';
import { Button, Form} from 'semantic-ui-react';
import {DepartmentAPI} from '../api/DepartmentApi';

class ApplicationForm extends Component {

    constructor(props){
        super(props);
        this.state = {
            departments:[],
            department: '',
            firstName : '',
            lastName : '',
            phone : '',
            email : '',
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(){}
    handleSubmit(){}

    async componentDidMount() {
        const department = DepartmentAPI.getAll(); // TODO: dropdown for å velge department
        this.setState({
            departments: await department,
        });
    }

    render() {

        console.log(this.state.departments);

        const department = this.state.departments.map(department =>{
            return (
                <option key={department.id} value={department.id} >
                    {department.short_name}
                </option>
            );
        });

        return (
            <Form>
                <Form.Group widths='equal'>
                    <Form.Field><input placeholder="Fornavn" value={this.state.firstName} onChange={this.handleChange}/></Form.Field>
                    <Form.Field><input placeholder='Etternavn' value={this.state.lastName} onChange={this.handleChange}/></Form.Field>
                </Form.Group>

                <Form.Group widths='equal'>
                    <Form.Field><input placeholder='Telefonnummer' value={this.state.phone} onChange={this.handleChange} type="number"/></Form.Field>
                    <Form.Field><input placeholder='E-post' value={this.state.email} onChange={this.handleChange}/></Form.Field>
                </Form.Group>

                <Form.Group widths='equal'>
                    <Form.Field><select label='Avdeling' value={this.state.department} onChange={this.handleChange}>
                        {department}
                    </select></Form.Field>

                </Form.Group>

                <Form.Field><textarea id='description' placeholder='Description' value={this.state.description} onChange={this.handleChange}/></Form.Field>
                <Form.Field><input id='time' type="datetime-local" value={this.state.time} onChange={this.handleChange}/></Form.Field>
                <Form.Field><Button id='submitBtn' onClick={this.handleSubmit} >Submit</Button></Form.Field>
            </Form>
        )
    }
}

export default ApplicationForm;
