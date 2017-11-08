import React from 'react';
import { Button, Form } from 'semantic-ui-react';
import DepartmentDropdown from './DepartmentDropdown';
import FieldOfStudyDropdown from './FieldOfStudyDropdown';

export default ({application, departments, onChange, onSubmit}) => {
  const fieldOfStudies = application.department.hasOwnProperty('field_of_study') ? application.department.field_of_study : [];

  return (
    <div>
      <Form onSubmit={onSubmit}>

        <Form.Group widths='equal'>
          <Form.Field>
            <input name="firstName" placeholder="Fornavn"
                   value={application.firstName}
                   onChange={onChange}
                   required/>
          </Form.Field>
          <Form.Field>
            <input name="lastName"
                   placeholder='Etternavn'
                   value={application.lastName}
                   onChange={onChange}
                   required/>
          </Form.Field>
        </Form.Group>

        <Form.Group widths='equal'>
          <Form.Field>
            <input name="phone"
                   placeholder='Telefonnummer'
                   value={application.phone}
                   onChange={onChange}
                   type="number"
                   required/>
          </Form.Field>
          <Form.Field>
            <input name="email"
                   placeholder='E-post'
                   value={application.email}
                   onChange={onChange}
                   type="email"
                   required/>
          </Form.Field>
        </Form.Group>

        <Form.Group widths='equal'>
          <Form.Field>
            <DepartmentDropdown
              departments={departments}
              value={application.department.id}
              onChange={onChange}
            />
          </Form.Field>
          <Form.Field>
            <FieldOfStudyDropdown
              fieldOfStudies={fieldOfStudies}
              value={application.fieldOfStudyId}
              onChange={onChange}
            />
          </Form.Field>
        </Form.Group>

        <Form.Field><Button>Send inn</Button></Form.Field>
      </Form>
    </div>
  );
}
