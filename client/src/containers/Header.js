import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import {Image, Menu} from 'semantic-ui-react';
import logo from '../images/logo_condensed.png';
import './Header.css';

class Header extends Component {
    render() {
        return (
            <div>
                <Link to={'/'}>
                    <Image className="logo" src={logo}/>
                </Link>

                <Menu stackable secondary className="main-navigation">
                    <Menu.Item link><Link to={'/assistenter'}>Assistenter</Link></Menu.Item>
                    <Menu.Item link><Link to={'/team'}>Team</Link></Menu.Item>
                    <Menu.Item link><Link to={'/laerere'}>Lærere</Link></Menu.Item>
                    <Menu.Item link><Link to={'/foreldre'}>Foreldre</Link></Menu.Item>
                    <Menu.Item link><Link to={'/om-oss'}>Om oss</Link></Menu.Item>
                </Menu>
            </div>
        );
    }
}

export default Header;
