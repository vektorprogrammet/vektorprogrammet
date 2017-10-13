import React from 'react';
import './ContactInformation.css';

export const ContactInfo = () => {
    return (
        <section>
            <ul className="unordered-list">
                <li className="list-container">
                    Kontakt:
                    <li className="list-ntnu">
                        <a href="mailto:NTNU@gmail.com">Trondheim - NTNU</a>
                    </li>
                    <li className="list-hist">
                        <a href="mailto:HIST@gmail.com">Trondheim - HIST</a>
                    </li>
                    <li className="list-nmbu">
                        <a href="mailto:NMBU@gmail.com">Ås</a>
                    </li>
                    <li  className="list-uio">
                        <a href="mailto:UiO@gmail.com">Oslo</a>
                    </li>
                </li>
            </ul>
        </section>
    );
};

export default ContactInfo;