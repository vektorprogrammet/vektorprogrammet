import React, {Component} from 'react';
import {Icon, Card, Image} from 'semantic-ui-react';

export default class TeamCard extends Component {
    render() {
        const card = (
            <Card>
                <Image className="teamImage" src={this.props.imagePath}/>
                <Card.Content>
                    <Card.Header>{this.props.name}</Card.Header>
                    <Card.Meta className="teamCardMeta">{this.props.email}</Card.Meta>
                    <Card.Description className="teamCardDesc">{this.props.shortDesc}</Card.Description>
                </Card.Content>
                { this.props.accept_application &&
                <Card.Content extra className="teamCardExtra">
                    <a>
                        <Icon color="green" name='star'/>
                        Har opptak!
                    </a>
                </Card.Content>
                }
            </Card>
        );

        return card;
    }
}
