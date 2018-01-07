
var app = require('express')();
var server = require('http').Server(app);

const crypto = require('crypto'),
  fs = require("fs"),
  http = require("http");
var https = require('https');

var privateKey = fs.readFileSync('/etc/letsencrypt/live/app.WooCabs.com/privkey.pem').toString();
var certificate = fs.readFileSync('/etc/letsencrypt/live/app.WooCabs.com/cert.pem').toString();

// var credentials = crypto.createCredentials({key: privateKey, cert: certificate});
// var server = http.createServer(app);
// server.setSecure(credentials);

var options = {
  key: privateKey,
  cert: certificate
};

var server = https.createServer(options, app).listen(3000);

var io = require('socket.io')(server);
var debug = require('debug')('Uber:Chat');
var request = require('request');
var port = process.env.PORT || '3000';

process.env.DEBUG = '*';
// process.env.DEBUG = '*,-express*,-engine*,-send,-*parser';

server.listen(port);

io.on('connection', function (socket) {

    console.log('new connection established', socket.handshake.query.myid);

    socket.reqid = socket.handshake.query.reqid;
    
    socket.join(socket.handshake.query.myid);

    socket.emit('connected', 'Connection to server established!');

    socket.on('update sender', function(data) {
        console.log('update sender', data);
        socket.handshake.query.myid = data.myid;
        socket.handshake.query.reqid = data.reqid;
        socket.reqid = socket.handshake.query.reqid;
        socket.join(socket.handshake.query.myid);
        socket.emit('sender updated', 'Sender Updated ID:'+data.reqid, 'Request ID:'+data.myid);
    });

    socket.on('send message', function(data) {

        if(data.type == 'up') {
            receiver = 'pu' + data.provider_id;
        } else {
            receiver = 'up' + data.user_id;
        }

        console.log('data', data);
        console.log('receiver', receiver);

        socket.broadcast.to( receiver ).emit('message', data);

        //url = 'http://localhost:8000/chat/save?user_id='+data.user_id
        url = 'https://app.WooCabs.com/chat/save?user_id='+data.user_id
        +'&provider_id='+data.provider_id
        +'&message='+data.message
        +'&type='+data.type
        +'&request_id='+data.request_id;

        console.log(url);

        request(url, function (error, response, body) {
            if (!error && response.statusCode == 200) {
                // console.log(body); // Show the HTML for the Google homepage. 
            }
        });
    });

    socket.on('disconnect', function(data) {
        console.log('disconnect', data);
    });
});