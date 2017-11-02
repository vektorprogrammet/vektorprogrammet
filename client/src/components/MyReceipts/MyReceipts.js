import React, {Component} from 'react';
import {Table} from 'semantic-ui-react';

export default class MyReceipts extends Component {
    render() {
        return (
            <div>
                <h1>Mine Utlegg</h1>
                // Create new Receipt ability here later
                <Table basic>
                    <Table.Header>
                        <Table.Row>
                            <Table.HeaderCell>Id</Table.HeaderCell>
                            <Table.HeaderCell>Utleggdato</Table.HeaderCell>
                            <Table.HeaderCell>Beskrivelse</Table.HeaderCell>
                            <Table.HeaderCell>Sum</Table.HeaderCell>
                            <Table.HeaderCell>Kvittering</Table.HeaderCell>
                            <Table.HeaderCell>Status</Table.HeaderCell>
                        </Table.Row>
                    </Table.Header>
                    <Table.Body>
                        {this.props.receipts.map(function (receipt) {
                            return (
                                <Table.Row key={receipt.id}>
                                    <Table.Cell>{receipt.id}</Table.Cell>
                                    <Table.Cell>{receipt.receipt_date}</Table.Cell>
                                    <Table.Cell>{receipt.description}</Table.Cell>
                                    <Table.Cell>{receipt.sum}</Table.Cell>
                                    <Table.Cell>Vis kvittering</Table.Cell>
                                    <Table.Cell>{receipt.status}</Table.Cell>
                                </Table.Row>
                            )
                        })}
                    </Table.Body>
                </Table>
            </div>

        )
    }
}