import React, { Component } from 'react';
import { Menu, Segment } from 'semantic-ui-react'

export default class TeamOverview extends Component {
  state = { activeItem: 'Trondheim' };

  handleItemClick = (e, { name }) => this.setState({ activeItem: name });

  render() {
    const { activeItem } = this.state;

    return (
      <div>
        <Menu fluid widths={5}>
          <Menu.Item name='Trondheim' active={activeItem === 'Trondheim'} onClick={this.handleItemClick} />
          <Menu.Item name='Oslo' active={activeItem === 'Oslo'} onClick={this.handleItemClick} />
          <Menu.Item name='Tromsø' active={activeItem === 'Tromsø'} onClick={this.handleItemClick} />
          <Menu.Item name='Bergen' active={activeItem === 'Bergen'} onClick={this.handleItemClick} />
          <Menu.Item name='Ås' active={activeItem === 'Ås'} onClick={this.handleItemClick} />
        </Menu>

        <Segment>
          <h1>Dette er teamet</h1>
        </Segment>
      </div>
    )
  }
}