import React from 'react';
import teacher from '../../images/Teacher.svg';

const style = {
  width: '500px',
  maxWidth: '100%',
  display: 'block',
  margin: '50px auto'
};

export default () => (
  <div>
    <h2 className="centered">Læringsassistent i matematikk</h2>
    <p>Vektorprogrammet er en studentorganisasjon som sender realfagssterke studenter til grunnskolen for å
      hjelpe elevene med matematikk i skoletiden. Vi ser etter deg som lengter etter en mulighet til å lære
      bort kunnskapene du sitter med og ønsker å ta del i et sterkt sosialt fellesskap. Etter å ha vært
      vektorassistent kommer du til å sitte igjen med mange gode erfaringer og nye venner på tvert av trinn og
      linje.
    </p>
    <img src={teacher} style={style} alt="Vektorsassistenter sammen med lærer"/>
    <p>I tillegg vil du få muligheten til å delta på mange sosiale arrangementer, alt fra fest og
      grilling til spillkveld. Samtidig arrangerer vi populærforedrag som er til for å øke motivasjonen din
      for videre studier. Vi har hatt besøk av blant annet Andreas Wahl, Jo Røislien, Knut Jørgen Røed
      Ødegaard og James Grime.
    </p>
  </div>
)
