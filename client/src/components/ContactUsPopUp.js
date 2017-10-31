import React, {Component} from 'react';
import './ContactUsPopUp.css';
import SingleInput from './SingleInput';
import TextArea from './TextArea';
import { Form, Icon, Image, Button} from 'semantic-ui-react';
import message from '../images/message-icon.png';
class ContactUsPopUp extends Component {
    /*TODO:Should this component have states and logic? Or should it be higher up?*/
    constructor(props) {
        super(props);
        this.state = {
            formInputName: '',
            formInputEmail: '',
            formInputSubject: '',
            formInputCaptcha: '',
            formInputMessage: ''
        };
        this.handleNameChange = this.handleNameChange.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);
        this.handleEmailChange = this.handleEmailChange.bind(this);
        this.handleSubjectChange = this.handleSubjectChange.bind(this);
        this.handleCaptchaChange = this.handleCaptchaChange.bind(this);
        this.handleMessageChange = this.handleMessageChange.bind(this);
    }

    handlePropagation = (e) => {
        e.stopPropagation();
    };
    handleNameChange(e) {
        this.setState({ formInputName: e.target.value });
    }
    handleEmailChange(e) {
        this.setState({ formInputEmail: e.target.value });
    }
    handleSubjectChange(e) {
        this.setState({ formInputSubject: e.target.value });
    }
    handleCaptchaChange(e) {
        this.setState({ formInputCaptcha: e.target.value });
    }
    handleMessageChange(e) {
        this.setState({ formInputMessage: e.target.value });
    }

    handleFormSubmit(e) {
        e.preventDefault();

        const formPayload = {
            name: this.state.formInputName,
            email: this.state.formInputEmail,
            subject: this.state.formInputSubject,
            captcha: this.state.formInputCaptcha,
            message: this.formInputMessage
        };

        console.log('Send this in a POST request:', formPayload)
        /*this.handleClearForm(e);*/
    }

    render() {
        if (!this.props.show) {
            return null;
        }
        const isMobile = this.props.windowWidth <= 400;
        const contactUsPopCSS = isMobile ? ' contactUsPopUpMobile' : 'contactUsPopUp';
        return (
            <div className={'backdrop' + (this.props.show ? ' visible' : '')} onClick={this.props.onClose}>
                <div className={contactUsPopCSS + (this.props.show ? ' visible' : '')} onClick={this.handlePropagation}>
                    <Icon name={'remove'} onClick={this.props.onClose} className="contactUsPopUp-icon"/>
                    <div className="header-frame">
                        <h2>Kontakt vektor ved NTNU</h2>
                        <img src={message}/>
                    </div>
                    /*Different css-files for pc and mobile*/
                    /*TODO: Find out why unstackable doesn't work*/
                    <Form unstackable className="contactUsPopUp-form" onSubmit={this.handleFormSubmit}>
                        <Form.Group widths={2}>
                            <Form.Input
                                className="form-input"
                                name='name'
                                type='text'
                                value={this.state.formInputName}
                                onChange={this.handleNameChange}
                                placeholder='Ditt navn'
                            />
                            <Form.Input
                                className="form-input"
                                name='email'
                                type='text'
                                value={this.state.formInputEmail}
                                onChange={this.handleEmailChange}
                                placeholder='Din E-post'
                            />
                        </Form.Group>
                        <Form.Group widths={2}>
                            <Image className="contactUsPopUp-captchaImage" src={"http://via.placeholder.com/100x50/"}/>
                            <Form.Input
                                className="form-input"
                                name='captcha'
                                type='text'
                                value={this.state.formInputCaptcha}
                                onChange={this.handleCaptchaChange}
                                placeholder=''
                            />
                        </Form.Group>
                        <textarea
                            classname="form-input"
                            name='message'
                            rows={5}
                            value={this.state.formInputMessage}
                            onChange={this.handleMessageChange}
                            placeholder=''
                        />
                    </Form>
                </div>
            </div>
        )
    }
}

export default ContactUsPopUp;
