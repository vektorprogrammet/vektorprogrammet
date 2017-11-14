import React from 'react';
import { Button, Form, Header } from 'semantic-ui-react';
import { Field } from 'redux-form';

//TODO: han en "onSubmit" som snakker med state
export default () => {

    return (
        <div>
            <Header as='h3'>Abonner på vårt nyhetsbrev!</Header>
            <Form>
            <Form.Group widths='equal'>
            <Form.Field>
                <Field
                    name="firstName"
                    component="input"
                    type="text"
                    placeholder="Fornavn"
                    required/>
                <Field
                    name="email"
                    component="input"
                    placeholder='E-post'
                    type="email"
                    required/>
            </Form.Field>
            </Form.Group>


            <Form.Field><Button>Registrer</Button></Form.Field>
            </Form>
        </div>
    );
};