import React, {Component} from 'react';
import './ContactUsPopUp.css';
import { Form, Icon, Image, Button} from 'semantic-ui-react';
import message from '../images/message-icon.png';
class ContactUsPopUp extends Component {
    /*TODO:Should this component have states? Or should it be higher up?*/
    constructor(props) {
        super(props);
        this.state = {
            formInputName: '',
            formInputEmail: '',
            formInputSubject: '',
            formInputCaptcha: '',
            formInputMessage: ''
        };
    }

    handlePropagation = (e) => {
        e.stopPropagation();
    };

    render() {
        if (!this.props.show) {
            return null;
        }
        const isMobile = this.props.windowWidth <= 500;
        const contactUsPopCSS = isMobile ? ' contactUsPopUpMobile' : 'contactUsPopUp';
        return (
            <div className={'backdrop' + (this.props.show ? ' visible' : '')} onClick={this.props.onClose}>
                <div className={contactUsPopCSS + (this.props.show ? ' visible' : '')} onClick={this.handlePropagation}>
                    <Icon name={'remove'} onClick={this.props.onClose} className="contactUsPopUp-icon"/>
                    <div className="header-frame">
                        <h2>Kontakt vektor ved NTNU {isMobile ? 'Mobile' : 'Computer'}</h2>
                        <img src={message}/>
                    </div>
                    <Form style={{padding: '0 15px'}}>
                        <Form.Group widths='equal'>
                            <Form.Field>
                                <Form.Input placeholder='Ditt navn' />
                                <br/>
                                <Form.Input placeholder='Din E-post' />
                                <br/>
                                <Form.Input placeholder='Emne' />
                                <br/>
                                /*TODO: Get the captcha-functionality to work */
                                <Image className="contactUsPopUp-captchaImage" src={"http://via.placeholder.com/100x50/"}/>
                                <br/>
                                <input/>
                            </Form.Field>
                            <Form.Field>
                                <Form.TextArea id="text-area" placeholder='Melding' />
                                <Button id="message-btn" primary>Send melding</Button>
                            </Form.Field>
                        </Form.Group>
                    </Form>
                </div>
            </div>
        )
    }
}

export default ContactUsPopUp;
