import React, {Component} from 'react';
import { Modal, Icon } from 'semantic-ui-react';
import './ConfirmationBox.css';

class ConfirmationBox extends Component {

    render() {
        return (
            <Modal size='tiny' open={this.props.show}>
                <Modal.Header>
                    Takk for din søknad <Icon name={'remove'} onClick={this.props.onClose} className="confirmationIcon"/>
                </Modal.Header>
                <Modal.Content>
                    <p>Søknaden er nå registrert. Du vil innen kort tid få kopi på mail.</p>
                </Modal.Content>
            </Modal>
        )
    }
}

export default ConfirmationBox;
