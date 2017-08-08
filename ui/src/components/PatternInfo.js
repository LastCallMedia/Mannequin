
import React, {Component} from 'react';
import {Link} from 'react-router-dom';
import CodeFrame from './CodeFrame';
import PropTypes from 'prop-types';
import {PatternShape, VariantShape, UsedShape} from '../types';
import cx from 'classnames';
import './PatternInfo.css';

const PatternInfo = ({pattern, used, controls, variant, className}) => {
    const rawFormat = pattern.metadata.source_format || 'html';
    const theseControls = React.cloneElement(controls, {
        className: cx(controls.props.className, 'controls')
    });
    return (
        <div className={`PatternInfo ${className}`}>
            <div className="inner">
                {theseControls}
                <div className="info">
                    <h3>{pattern.name}</h3>
                    <div className="PatternInfoSection">
                        <label>Uses</label>
                        <p>{used.map(p => (
                            <Link key={p.id} to={`/pattern/${p.id}`}>{p.name}</Link>
                        ))}</p>
                    </div>
                    <div className="PatternInfoSection">
                        <label>Description</label>
                        <p>{pattern.metadata.description}</p>
                    </div>
                </div>
                <CodeToggleFrame className="code" html={variant && variant.source} raw={pattern.source} rawFormat={rawFormat} />
            </div>
        </div>
    )
}

PatternInfo.propTypes = {
    controls: PropTypes.node,
    pattern: PropTypes.shape(PatternShape),
    variant: PropTypes.shape(VariantShape),
    used: PropTypes.arrayOf(PropTypes.shape(UsedShape)),
}

export default PatternInfo;


class CodeToggleFrame extends Component {
    constructor(props) {
        super(props);
        this.state = {mode: 'html'}
        this.switchMode = this.switchMode.bind(this);
    }
    switchMode(e) {
        this.setState({mode: e.target.getAttribute('data-mode')})
    }
    render() {
        const {className} = this.props;
        const {mode} = this.state;
        const src = this.props[mode];
        const title = mode === 'html' ?  'View Html' : 'View Raw';
        const format = mode === 'html' ? 'html' : this.props.rawFormat;

        return (
            <div className={`CodeFrame ${className}`}>
                <CodeFrame frameBorder="0" title={title} src={src} format={format}></CodeFrame>
                <div className="button-group">
                    <a className="button" data-mode="html" onClick={this.switchMode}>HTML</a>
                    <a className="button" data-mode="raw" onClick={this.switchMode}>Raw</a>
                </div>
            </div>
        )
    }
}