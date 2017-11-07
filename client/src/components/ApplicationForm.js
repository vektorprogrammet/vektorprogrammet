import React, {Component} from 'react';
import {Button, Form, Header} from 'semantic-ui-react';
import {DepartmentApi} from '../api/DepartmentApi';
import {ApplicationApi} from '../api/ApplicationApi';
import ConfirmationBox from '../components/ConfirmationBox';

class ApplicationForm extends Component {

    constructor(props){
        super(props);
        this.state = {
            departments: [],
            department: '',
            activeAdmission: false,
            confirmationBoxVisible: false,
            application : {
                departmentId : '',
                fieldOfStudyId : '',
                firstName : '',
                lastName : '',
                phone : '',
                email : ''
            }
        };
    }

    handleChange = (event) =>{
        const application = {...this.state.application};
        application[event.target.name] = event.target.value;
        this.setState({application});
    };

    handleDepartmentChange = (event) =>{
        const application = {...this.state.application};
        const id = event.target.value;
        application.departmentId = id;
        const department = this.state.departments.find(department => department.id === id);
        this.setState({
            department,
            application
        });
    };

    handleSubmit = async () =>{
        const response = await ApplicationApi.post(this.state.application);
        if (response.ok){
            this.showConfirmation();
        }
    };


    showConfirmation = () =>{
        this.setState({confirmationBoxVisible : true});
    };

    hideConfirmation = () =>{
        this.setState({confirmationBoxVisible : false});
    };

    async componentDidMount(){
        const departments = await DepartmentApi.getByActiveSemester();
        if (departments.length !== 0){
            const application = {...this.state.application};
            application.departmentId = departments[0].id;
            application.fieldOfStudyId = departments[0].field_of_study[0];
            this.setState({
                departments,
                department: departments[0],
                application,
                activeAdmission : true
            });
        }
    }


    render() {
        const departments = this.state.departments.map(department =>{
            return (
                <option key={department.id} value={department.id} >
                    {department.short_name}
                </option>

            );
        });
        const isActiveAdmission = this.state.activeAdmission;
        const fieldOfStudies = this.state.department.field_of_study || [];
        const fieldOfStudyOptions = fieldOfStudies.map(fieldOfStudy =>{
            return (
                <option key={fieldOfStudy.id} value={fieldOfStudy.id} >
                    {fieldOfStudy.name}
                </option>
            );
        });

        return (
            <div>
                {isActiveAdmission ?
                    <div>
                        <ConfirmationBox show={this.state.confirmationBoxVisible} onClose={this.hideConfirmation}/>
                        <Form onSubmit={this.handleSubmit}>
                            <Form.Group widths='equal'>
                                <Form.Field><input name="firstName" placeholder="Fornavn"
                                                   value={this.state.application.firstName} onChange={this.handleChange}
                                                   required/></Form.Field>
                                <Form.Field><input name="lastName" placeholder='Etternavn'
                                                   value={this.state.application.lastName} onChange={this.handleChange}
                                                   required/></Form.Field>
                            </Form.Group>

                            <Form.Group widths='equal'>
                                <Form.Field><input name="phone" placeholder='Telefonnummer'
                                                   value={this.state.application.phone} onChange={this.handleChange}
                                                   type="number" required/></Form.Field>
                                <Form.Field><input name="email" placeholder='E-post'
                                                   value={this.state.application.email} onChange={this.handleChange} type="email"
                                                   required/></Form.Field>
                            </Form.Group>

                            <Form.Group widths='equal'>
                                <Form.Field><select name="department" label='Avdeling' value={this.state.department.id}
                                                    onChange={this.handleDepartmentChange}>
                                    {departments}
                                </select></Form.Field>
                                <Form.Field><select name="fieldOfStudyId" label='Linje'
                                                    value={this.state.application.fieldOfStudyId}
                                                    onChange={this.handleChange}>
                                    {fieldOfStudyOptions}
                                </select></Form.Field>

                            </Form.Group>

                            <Form.Field><Button>Send inn</Button></Form.Field>
                        </Form>

                    </div>
                    :
                    <Header as='h4'>Det er dessverre ikke aktiv søkeperiode</Header>
                }
                </div>
        )
    }
}

export default ApplicationForm;
