var express = require('express');
var path = require('path');
var logger = require("./init/logger");
var mongoose   = require('mongoose');
var wwvappconfig = require('config').get('wwvapp');
//var passport = require('passport');

var db_connectStr='mongodb://'+
                  wwvappconfig.db.uid+':'+
                  wwvappconfig.db.pwd+'@'+
                  wwvappconfig.db.host+':'+
                  wwvappconfig.db.port+'/'+
                  wwvappconfig.db.dbname;

var db_connectStrT='mongodb://'+
                  wwvappconfig.db.test.uid+':'+
                  wwvappconfig.db.test.pwd+'@'+
                  wwvappconfig.db.test.host+':'+
                  wwvappconfig.db.test.port+'/'+
                  wwvappconfig.db.test.dbname;                  


//logger.info("connect string",db_connectStrT);
var db = mongoose.connect(db_connectStrT).connection; 


mongoose.connection.on("connected", function(ref) {
logger.info("connected to database "+ wwvappconfig.db.test.dbname+" on server "+  wwvappconfig.db.test.host);
var app = express();
var expconfig = require("./init");
logger.info("configuring express....");
 

//logger.info('NODE_CONFIG: ' + config.util.getEnv('NODE_CONFIG'));
//var nodeenv=config.util.getEnv('NODE_CO'NFIG');


expconfig.init(app, express,db);



// Auth Middleware - This will check if the token is valid
// Only the requests that start with /api/v1/* will be checked for the token.
// Any URL's that do not follow the below pattern should be avoided unless you 
// are sure that authentication is not needed




app.all('/api/v1/*', [require('./middlewares/validateRequest')]);

//app.use('/', function(req,res){  logger.info('path invoked' ,req.url);});

//app.use(passport.initialize());
// Initialize Passport
//var initPassport = require('./passport/init');
//initPassport(passport);

/* un comment the following function to see rawbody
app.use(function(req, res, next) {
  req.rawBody = '';
  //req.setEncoding('utf8');

  req.on('data', function(chunk) { 
    req.rawBody += chunk;
  });

  //console.log("rawBody",req.rawBody);
  next();
}); */

app.use('/', require('./routes'));

// If no route is matched by now, it must be a 404



app.use(function(req, res, next) {
  logger.error('route not found');
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});


var server = app.listen(app.get('port'), function() {
  console.log('Express server listening on port ' + server.address().port);
});

});


