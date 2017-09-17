import React, { Component } from 'react';
import './SponsorList.css';

export default class SponsorList extends Component {
  render() {
    const sponsors = this.props.sponsors || [];
    const sponsorItems = sponsors.map(sponsor => (
        <li key={sponsor.id}><a href={sponsor.url}>{sponsor.name}</a></li>
    ));

    return (
        <ul>{sponsorItems}</ul>
    );
  }
}
