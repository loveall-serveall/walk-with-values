/*exports.index = function(req, res){
  //res.render('index');
   res.redirect('/list');
};  */

var socketio = require('./socketio');

module.exports = function(io){


 var socketio = require('./socketio')(io);
  

  var routes = {};
  routes.index = function (req, res) { 
  	console.log('reached to index route for sure...')
  	io.on('connection', function (socket) {
  	console.log('a user connected');
    //socket.emit('news', { hello: 'world' });
   // socket.on('my other event', function (data) {
   //   console.log(data);
   // });
     //io.emit('chat message', "Hello World");
    });
  	res.redirect('/list');
  };

  routes.list = socketio.list;
   routes.list2 = socketio.list2;
  return routes;
} 