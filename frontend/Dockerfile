FROM node:16-alpine as build-stage
RUN npm install npm@8.5.0 -g
WORKDIR /frontend
COPY package*.json .
RUN npm install
COPY . .
ENV REACT_APP_NAME="GPM"
ENV REACT_APP_BACKEND_URL="https://gpm.mondomaine.fr"
ENV REACT_APP_RECAPTCHA_ENABLED="0"
ENV REACT_APP_RECAPTCHA_SITEKEY=""
RUN npm run build

FROM nginx:1.22
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build-stage /frontend/build /usr/share/nginx/html