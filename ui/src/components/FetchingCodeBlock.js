import React, { Component } from 'react';
import CodeBlock from './CodeBlock';
import Callout from './Callout';
import { Loading } from './Icons';
import PropTypes from 'prop-types';
// import './FetchingCodeBlock.css';

class FetchingCodeBlock extends Component {
  constructor(props) {
    super(props);
    this.state = { code: '', loading: false, error: null };
  }
  componentDidMount() {
    this.fetch();
  }
  componentDidUpdate(prevProps) {
    if (prevProps.src !== this.props.src) {
      this.fetch();
    }
  }
  fetch() {
    this.setState({ loading: true });
    this.props
      .fetch(this.props.src)
      .then(code => this.setState({ code, loading: false, error: null }))
      .catch(error => this.setState({ error, loading: false, code: '' }));
  }
  render() {
    const { code, loading, error } = this.state;
    const { language } = this.props;

    return (
      <div
        className={loading ? 'FetchingCodeBlock loading' : 'FetchingCodeBlock'}
      >
        {error && (
          <Callout
            type="alert"
            title="Error fetching code"
            content={<p>{error.message}</p>}
          />
        )}
        {loading && <Loading />}
        {!error && <CodeBlock language={language}>{code}</CodeBlock>}
      </div>
    );
  }
}
FetchingCodeBlock.propTypes = {
  src: PropTypes.string.isRequired,
  language: PropTypes.string.isRequired,
  fetch: PropTypes.func.isRequired
};
FetchingCodeBlock.defaultProps = {
  fetch: src =>
    fetch(src, { credentials: 'same-origin' }).then(res => {
      if (res.ok) return res.text();
      throw new Error(res.statusText);
    }),
  language: 'html'
};

export default FetchingCodeBlock;
