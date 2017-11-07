import React from 'react';

import {NavLink} from 'react-router-dom';
import {Menu} from 'semantic-ui-react';

import './Menu.css'

export default ({user}) => {
  const style = {
    top: '104px'
  };
  return (
      <Menu vertical fixed={'left'} size={'huge'} style={style}>
        <NavLink activeClassName={'active'} exact to={'/dashboard'}>
          <Menu.Item name='DASHBOARD' onClick={this.handleItemClick}/>
        </NavLink>
        <Menu.Item>
          <Menu.Header>{user.first_name} {user.last_name}</Menu.Header>

          <Menu.Menu>
            <NavLink to={'/dashboard/profil'}><Menu.Item name='Profil' onClick={this.handleItemClick}/></NavLink>
            <Menu.Item name='consumer' onClick={this.handleItemClick}/>
          </Menu.Menu>
        </Menu.Item>

        <Menu.Item>
          <Menu.Header>Team - IT</Menu.Header>

          <Menu.Menu>
            <Menu.Item name='rails' onClick={this.handleItemClick}/>
            <Menu.Item name='python' onClick={this.handleItemClick}/>
            <Menu.Item name='php' onClick={this.handleItemClick}/>
          </Menu.Menu>
        </Menu.Item>

        <Menu.Item>
          <Menu.Header>Avdeling - NTNU</Menu.Header>

          <Menu.Menu>
            <Menu.Item name='shared' onClick={this.handleItemClick}/>
            <Menu.Item name='dedicated' onClick={this.handleItemClick}/>
          </Menu.Menu>
        </Menu.Item>

        <Menu.Item>
          <Menu.Header>Support</Menu.Header>

          <Menu.Menu>
            <Menu.Item name='email' onClick={this.handleItemClick}>
              E-mail Support
            </Menu.Item>

            <Menu.Item name='faq' onClick={this.handleItemClick}>
              FAQs
            </Menu.Item>
          </Menu.Menu>
        </Menu.Item>
      </Menu>
  );
}
