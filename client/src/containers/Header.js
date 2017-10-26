import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import {Image, Menu, Responsive} from 'semantic-ui-react';
import logo from '../images/logo_condensed.png';
import './Header.css';
import {slide as BurgerMenu} from 'react-burger-menu';

class Header extends Component {
    render() {
        return (
            <div>
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
                    <BurgerMenu>
                        <Link to={'/assistenter'}>Assistenter</Link>
                        <Link to={'/team'}>Team</Link>
                        <Link to={'/laerere'}>Lærere</Link>
                        <Link to={'/foreldre'}>Foreldre</Link>
                        <Link to={'/om-oss'}>Om oss</Link>
                    </BurgerMenu>
                </Responsive>
            </div>
        );
    }
}

export default Header;
