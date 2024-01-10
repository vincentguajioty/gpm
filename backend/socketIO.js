const db = require('./db');
const logger = require('./winstonLogger');
const jwtFunctions = require('./jwt');
const fonctionsMetiers = require('./helpers/fonctionsMetiers');
const middlewaresFunctions = require('./helpers/middlewares');
const { instrument } = require("@socket.io/admin-ui");

const socketInterface = async (http) => {
    try {
        let config = await db.query(`
            SELECT
                *
            FROM
                CONFIG
        `);
        config = config[0];
        
        const socketIO = require('socket.io')(http, {
            cors: {
                origin: [process.env.CORS_ORIGINS, "https://admin.socket.io"],
                default: process.env.CORS_ORIGINS,
                methods: ["GET", "POST"],
                credentials: true,
                allowedHeaders: ["token"],
            },
        });

        if(process.env.SOCKET_IO_MONITOR == true)
        {
            logger.debug('SOCKETIO Monitor activé');
            instrument(socketIO, {
                auth: {
                    type: "basic",
                    username: process.env.SOCKET_IO_MONITOR_USER,
                    password: process.env.SOCKET_IO_MONITOR_PWD
                },
                namespaceName: process.env.SOCKET_IO_MONITOR_NAMESPACE
            });
        }

        socketIO.on('connection', (socket) => {
            logger.debug(`${socket.id} user just connected!`);
            
            socket.on('disconnect', () => {
                logger.debug('A user disconnected');
            });

            //---- Inventaires des lots ----
            socket.on('lot_inventaire_join', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['lots_lecture']);
                if(authResult == true)
                {
                    logger.debug('User dans inventaire lot ' + data);
                    socket.join(data);
                }
            });

            socket.on('lot_inventaire_update', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['lots_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    await fonctionsMetiers.updateInventaireLotItem(data);
                    socketIO.to('lot-'+data.idInventaire).emit("lot_inventaire_updateYourElement", data);
                }
            });
            
            socket.on('lot_inventaire_demandePopullationPrecedente', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['lots_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    await fonctionsMetiers.initiateOldValuesInventaireLot(data.idInventaire);
                    socketIO.to('lot-'+data.idInventaire).emit("lot_inventaire_reloadPage");
                }
            });

            socket.on('lot_inventaire_validate', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['lots_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    socketIO.to('lot-'+data.idInventaire).emit("lot_inventaire_validate");
                    await fonctionsMetiers.validerInventaireLot(data.idInventaire, data.commentaire);
                }
            });
            
            //---- Inventaires des réserves ----
            socket.on('reserve_inventaire_join', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['reserve_lecture']);
                if(authResult == true)
                {
                    logger.debug('User dans inventaire réserve ' + data);
                    socket.join(data);
                }
            });

            socket.on('reserve_inventaire_update', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['reserve_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    await fonctionsMetiers.updateInventaireReserveItem(data);
                    socketIO.to('reserve-'+data.idReserveInventaire).emit("reserve_inventaire_updateYourElement", data);
                }
            });
            
            socket.on('reserve_inventaire_demandePopullationPrecedente', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['reserve_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    await fonctionsMetiers.initiateOldValuesInventaireReserve(data.idReserveInventaire);
                    socketIO.to('reserve-'+data.idReserveInventaire).emit("reserve_inventaire_reloadPage");
                }
            });

            socket.on('reserve_inventaire_validate', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['reserve_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    socketIO.to('reserve-'+data.idReserveInventaire).emit("reserve_inventaire_validate");
                    await fonctionsMetiers.validerInventaireReserve(data.idReserveInventaire, data.commentaire);
                }
            });

            //---- Consommations espace publc ----
            if(config.consommation_benevoles == true)
            {
                socket.on('consommation_join_evenement', async (data) => {
                    logger.debug('User dans le suivi de conso ' + data);
                    socket.join(data);
                });

                socket.on('consommation_addElement', async (data) => {
                    let newElement = await fonctionsMetiers.ajouterItemConsommation(data);
                    socketIO.to('consommation-'+data.idConsommation).emit("consommation_addElement", newElement);
                })

                socket.on('consommation_updateElement', async (data) => {
                    let result = await fonctionsMetiers.updateItemConsommation(data);
                    socketIO.to('consommation-'+data.idConsommation).emit("consommation_updateElement", result);
                })

                socket.on('consommation_deleteElement', async (data) => {
                    await fonctionsMetiers.supprimerItemConsommation(data.idConsommationMateriel);
                    socketIO.to('consommation-'+data.idConsommation).emit("consommation_deleteElement", {idConsommationMateriel: data.idConsommationMateriel});
                })

                socket.on('consommation_terminerSaisie', async (data) => {
                    await fonctionsMetiers.terminerSaisieConsommation(data);
                    socketIO.to('consommation-'+data.idConsommation).emit("consommation_reloadPage");
                })

                socket.on('consommation_updateRecond', async (data) => {
                    let result = await fonctionsMetiers.updateReconditionnementConsommation(data);
                    socketIO.to('consommation-'+data.idConsommation).emit("consommation_updateElement", result);
                })

                socket.on('consommation_terminerReconditionnement', async (data) => {
                    await fonctionsMetiers.terminerReconditionnementConsommation(data);
                    socketIO.to('consommation-'+data.idConsommation).emit("consommation_reloadPage");
                })
            }
        });
    } catch (error) {
        logger.error(error)
    }
}

module.exports = {
    socketInterface,
}