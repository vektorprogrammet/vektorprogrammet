import React, {Component} from 'react';
import {List, Button} from 'semantic-ui-react';
import './ListComponentAboutUs.css';

class ListComponentAboutUs extends Component{
    render() {
        const width = window.innerWidth;
        const check = width < 770;
        const idName = check ? 'horisontal' : '';

        return (
            <List size={"massive"} id="mainList">
                <List.Item icon='question circle' content={<a href={"#FAQ"}>FAQ</a>} id={idName}/>
                <List.Item icon='envelope' content={<a href={"#kontakt-info"}>Kontaktinfo</a>} id={idName}/>
                <List.Item
                    icon='talk'
                    content={
                        <Button basic color='blue' onClick={this.props.onClick}>Kontakt oss nå!</Button>
                    }
                    id={idName}
                />
            </List>
        );
    }
}

export default ListComponentAboutUs;