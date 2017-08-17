Example:
```js
<Menu tree={[
    {
        name: 'Link with children',
        to: '#',
        children: [
            {
                name: 'Child 1',
                to: '#',
                children: [
                    {name: 'Child of child 1', to: '#'}
                ]
            }
        ]
    }
]} />
```