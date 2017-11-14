import React from 'react';
import {Field} from 'redux-form';

export default ({departments, onChange}) => (
  <Field
    component="select"
    name="department"
    label='Avdeling'
    parse={(id) => departments.find(d => d.id === parseInt(id, 10))}
    format={value => value && value.hasOwnProperty('id') ? value.id : value}
    onChange={onChange}
  >

    {departments.map(department => (
      <option key={department.id} value={department.id}>
        {department.short_name}
      </option>
    ))}

  </Field>
);
