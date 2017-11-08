import React from 'react';
import { Dropdown, Image } from 'semantic-ui-react';
import profileImg from '../images/fake-profile.jpg';
import { NavLink } from 'react-router-dom';
import './UserMenu.css';

const UserMenu = ({user, history, logout}) => {
    const loggedIn = user && user.hasOwnProperty('first_name');
    if (!loggedIn) {
        return <NavLink className="header-login-link" exact to={'/login'}>Logg inn</NavLink>;
    }

    const trigger = (
        <span>
            <Image avatar src={profileImg}/> {user.first_name} {user.last_name}
        </span>
    );
    return (
        <Dropdown className="user-menu-logged-in" trigger={trigger} pointing='top' icon={null}>
            <Dropdown.Menu>
                <Dropdown.Item key='user' text='Dashboard' icon='user' onClick={() => {history.push('/dashboard')}} />
                <Dropdown.Item key='settings' text='Innstillinger' icon='settings' />
                <Dropdown.Item key='sign-out' text='Logg ut' icon='sign out' onClick={() => {logout(); history.push('/')}} />
            </Dropdown.Menu>
        </Dropdown>
    );
};

export default UserMenu;
