(function (expressConfig) {
  var logger = require("./logger");
  var path = require('path');
 var wwvappconfig = require('config').get('wwvapp');
  


//mongodb://[username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]][/[database][?options]]

//var dburl='mongodb://'+process.env.MG_USR+":"+process.env.MG_PWD+process.env.MG_HOST+":"+process.env.MG_PORT+"/PIF?authSource"+process.env.MG_ATHSRC;



  expressConfig.init = function (app, express,db) {

  //  app.use(require('express'));
    api = express.Router();

    api.use(clientErrorHandler);

    function clientErrorHandler(err, req, res, next) {
      logger.log("error","Something wrong with an XHR request",err.stack);

      if (req.xhr) {
        res.send(500, { error: 'Something blew up!' });
      } else {
         next(err);
      }
    }


//    logger.debug("Setting parse urlencoded request bodies into req.body.");
    var bodyParser = require('body-parser');
     app.use(bodyParser.json());
    app.use(require('morgan')('combined',{ "stream": logger.stream }));



app.all('*', function(req, res, next) {
   req.db=db;
  // CORS headers
  res.header("Access-Control-Allow-Origin", "*"); // restrict it to the required domain
  res.header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS');
  // Set custom headers for CORS
  res.header('Access-Control-Allow-Headers', 'Content-type,Accept,X-Access-Token,X-Key');
  res.header('Content-Type', 'application/json');
  
  if (req.method == 'OPTIONS') {
    res.status(200).end();
  } else {
    next();
  }
});



// Start the server
 var port = process.env.PORT || wwvappconfig.server.port;
   if(process.argv.indexOf("-p") != -1){ //does our flag exist?
    port = process.argv[process.argv.indexOf("-p") + 1]; //grab the next item
    console.log('port from args = ',port);
}
   app.set('port', port);
  };


})(module.exports);
