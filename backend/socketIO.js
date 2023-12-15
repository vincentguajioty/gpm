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

            socket.on('join_inventaire_lot', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['lots_lecture']);
                if(authResult == true)
                {
                    logger.debug('User dans inventaire lot ' + data);
                    socket.join(data);
                }
            });

            socket.on('inventaireLotUpdate', async (data) => {
                let authResult = await jwtFunctions.verifyJWTforSocketIO(socket.handshake.headers.token, ['lots_modification']);
                if(authResult == true)
                {
                    logger.debug(data);
                    await fonctionsMetiers.updateInventaireLotItem(data);
                    socket.to('lot-'+data.idInventaire).emit("updateYourElement", data);
                }
            });
            
            socket.on('demandePopullationPrecedente', async (data) => {
                logger.debug(data);
                socket.to('lot-'+data.idInventaire).emit("demandePopullationPrecedente", data.demandePopullationPrecedente);
            });

            socket.on('inventaireLotValidate', async (data) => {
                logger.debug(data);
                socket.to('lot-'+data.idInventaire).emit("inventaireLotValidate");
                await fonctionsMetiers.validerInventaireLot(data.idInventaire, data.commentaire);
            });
            
            socket.on('disconnect', () => {
              logger.debug('A user disconnected');
            });
        });
    } catch (error) {
        logger.error(error)
    }
}

module.exports = {
    socketInterface,
}