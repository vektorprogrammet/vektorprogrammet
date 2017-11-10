import React, { Component } from 'react';
import { Link, NavLink, withRouter } from 'react-router-dom';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Image, Menu, Responsive } from 'semantic-ui-react';
import logo from '../images/vektor-logo.svg';
import './Header.css';
import { slide as BurgerMenu } from 'react-burger-menu';
import UserMenu from './UserMenu';

import { requestLogout } from '../actions/authentication';

class Header extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isOpen: false,
      fullWidth: props.location.pathname.indexOf('/dashboard') === 0,
    };
  }

  componentDidMount() {
    this.props.history.listen(location => {
      this.setState({fullWidth: location.pathname.indexOf('/dashboard') === 0});
    });
  }

  closeBurgerMenu = () => {
    this.setState({
      isOpen: false,
    });
  };

  render() {
    return (
      <div>
        {this.state.fullWidth && <div className={'header-push-content'}/>}
        <div className={this.state.fullWidth ? 'top-header full-width fixed' : 'top-header'}>
          <Responsive minWidth={Responsive.onlyTablet.minWidth}>

            <Menu secondary className="main-navigation">
              <Link to={'/'}>
                <Image className="logo" src={logo}/>
              </Link>
              <NavLink activeClassName="active" className="menuLinks" exact
                       to={'/assistenter'}>Assistenter</NavLink>
              <NavLink activeClassName="active" className="menuLinks" exact to={'/team'}>Team</NavLink>
              <NavLink activeClassName="active" className="menuLinks" exact to={'/om-oss'}>Om oss</NavLink>
              <UserMenu
                user={this.props.user}
                history={this.props.history}
                logout={this.props.requestLogout}
              />
              <NavLink activeClassName="active" className="menuLinks" exact to={'/kontakt'}>Kontakt</NavLink>
            </Menu>
          </Responsive>

          <Responsive {...Responsive.onlyMobile}>
            <Image className="mobile-logo" src={logo}/>
            <BurgerMenu isOpen={this.state.isOpen}>
              <li className="linkGroup" onClick={this.closeBurgerMenu}>
                <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/'}>Hjem</NavLink>
                </ul>
                <ul><NavLink activeClassName="active" className="burgerLinks" exact
                             to={'/assistenter'}>Assistenter</NavLink>
                </ul>
                <ul><NavLink activeClassName="active" className="burgerLinks" exact
                             to={'/team'}>Team</NavLink></ul>
                <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/om-oss'}>Om
                  oss</NavLink></ul>
                <ul><NavLink activeClassName="active" className="burgerLinks" exact
                             to={'/kontakt'}>Kontakt</NavLink></ul>
                <ul><NavLink className="header-login-link" exact to={'/dashboard'}>Logg inn</NavLink></ul>
              </li>
            </BurgerMenu>
          </Responsive>
        </div>
      </div>
    );
  }
}

const mapStateToProps = state => ({
  user: state.user,
});
const mapDispatchToProps = dispatch => bindActionCreators({
  requestLogout,
}, dispatch);

export default connect(mapStateToProps, mapDispatchToProps, null, {pure: false})(withRouter(props =>
  <Header {...props}/>));
