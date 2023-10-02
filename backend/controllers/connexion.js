const db = require('../db');
const brcypt = require('bcryptjs');
const authenticator = require('authenticator');
const jwt = require('jsonwebtoken');
const jwtFunctions = require('../jwt');
const dotenv = require('dotenv').config();
const logger = require('../winstonLogger');
const axios = require('axios');
const moment = require('moment');
const fonctionsMetiers = require('../fonctionsMetiers');


exports.alive = (req, res) => {
    res.json({message: "Backend serveur is alive"});
}