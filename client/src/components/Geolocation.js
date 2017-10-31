import React, {Component} from 'react';


class Geolocation extends Component {

    constructor(props){
        super(props)
        this.state = {
            ntnu : {
                latitude: '63.2510',
                longitude: '10.249'
            },

            uit : {
                latitude: '69.3907',
                longitude: '18.5712'
            },

            crd : {
                latitude: '',
                longitude: ''
            },

            closestDepartment : ''
        };
        this.handleClosestDepartment = this.handleClosestDepartment.bind(this);
    }

    async componentDidMount() {
        await this.geoFindLocation();
        await this.findClosestDepartment();
        await this.handleClosestDepartment();
    }

    geoFindLocation() {
        navigator.geolocation.getCurrentPosition(this.success, this.error);
    }

    success = (position) => {
        const crd = position.coords;
        this.setState({crd: {latitude: crd.latitude, longitude: crd.longitude}});
    };

    error = (err) => {
        console.warn(`ERROR(${err.code}): ${err.message}`);
    };

    findClosestDepartment(){
        const state = this.state;
        if (this.getDistanceFromLatLonInKm(state.crd.latitude, state.crd.longitude, state.ntnu.latitude, state.ntnu.longitude) <=
        this.getDistanceFromLatLonInKm(state.crd.latitude, state.crd.longitude,state.uit.latitude, state.uit.longitude)){
            this.setState({closestDepartment: 0}); //ntnu
        } else {
            this.setState({closestDepartment: 3}); // uit
        }
    }

    getDistanceFromLatLonInKm = (lat1,lon1,lat2,lon2) => {
        const R = 6371; // Radius of the earth in km
        const dLat = this.deg2rad(lat2-lat1);  // convert to radians below
        const dLon = this.deg2rad(lon2-lon1);
        const a =
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) *
                Math.sin(dLon/2) * Math.sin(dLon/2)
            ;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const d = R * c; // Distance in km
        return d;
    };

    deg2rad = (deg) => {
        return deg * (Math.PI/180)
    };

    handleClosestDepartment(){
        this.props.closestDepartment(this.state.closestDepartment);
    }

    render() {
        return (
            <div onLoad={this.handleClosestDepartment}></div>
        )
    }
}

export default Geolocation;
