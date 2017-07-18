
import React from 'react';

export const loadingWrapper = (waitProp, Wrapped) => {
    const C = (props) => {
        if(props[waitProp] !== undefined) {
            return <Wrapped {...props} />
        }
        return <span>Waiting</span>
    }
    C.displayName = `loading(${Wrapped.displayName})`;
    return C;
}