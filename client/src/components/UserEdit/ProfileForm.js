import React from 'react';
import { Field } from 'redux-form';

import { Form, Button } from 'semantic-ui-react';

const ProfileForm = ({onSubmit}) => (
  <Form onSubmit={onSubmit}>
    <Form.Group>
      <Form.Field width={16}>
        <label>Brukernavn</label>
        <Field
          component="input"
          placeholder="username"
          name="user_name"
          required
        />
      </Form.Field>
    </Form.Group>
    <Form.Group>
      <Form.Field width={16}>
        <label>Fornavn</label>
        <Field
          component="input"
          placeholder="fornavn"
          name="first_name"
          required
        />
      </Form.Field>
    </Form.Group>
    <Form.Group>
      <Form.Field width={16}>
        <label>Etternavn</label>
        <Field
          component="input"
          placeholder="etternavn"
          name="last_name"
          required
        />
      </Form.Field>
    </Form.Group>
    <Form.Group>
      <Form.Field width={16}>
        <label>E-post</label>
        <Field
          component="input"
          type="email"
          placeholder="e-post"
          name="email"
          required
        />
      </Form.Field>
    </Form.Group>
    <Form.Group>
      <Form.Field width={16}>
        <label>Telefon</label>
        <Field
          component="input"
          placeholder="Telefon"
          name="phone"
          required
        />
      </Form.Field>
    </Form.Group>
    <Form.Group>
      <Form.Field width={16}>
        <label>Kontonummer</label>
        <Field
          component="input"
          placeholder="Kontonummer"
          name="account_number"
        />
      </Form.Field>
    </Form.Group>
    <Button>Oppdater</Button>
  </Form>
);

export default ProfileForm;
