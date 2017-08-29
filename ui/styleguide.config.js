var path = require('path');

module.exports = {
    styleguideComponents: {
        Wrapper: path.join(__dirname + '/src/StyleGuideWrapper')
    },
    resolver: require('react-docgen').resolver.findAllComponentDefinitions
}