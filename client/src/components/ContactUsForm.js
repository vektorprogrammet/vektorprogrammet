import React, {Component} from 'react';
import { Button, Checkbox, Form } from 'semantic-ui-react';

class ContactUsForm extends Component {
    render() {
        return (
            <Form>
                <Form.Field width={8}>
                    <input placeholder='Ola Nordmann'/>
                </Form.Field>
                <Form.Field width={8}>
                    <input placeholder='E-post' />
                </Form.Field>
                <Form.TextArea placeholder='Message' width={8}/>
                <Button primary type='submit'>Submit</Button>
            </Form>
        );
    }
}

export default ContactUsForm;