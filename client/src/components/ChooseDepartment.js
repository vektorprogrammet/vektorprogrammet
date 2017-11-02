import React, {Component} from 'react';
import {Image, Card, Icon, Grid} from 'semantic-ui-react';
import './ChooseDepartment.css';

class ChooseDepartment extends Component {

    handleClick = () => {
        alert("Hello");
    };

    render() {
        return (
            <div className="choose-department">
                <Grid centered stackable>
                    <Card fluid color="grey" onClick={this.handleClick}>
                        <Image src="https://via.placeholder.com/200x200"/>
                        <Card.Content>
                            <Card.Header>
                                {this.props.departmentName}
                            </Card.Header>
                            <Card.Meta>
                        <span className='date'>
                        Joined in 2015
                        </span>
                            </Card.Meta>
                            <Card.Description>
                                Matthew is a musician living in Nashville.
                                Matthew is a musician living in Nashville.
                                Matthew is a musician living in Nashville.
                                Matthew is a musician living in Nashville.
                            </Card.Description>
                        </Card.Content>
                        <Card.Content extra>
                            <a>
                                <Icon name='user' />
                                22 Friends
                            </a>
                        </Card.Content>
                    </Card>
                </Grid>
            </div>
        );
    }
}

export default ChooseDepartment;