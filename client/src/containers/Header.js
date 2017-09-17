import React, {Component} from 'react';
import { Link } from 'react-router-dom'

class Header extends Component {
  render() {
    return (
        <nav>
          <Link to={'/'}>Hjem</Link>
          <Link to={'/assistenter'}>Assistenter</Link>
          <Link to={'/team'}>Team</Link>
        </nav>
    );
  }
}

export default Header;
