const dotenv = require('dotenv').config();

const { format, createLogger, transports } = require('winston');
const { timestamp, combine, errors, json } = format;
const LEVEL = Symbol.for('level');

const logger = createLogger({
    level: process.env.LOG_LEVEL,
    format: combine(
        timestamp(),
        errors({stack:true}),
        json(),
    ),
    transports: [
        new transports.Console(),
        new transports.File({
            filename: 'logs/logs.log',
            maxsize: 5242880,
            maxFiles: 5,
        }),
    ],
});

module.exports = logger;