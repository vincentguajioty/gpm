FROM node:16-alpine

WORKDIR /backend

COPY package.json /backend

RUN npm install

COPY . /backend

EXPOSE 3001

CMD ["node","index.js"]!