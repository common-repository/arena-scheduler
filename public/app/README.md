# Arena Scheduler

Arena Scheduler is a custom WordPress plugin that integrates with React for a dynamic scheduling application.

## Available Scripts

In the project directory, you can run:

### `npm start`

Runs the app in development mode.\
Open [http://localhost:3000](http://localhost:3000) to view it in your browser.

The page will reload when you make changes.\
You may also see any lint errors in the console.

### `npm run build`

Builds the app for production to the `build` folder.\
It correctly bundles React in production mode and optimizes the build for the best performance.

The build is minified, and the filenames include the hashes.\
Your app is ready to be deployed!

### Additional Setup

#### 1. Create a .gitignore File

To avoid committing unnecessary files to your repository, create a .gitignore file in the root directory of the project. Add the following lines to the .gitignore file:

`build
node_modules
.env
.DS_Store
package-lock.json`

#### 2. Create a .env File

Before running the development server or building the project, create a `.env` file in the root directory of the project. In this file, add the following environment variable:

`REACT_APP_SERVER_HOST=index.php?rest_route=/custom/v1/`
