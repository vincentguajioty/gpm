const logger = require('./winstonLogger');
const jwtFunctions = require('./jwt');
const fonctionsMetiers = require('./helpers/fonctionsMetiers');

const socketInterface = async (http) => {
    try {
        const socketIO = require('socket.io')(http, {
            cors: {
                origin: [process.env.CORS_ORIGINS],
                methods: ["GET", "POST"],
                credentials: true,
                allowedHeaders: ["token"],
            },
        });

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
                    socket.to('lot-'+data.idInventaire).emit("lot_inventaire_updateYourElement", data);
                }
            });
            
            socket.on('lot_inventaire_demandePopullationPrecedente', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['lots_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    socket.to('lot-'+data.idInventaire).emit("lot_inventaire_demandePopullationPrecedente", data.demandePopullationPrecedente);
                }
            });

            socket.on('lot_inventaire_validate', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['lots_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    socket.to('lot-'+data.idInventaire).emit("lot_inventaire_validate");
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
                    socket.to('reserve-'+data.idReserveInventaire).emit("reserve_inventaire_updateYourElement", data);
                }
            });
            
            socket.on('reserve_inventaire_demandePopullationPrecedente', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['reserve_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    socket.to('reserve-'+data.idReserveInventaire).emit("reserve_inventaire_demandePopullationPrecedente", data.demandePopullationPrecedente);
                }
            });

            socket.on('reserve_inventaire_validate', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['reserve_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    socket.to('reserve-'+data.idReserveInventaire).emit("reserve_inventaire_validate");
                    await fonctionsMetiers.validerInventaireReserve(data.idReserveInventaire, data.commentaire);
                }
            });
        });
    } catch (error) {
        logger.error(error)
    }
}

module.exports = {
    socketInterface,
}