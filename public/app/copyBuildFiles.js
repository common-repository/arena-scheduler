const fs = require('fs-extra');
const path = require('path');

const source = path.join(__dirname, 'build');
const destination = path.join(__dirname, '../build');

fs.copy(source, destination, err => {
    if (err) {
        console.error('Error copying build files:', err);
    } else {
        console.log('Build files copied successfully!');
    }
});
