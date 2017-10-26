import React, {Component} from 'react';
import {Link, NavLink} from 'react-router-dom';
import {Image, Menu, Responsive} from 'semantic-ui-react';
import logo from '../images/logo_condensed.png';
import './Header.css';
import {slide as BurgerMenu} from 'react-burger-menu';

class Header extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isOpen: false
        };

        this.closeBurgerMenu = this.closeBurgerMenu.bind(this);
    }

    closeBurgerMenu() {
        this.setState({
            isOpen: false
        });
    }

    render() {
        return (
            <div className="top-header">
                <Responsive minWidth={Responsive.onlyTablet.minWidth}>
                    <Link to={'/'}>
                        <Image className="logo" src={logo}/>
                    </Link>

                    <Menu secondary className="main-navigation">
                        <Menu.Item link><Link to={'/assistenter'}>Assistenter</Link></Menu.Item>
                        <Menu.Item link><Link to={'/team'}>Team</Link></Menu.Item>
                        <Menu.Item link><Link to={'/laerere'}>Lærere</Link></Menu.Item>
                        <Menu.Item link><Link to={'/foreldre'}>Foreldre</Link></Menu.Item>
                        <Menu.Item link><Link to={'/om-oss'}>Om oss</Link></Menu.Item>
                    </Menu>
                </Responsive>

                <Responsive {...Responsive.onlyMobile}>
                        <Image className="mobile-logo" src={logo}/>
                    <BurgerMenu isOpen={this.state.isOpen}>
                        <li className="linkGroup" onClick={this.closeBurgerMenu}>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/'}>Hjem</NavLink></ul>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/assistenter'}>Assistenter</NavLink></ul>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/team'}>Team</NavLink></ul>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/laerere'}>Lærere</NavLink></ul>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/foreldre'}>Foreldre</NavLink></ul>
                            <ul><NavLink activeClassName="active" className="burgerLinks" exact to={'/om-oss'}>Om oss</NavLink></ul>
                        </li>
                    </BurgerMenu>
                </Responsive>
            </div>
        );
    }
}

export default Header;
