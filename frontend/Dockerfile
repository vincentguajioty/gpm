FROM node:22-alpine AS build-stage
WORKDIR /frontend
COPY package*.json .
RUN npm install
COPY . .
RUN npm run build

FROM nginx:1.27.4
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build-stage /frontend/build /usr/share/nginx/html