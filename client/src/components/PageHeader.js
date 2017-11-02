import React from 'react';
import "./PageHeader.css";

export default ({children}) => {
    return (
        <header className="page-header">
            {children}
        </header>
    )
}