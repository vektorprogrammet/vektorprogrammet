import React from 'react';

export default ({fieldOfStudies, value, onChange}) => (
  <select
    name="fieldOfStudyId"
    label='Linje'
    value={value}
    onChange={onChange}>
    <option value="">Velg linje...</option>

    {fieldOfStudies.map(fieldOfStudy => (
      <option key={fieldOfStudy.id} value={fieldOfStudy.id}>
        {fieldOfStudy.name}
      </option>
    ))}

  </select>
);
