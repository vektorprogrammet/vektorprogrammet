import React from 'react';
import { Button, Form } from 'semantic-ui-react';
import { Field, reduxForm, formValueSelector, change } from 'redux-form';
import { connect } from 'react-redux';
import DepartmentDropdown from './DepartmentDropdown';
import FieldOfStudyDropdown from './FieldOfStudyDropdown';
import { postApplication } from '../../actions/application';

let ApplicationForm = ({departments, handleSubmit, selectedDepartment, clearFieldOfStudy, submitApplication}) => {
  const fieldOfStudies = selectedDepartment ? selectedDepartment.field_of_study : [];

  return (
    <div>
      <Form onSubmit={handleSubmit(values => {submitApplication(values)})}>

        <Form.Group widths='equal'>
          <Form.Field>
            <Field
              name="firstName"
              component="input"
              type="text"
              placeholder="Fornavn"
              required/>
          </Form.Field>
          <Form.Field>
            <Field
              name="lastName"
              component="input"
              type="text"
              placeholder='Etternavn'
              required/>
          </Form.Field>
        </Form.Group>

        <Form.Group widths='equal'>
          <Form.Field>
            <Field
              name="phone"
              placeholder='Telefonnummer'
              component="input"
              type="number"
              required/>
          </Form.Field>
          <Form.Field>
            <Field
              name="email"
              component="input"
              placeholder='E-post'
              type="email"
              required/>
          </Form.Field>
        </Form.Group>

        <Form.Group widths='equal'>
          <Form.Field>
            <DepartmentDropdown departments={departments} onChange={clearFieldOfStudy} />
          </Form.Field>
          <Form.Field>
            <FieldOfStudyDropdown fieldOfStudies={fieldOfStudies}/>
          </Form.Field>
        </Form.Group>

        <Form.Field><Button>Send inn</Button></Form.Field>
      </Form>
    </div>
  );
};

const form = 'application';
const selector = formValueSelector(form);

ApplicationForm = reduxForm({
  form,
  enableReinitialize : true
})(ApplicationForm);

const mapStateToProps = state => ({
  initialValues: {
    department: state.departments[0]
  },
  selectedDepartment: selector(state, 'department'),
});

const mapDispatchToProps = dispatch => ({
  clearFieldOfStudy: () => {
    dispatch(change(form, 'fieldOfStudyId', ''))
  },
  submitApplication: application => {
    dispatch(postApplication(application))
  }
});

export default connect(mapStateToProps, mapDispatchToProps)(ApplicationForm);
