import React from 'react';
import { Button, Form } from 'semantic-ui-react';
import { Field } from 'redux-form';
import DepartmentDropdown from "../Form/DepartmentDropdown";

//TODO: han en "onSubmit" som snakker med state
export default ({departments, onSubmit, departmentChange}) => {

  return (
    <div>
      <Form onSubmit={onSubmit}>
        <Form.Group widths='equal'>
          <Form.Field>
            <DepartmentDropdown departments={departments} onChange={departmentChange}/>
          </Form.Field>
        </Form.Group>
        <Form.Group widths='equal'>
          <Form.Field>
            <Field
              name="firstName"
              component="input"
              type="text"
              placeholder="Navn"
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


        <Form.Field><Button>Registrer</Button></Form.Field>
      </Form>
    </div>
  );
};