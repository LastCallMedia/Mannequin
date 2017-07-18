
import React, {Component} from 'react';
import Highlight from 'react-syntax-highlight';
import 'highlight.js/styles/default.css';
import 'highlight.js/styles/atom-one-dark.css';

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
        return <Highlight lang={format} value={code} />
    }
}

export default CodeFrame;