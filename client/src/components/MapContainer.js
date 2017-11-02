import React, {Component} from 'react';

import {Map, InfoWindow, Marker, GoogleApiWrapper} from 'google-maps-react';

export class MapContainer extends Component {
    render() {
        return (
            <Map google={this.props.google}
                 initialCenter={{
                     lat: 63.4195014,
                     lng: 10.40195
                 }}
                 zoom={14}
            >
                <Marker name={'Current location'} />
            </Map>
        );
    }
}
export default GoogleApiWrapper({
    apiKey: ("AIzaSyAyesbQMyKVVbBgKVi2g6VX7mop2z96jBo")
})(MapContainer)
