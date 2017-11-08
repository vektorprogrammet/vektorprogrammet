import React from 'react';

export default ({departments, value, onChange}) => (
  <select
    name="department"
    label='Avdeling'
    value={value}
    onChange={onChange}>

    {departments.map(department => (
      <option key={department.id} value={department.id}>
        {department.short_name}
      </option>
    ))}

  </select>
);
