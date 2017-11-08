import React from 'react';
import './PageHeader.css';

export default ({children, className}) => {
  return (
    <header className={'page-header ' + className}>
      {children}
    </header>
  );
}
