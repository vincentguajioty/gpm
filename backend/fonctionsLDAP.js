const db = require('./db');
const ldap = require('ldapjs');
const dotenv = require('dotenv').config();
const logger = require('./winstonLogger');