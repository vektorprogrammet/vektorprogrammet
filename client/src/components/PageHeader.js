import React from 'react';
import './PageHeader.css';

export default ({children, className}) => {
  return (
    <header className={className ? className + ' page-header' : 'page-header'}>
      {children}
    </header>
  );
}
