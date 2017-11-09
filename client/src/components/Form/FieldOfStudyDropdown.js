import React from 'react';
import {Field} from 'redux-form';

export default ({fieldOfStudies}) => (
  <Field
    component="select"
    name="fieldOfStudyId"
    label='Linje'
  >
    <option value="">Velg linje...</option>

    {fieldOfStudies.map(fieldOfStudy => (
      <option key={fieldOfStudy.id} value={fieldOfStudy.id}>
        {fieldOfStudy.name}
      </option>
    ))}

  </Field>
);
