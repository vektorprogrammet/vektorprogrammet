import React from 'react';
import { Grid, List } from 'semantic-ui-react';

export default () => (
  <Grid columns={2}>
    <Grid.Column width={16}>
      <h2 className="centered">Hvordan blir jeg Vektorassistent?</h2>
    </Grid.Column>

    <Grid.Column mobile={16} tablet={8} computer={8} widescreen={8}  className="assistantListText1">

      <h3>Opptakskrav</h3>
      <List bulleted className="assistantPageListe">
        <List.Item>Du studerer på høgskole/universitet</List.Item>
        <List.Item>Du har hatt R1/S2 på videregående</List.Item>
        <List.Item>Du har tid til å dra til en ungdomsskole <br/>èn dag i uka (kl. 8-14)</List.Item>
      </List>
    </Grid.Column>

    <Grid.Column mobile={16} tablet={8} computer={8} widescreen={8} className="assistantListText2">
      <h3>Opptaksprosessen</h3>
      <List ordered className="assistantPageListe">
        <List.Item>Vektorprogrammet tar opp nye assistenter i starten av hvert semester</List.Item>
        <List.Item>Send inn søknad fra skjemaet lengre ned på denne siden</List.Item>
        <List.Item>Møt opp på intervju slik at vi kan bli bedre kjent med deg</List.Item>
        <List.Item>Dra på et gratis pedagogikkurs arrangert av Vektorprogrammet</List.Item>
        <List.Item>Få tildelt en ungdomsskole som du og din vektorpartner skal dra til</List.Item>
      </List>
    </Grid.Column>
  </Grid>
);
