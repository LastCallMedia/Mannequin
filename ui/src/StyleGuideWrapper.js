
import React, {Component} from 'react';
import {StaticRouter} from 'react-router-dom';
import './App.css';

export default class Wrapper extends Component {
    render() {
        const context = {};
        return (
            <StaticRouter context={context}>
                {this.props.children}
            </StaticRouter>
        )
    }
}