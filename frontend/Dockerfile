FROM node:16-alpine AS build-stage
RUN npm install npm@8.5.0 -g
WORKDIR /frontend
COPY package*.json .
RUN npm install
COPY . .
RUN npm run build

FROM nginx:1.22
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build-stage /frontend/build /usr/share/nginx/html