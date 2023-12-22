// const { format, createLogger, transports } = require('winston');
// const { timestamp, combine, errors, json } = format;

// const logger = createLogger({
//     level: process.env.LOG_LEVEL,
//     format: combine(
//         timestamp(),
//         errors({stack:true}),
//         json(),
//     ),
//     transports: [
//         new transports.Console(),
//         new transports.File({
//             filename: 'logs/logs.log',
//             maxsize: 5242880,
//             maxFiles: 5,
//         }),
//     ],
// });

// module.exports = logger;

const { format, createLogger, transports } = require('winston');
const { combine, colorize, timestamp, align, printf, json, errors } = format;

const DailyRotateFile = require("winston-daily-rotate-file");

const logFormat = combine(
    colorize(),
    timestamp(),
    json(),
    errors({stack:true}),
    printf(
        info => `[${info.timestamp}][${info.level}][${info.idPersonne > 0 ? 'idPersonne: '+info.idPersonne : 'SYSTEM'}][${info.identifiant ? 'identifiant: '+info.identifiant : 'SYSTEM'}][delegation: ${info.tokenDelegationActive && info.tokenDelegationActive == true ? true : false}][userInitial: ${info.tokenDelegationActive && info.tokenDelegationActive == true ? info.tokenDelegationInitialIdPersonne+' '+info.tokenDelegationInitialIdentifiant : 'N/A'}] ${info.message}${info.backupBeforeDrop ? ' '+JSON.stringify(info.backupBeforeDrop) : ''}`,
    ),
);

const transport = new DailyRotateFile({
    filename: 'logs/%DATE%.log',
    datePattern: 'YYYY-MM-DD',
    zippedArchive: false,
    maxSize: '20m',
    maxFiles: '365d',
    prepend: true,
    level: process.env.LOG_LEVEL,
});

transport.on('rotate', function (oldFilename, newFilename) {
// call function like upload to s3 or on cloud
});

const logger = createLogger({
    format: logFormat,
    transports: [
        transport,
        new transports.Console({
            level: process.env.LOG_LEVEL,
        }),
    ]
});


module.exports = logger;