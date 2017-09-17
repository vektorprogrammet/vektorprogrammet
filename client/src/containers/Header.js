import React, {Component} from 'react';
import { Link } from 'react-router-dom';
import { Grid, Image } from 'semantic-ui-react';
import logo from '../images/logo_condensed.png';
import './Header.css';

class Header extends Component {
  render() {
    return (
    <Grid>
      <Grid.Row centered>
        <Link to={'/'}>
          <Image className="logo" src={logo} />
        </Link>
      </Grid.Row>
      <Grid.Row centered>
        <nav className="main-navigation">
          <Link to={'/assistenter'}>Assistenter</Link>
          <Link to={'/team'}>Team</Link>
          <Link to={'/laerere'}>Lærere</Link>
          <Link to={'/foreldre'}>Foreldre</Link>
          <Link to={'/om-oss'}>Om oss</Link>
        </nav>
      </Grid.Row>
    </Grid>
    );
  }
}

export default Header;
