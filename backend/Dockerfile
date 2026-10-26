FROM node:22-alpine

WORKDIR /backend

COPY package.json /backend

RUN npm install && npm cache clean --force

COPY --chown=node:node . /backend

USER node

EXPOSE 3001

CMD ["node","index.js"]!