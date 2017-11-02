import React, {Component} from 'react';
import {ReceiptApi} from '../../api/ReceiptApi';
import MyReceipts from './MyReceipts';

export default class MyReceiptsContainer extends Component {
    state = {receipts: [] };

    async componentDidMount() {
        this.setState({ receipts: await ReceiptApi.getAll() })
    }

    render() {
        return <MyReceipts receipts={this.state.receipts}/>
    }
}