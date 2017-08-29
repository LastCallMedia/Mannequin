Default:
```js
<FetchingCodeBlock src="" fetch={() => Promise.resolve('This is code that has been fetched.')} />
```
Loading:
```js
<FetchingCodeBlock src="" fetch={() => new Promise(() => {})} />
```
Error:
```js
<FetchingCodeBlock src="" fetch={() => Promise.reject(new Error('You ran out of link juice!'))} />
```