import React from 'react';
import './Dashboard.css';

export default ({user}) => (
  <div className="dashboard">
    <p>Du er logget inn som</p>
    <h2>{user.first_name} {user.last_name}</h2>
  </div>
)
