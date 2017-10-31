import React, {Component} from 'react';
import {Link, NavLink} from 'react-router-dom';
import {Image, Menu, Responsive} from 'semantic-ui-react';
import logo from '../images/vektor-logo.svg';
import './Header.css';
import {slide as BurgerMenu} from 'react-burger-menu';

class Header extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isOpen: false
        };
    }

    closeBurgerMenu = () => {
        this.setState({
            isOpen: false
        });
    };

    render() {
        return (
            <div className="top-header">
                <Responsive minWidth={Responsive.onlyTablet.minWidth}>

                    <Menu secondary className="main-navigation">
                        <Link to={'/'}>
                            <Image className="logo" src={logo}/>
                        </Link>
                        <NavLink activeClassName="active" className="menuLinks" exact to={'/assistenter'}>Assistenter</NavLink>
                        <NavLink activeClassName="active" className="menuLinks" exact to={'/team'}>Team</NavLink>
                        {/*<NavLink activeClassName="active" className="menuLinks" exact to={'/laerere'}>Lærere</NavLink>*/}
                        {/*<NavLink activeClassName="active" className="menuLinks" exact to={'/foreldre'}>Foreldre</NavLink>*/}
                        <NavLink activeClassName="active" className="menuLinks" exact to={'/om-oss'}>Om oss</NavLink>
                        <div className="header-login-container">
                            <NavLink activeClassName="hide" className="header-login-link" exact to={'/login'}>Logg inn</NavLink>
                            <NavLink activeClassName="active" className="menuLinks" exact to={'/kontakt'}>Kontakt</NavLink>
                        </div>
                    </Menu>
                </Responsive>

                <Responsive {...Responsive.onlyMobile}>
                    <Image className="mobile-logo" src={logo}/>
                    <BurgerMenu isOpen={this.state.isOpen}>
                        <li className="linkGroup" onClick={this.closeBurgerMenu}>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/'}>Hjem</NavLink></ul>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/assistenter'}>Assistenter</NavLink></ul>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/team'}>Team</NavLink></ul>
                            {/*<ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/laerere'}>Lærere</NavLink></ul>*/}
                            {/*<ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/foreldre'}>Foreldre</NavLink></ul>*/}
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/om-oss'}>Om oss</NavLink></ul>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/kontakt'}>Kontakt</NavLink></ul>
                        </li>
                    </BurgerMenu>
                </Responsive>
            </div>
        );
    }
}

export default Header;
