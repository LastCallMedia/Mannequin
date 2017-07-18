
import PropTypes from 'prop-types';

/**
 * Common Shapes for use on Component.propTypes.
 */
export const VariantShape = {
    id: PropTypes.string,
    name: PropTypes.string,
    description: PropTypes.string,
    rendered: PropTypes.string.isRequired
}
export const PatternShape = {
    name: PropTypes.string.isRequired,
    description: PropTypes.string,
    rendered: PropTypes.string,
    used: PropTypes.arrayOf(PropTypes.string),
    variants: PropTypes.arrayOf(PropTypes.shape(VariantShape)),
    tags: PropTypes.shape(),
};

export const UsedShape = {
    name: PropTypes.string.isRequired,
    id: PropTypes.string.isRequired
}