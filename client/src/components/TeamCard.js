import React, {Component} from 'react';
import {Icon, Card, Image} from 'semantic-ui-react';

export const TeamCard = props => {
    return (
            <Card>
                <Image className="teamImage" src={props.imagePath}/>
                <Card.Content>
                    <Card.Header>{props.name}</Card.Header>
                    <Card.Meta className="teamCardMeta">{props.email}</Card.Meta>
                    <Card.Description className="teamCardDesc">{props.shortDesc}</Card.Description>
                </Card.Content>
                { props.accept_application &&
                <Card.Content extra className="teamCardExtra">
                    <a>
                        <Icon color="green" name='star'/>
                        Har opptak!
                    </a>
                </Card.Content>
                }
            </Card>
        );
}
