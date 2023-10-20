const mariadb = require('mariadb');
const dotenv = require('dotenv').config();
const logger = require('./winstonLogger');

const db = mariadb.createPool({
    host: process.env.DB_HOST,
    port: process.env.DB_PORT,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    checkDuplicate: false,
    supportBigNumbers: true,
    multipleStatements: true,
    namedPlaceholders: true,
    dateStrings: [
        'DATE',
        'DATETIME'
    ],
});

module.exports = db;