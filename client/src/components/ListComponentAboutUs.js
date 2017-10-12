import React, {Component} from 'react';
import {List, Button} from'semantic-ui-react';

class ListComponentAboutUs extends Component{
    constructor(props){
        super(props);
    }
    render() {
        return (
            <List size={"massive"}>
                <List.Item icon='question circle' content={<a href={"#FAQ"}>FAQ</a>} />
                <List.Item icon='envelope' content={<a href={"#kontakt-info"}>Kontaktinformasjon</a>} />
                <List.Item
                    icon='talk'
                    content={
                        <Button basic color='blue' onClick={this.props.show}>Kontakt oss nå!</Button>
                    }
                />
            </List>
        );
    }
}

export default ListComponentAboutUs;