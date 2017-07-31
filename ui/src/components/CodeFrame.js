
import React, {Component} from 'react';

import SyntaxHighlighter, { registerLanguage } from "react-syntax-highlighter/dist/light";
import twig from "react-syntax-highlighter/dist/languages/twig";
import xml from "react-syntax-highlighter/dist/languages/xml";
import codeStyle from 'react-syntax-highlighter/dist/styles/foundation';

registerLanguage('twig', twig);
registerLanguage('html', xml);

class CodeFrame extends Component {
    constructor(props) {
        super(props)
        this.state = {code: ''}
    }
    componentDidMount() {
        this.fetch();
    }
    componentDidUpdate(prevProps) {
        if(prevProps.src !== this.props.src) {
            this.fetch();
        }
    }
    fetch() {
        this.setState({loading: true, err: false});
        fetch(this.props.src)
            .then(res => {
                this.setState({loading: false});
                res.text().then(code => this.setState({code}));
            })
            .catch(err => {
                this.setState({loading: false, err: err})
            })
    }
    render() {
        const {code, loading, err} = this.state;
        const {format} = this.props;

        if(loading) {
            return <p>Loading...</p>
        }
        if(err) {
            return <p>Error: {err}</p>
        }
        return <SyntaxHighlighter style={codeStyle} showLineNumbers={true} language={format}>{code}</SyntaxHighlighter>
    }
}

export default CodeFrame;