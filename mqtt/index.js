const crea = require('@creativechain-fdn/crea-js');
const aedes = require('aedes');
const dotenv = require('dotenv');
const { createServer } = require('net');

//Load .env file
dotenv.config();
let apiOptions = {
    nodes: ['https://nodes.creary.net'],
    addressPrefix: 'CREA',
    symbol: {
        CREA: 'CREA',
        CGY: 'CGY',
        CBD: 'CBD',
        VESTS: 'VESTS'
    },
    nai: {
        CREA: '@@000000021',
        CBD: '@@000000013',
        VESTS: '@@000000037',
        CGY: '@@000000005'
    },
    chainId: '0000000000000000000000000000000000000000000000000000000000000000'
};
crea.api.setOptions(apiOptions);
crea.config.set('address_prefix', apiOptions.addressPrefix);
crea.config.set('chain_id', apiOptions.chainId);

const port = process.env.MQTT_PORT;
const mqttServer = aedes();
const DEFAULT_ROLES = ['owner', 'active', 'posting', 'memo'];

mqttServer.authenticate = function(client, username, password, callback) {
    console.log('Trying authentication', username, password);
    crea.api.getState('@' + username, function (err, result) {
        if (err) {
            callback(err);
        } else {

            let accountData = result;

            if (accountData.accounts[username]) {
                let account = accountData.accounts[username];

                console.log(password.length, password.toString('hex'), password.toString());
                let signature = crea.crypto.Signature.fromBuffer(password);
                for (let role in DEFAULT_ROLES) {
                    let authRole = account['role'];
                    let pubKey = null;
                    if (role === 'memo') {
                        pubKey = authRole;
                    } else {
                        pubKey = authRole.key_auths[0][0];
                    }

                    let pk = crea.crypto.PublicKey.fromString(pubKey);
                    if (signature.verifyBuffer(Buffer.from(username, 'utf8'), pk)) {
                        callback(null, true);
                        return;
                    }
                }

                //Can not verify user
                callback(null, false);
            } else {
                //User not exists
                callback(null, false);
            }

        }
    });
};

mqttServer.on('client', function (client) {
    console.log('New Client connecting', client.id);
});

mqttServer.on('clientReady', function (client) {
    console.log('Client is ready', client.id);
});

mqttServer.on('clientDisconnect', function (client) {
    console.log('Client disconnected', client.id);
});

mqttServer.on('clientError', function (client, error) {
    console.log('Client error', client.id, error);
});

mqttServer.on('closed', function (client) {
    console.log('Server closed', client);
});



const netServer = createServer(mqttServer.handle);
netServer.listen(port, function () {
    console.log('MQTT Server listening on port', port)
});
