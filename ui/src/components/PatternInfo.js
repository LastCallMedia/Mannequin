import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import FetchingCodeBlock from './FetchingCodeBlock';
import PropTypes from 'prop-types';
import cx from 'classnames';
import './PatternInfo.css';

const PatternInfo = ({ pattern, variant, used, className, controls }) => {
  return (
    <div className={cx('PatternInfo', className)}>
      <div className="inner">
        <div className="controls">
          {controls}
        </div>
        <PatternInfoInfo
          className="info"
          pattern={pattern}
          used={used}
          variant={variant}
        />
        <PatternInfoCode className="code" pattern={pattern} variant={variant} />
      </div>
    </div>
  );
};
PatternInfo.propTypes = {
  pattern: PropTypes.shape({}),
  variant: PropTypes.shape({}),
  used: PropTypes.array
};

export default PatternInfo;

const PatternInfoInfo = ({ pattern, variant, used }) => {
  return (
    <div className="info">
      <h3 className="pattern-name">
        {pattern.name}
      </h3>
      {pattern.metadata.description &&
        <PatternInfoSection title="Description">
          {pattern.metadata.description}
        </PatternInfoSection>}
      {used.length > 0 &&
        <PatternInfoSection title="Used">
          {used.map(p =>
            <Link key={p.id} to={`/pattern/${p.id}`}>
              {p.name}
            </Link>
          )}
        </PatternInfoSection>}
    </div>
  );
};
PatternInfoInfo.propTypes = {
  used: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string,
      name: PropTypes.string
    })
  ),
  pattern: PropTypes.shape({
    name: PropTypes.string.isRequired,
    metadata: PropTypes.shape({
      description: PropTypes.string
    })
  }).isRequired
};
PatternInfoInfo.defaultProps = {
  used: []
};

const PatternInfoSection = ({ title, children }) =>
  <section>
    <h4>
      {title}
    </h4>
    <p>
      {children}
    </p>
  </section>;
PatternInfoSection.propTypes = {
  title: PropTypes.string.isRequired,
  children: PropTypes.node.isRequired
};

class PatternInfoCode extends Component {
  constructor(props) {
    super(props);
    this.state = { language: null, src: null };
    this.switchMode = this.switchMode.bind(this);
  }
  componentDidMount() {
    this.resetSource();
  }
  componentDidUpdate(prevProps) {
    if (
      prevProps.variant !== this.props.variant ||
      prevProps.pattern !== this.props.pattern
    ) {
      this.resetSource();
    }
  }
  resetSource() {
    let state = { language: null, src: null };
    const { variant, pattern } = this.props;
    if (variant) {
      state.language = 'html';
      state.src = variant.source;
    } else if (pattern) {
      state.language = pattern.metadata.source_format;
      state.src = pattern.source;
    }
    this.setState(state);
  }
  switchMode(e) {
    this.setState({
      language: e.target.getAttribute('data-language'),
      src: e.target.getAttribute('data-src')
    });
  }
  render() {
    const { src, language } = this.state;
    const { variant, pattern } = this.props;

    return (
      <div className="code">
        {src && <FetchingCodeBlock src={src} language={language} />}
        <div className="button-group">
          {variant &&
            <a
              className={cx(
                'button',
                src === variant.source ? 'primary' : 'secondary'
              )}
              onClick={this.switchMode}
              data-src={variant.source}
              data-language={'html'}
            >
              HTML
            </a>}
          {pattern &&
            <a
              className={cx(
                'button',
                src === pattern.source ? 'primary' : 'secondary'
              )}
              onClick={this.switchMode}
              data-src={pattern.source}
              data-language={pattern.metadata.source_format}
            >
              Raw
            </a>}
        </div>
      </div>
    );
  }
}
PatternInfoCode.propTypes = {
  variant: PropTypes.shape({
    source: PropTypes.string.isRequired
  }),
  pattern: PropTypes.shape({
    source: PropTypes.string.isRequired,
    metadata: PropTypes.shape({
      source_format: PropTypes.string
    })
  })
};
