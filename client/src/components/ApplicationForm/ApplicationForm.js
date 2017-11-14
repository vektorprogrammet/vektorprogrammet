import React from 'react';
import { Button, Form, Header } from 'semantic-ui-react';
import { Field } from 'redux-form';
import DepartmentDropdown from '../Form/DepartmentDropdown';
import FieldOfStudyDropdown from '../Form/FieldOfStudyDropdown';

export default ({departments, onSubmit, selectedDepartment, departmentChange}) => {
  const fieldOfStudies = selectedDepartment ? selectedDepartment.field_of_study : [];

  return (
    <div>
      <Header as='h3'>Send oss din søknad</Header>
      <Form onSubmit={onSubmit}>

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
            <DepartmentDropdown departments={departments} onChange={departmentChange} />
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
