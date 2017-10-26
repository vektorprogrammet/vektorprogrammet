import React, {Component} from 'react';
import {Icon, Card, Image} from 'semantic-ui-react';

export const TeamCard = ({imagePath, name, email, shortDesc, accept_application}) => {
    return (
        <Card>
            <Image className="teamImage" src={imagePath}/>
            <Card.Content>
                <Card.Header>{name}</Card.Header>
                <Card.Meta className="teamCardMeta">{email}</Card.Meta>
                <Card.Description className="teamCardDesc">{shortDesc}</Card.Description>
            </Card.Content>
            { accept_application &&
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
