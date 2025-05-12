# base image
FROM node:18-alpine

# set working directory of the container at any given time
WORKDIR /var/www/frontend 

# copy package.json and package-lock.json from local to container
COPY frontend/package*.json ./ 
# install dependencies
RUN npm install 
# install serve globally
RUN npm install i -g serve 

# copy all files from local frontend directory to container
COPY frontend/ ./ 

# build the react app
RUN npm run build