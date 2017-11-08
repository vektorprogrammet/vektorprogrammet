import React from 'react';
import Feature from './Feature';
import { Grid } from 'semantic-ui-react';

import certificate from '../../images/certificate.svg';
import calendar from '../../images/calendar.svg';
import graduation from '../../images/graduation.svg';

export default () => (
  <div className="large container assistant-page-features">
    <Grid columns={3} padded>
      <Feature
        img={certificate}
        alt={'Verv på CVen'}
        header={'Fint å ha på CVen'}
        content={'Erfaring som arbeidsgivere setter pris på. Alle assistenter får en attest.'}
      />
      <Feature
        img={calendar}
        header={'Sosiale arrangementer'}
        content={'Alle assistenter blir invitert til arrangementer som fester, populærforedrag, bowling, grilling i parken, gokart og paintball.'}
      />
      <Feature
        img={graduation}
        header={'Vær et forbilde'}
        content={'Her kommer en liten tekst om at vektorassistenter er superhelter i matteundervisningen.'}
      />
    </Grid>
  </div>
)
