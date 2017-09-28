import React, {Component} from 'react';
import './NewsletterPopUp.css';
import { Button, Form, Icon } from 'semantic-ui-react';

class NewsletterPopUp extends Component {

    render() {
        return (
            <div className={'newsletterPopUp' + (this.props.show ? ' visible' : '')}>
                <Icon name={'remove'} onClick={this.props.onClose} className="newsletterIcon"/>
                <br/>
                <p>Du virker interessert i Vektorprogrammet. <br/> Vil du melde deg på vårt nyhetsbrev?</p>
                <Form>
                    <Form.Input placeholder='Epostadresse' />
                    <Button>Meld meg på!</Button>
                </Form>
            </div>
        )
    }
}

export default NewsletterPopUp;
