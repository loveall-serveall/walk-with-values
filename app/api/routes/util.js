var logger = require("../init/logger");
var _ = require('underscore');

function getNextSequence(db ,name, callback){
    logger.info('inside getNextSequence. name',name);
       // = db.collection('counters');  

       db.collection('counters').findAndModify(
                 { _id: name },{},
                 { $inc: {seq: 1 }},
                 {new: true},
                 callback);

    };

    // read sadhanas from a file..

function readJSONFile(filename, callback) {
  require("fs").readFile(filename, function (err, data) {
    if(err) {
      callback(err);
      return;
    }
    try {
      callback(null, JSON.parse(data));
    } catch(exception) {
      callback(exception);
    }
  });
};

function getSadhanas(callback){

  readJSONFile("config/sadhanas.json", callback);

};




var utils = {
   
 encrypt: function(text){
  var cipher = crypto.createCipher(algorithm,password+'wwv')
  var crypted = cipher.update(text,'utf8','hex')
  crypted += cipher.final('hex');
  return crypted;
},
 
decrypt: function(text){
  var decipher = crypto.createDecipher(algorithm,password+'wwv')
//  logger.info("trying to decipher",text);
  var dec = decipher.update(text,'hex','utf8')
  dec += decipher.final('utf8');
  return dec;
},
   getNextSequence: function (db ,name, callback){
      getNextSequence(db,name,callback);
   },
   getSadhanas: function(callback){
    getSadhanas(callback);
   },
   
  /* getSadhanas: function(callback){
    getSadhanas(function(err,data){
      data = _.omit(_.map(data,function(obj,key){return obj}), 'text');
      callback(err,data);
    });
   },*/
   testParam : function(req,res){
    logger.info('testing a parameter',req.params.param);
    res.end('{"message":"Hello from the Server. param value :"'+req.params.param+'"}');
   },
  getNextSequence1dele: function (db ,name, callback){
    logger.info('inside getNextSequence. name',name);
       // = db.collection('counters');  

       db.collection('counters').findAndModify(
                 { _id: name },{},
                 { $inc: {seq: 1 }},
                 {new: true},
                 callback);
       
    },
   testDb : function(req,res)
   {
   logger.info('testdb call ');
   req.db.collection('counters').find().limit(10).toArray(function(err,data){
                      if (err) logger.info(err);
                      else {
                          logger.info('data ----+' ,data);
                          res.end(JSON.stringify(data));
                         }
       });
   },
   sadhanas : function(req,res)
    {
     logger.info("retrieveing all sadhanas..");
     getSadhanas(function (err, json) {
        if(err) { throw err; }
        //console.log(json);
        
         res.end(JSON.stringify(json.sadhanaslist.sadhanas));
                        
      });
    
   },

   values : function(req,res)
    {
     logger.info("retrieveing all values..");
     getSadhanas(function (err, json) {
        if(err) { throw err; }
        //console.log(json);
        
         res.end(JSON.stringify(json.valueslist.values));
                        
      });
    
   },
   sadhanasOriginal : function(req,res)
    {
    req.db.collection('sadhanas').find({id:1}).limit(10).toArray(function(err,data){

                      if (err) logger.info(err);
                      else {
                         // logger.info('data ----+' ,data[0].sadhanas);
                          res.end(JSON.stringify(data[0].sadhanas));
                         }
       });
   },
   log : function(req,res)
   {
    var logdata = req.body;
     logger.info('logdata from client', JSON.stringify(logdata));
     getNextSequence(req.db,'logid', function(err, obj) {
                      if (err) logger.info(err);
                      else
                         {
                       logger.info('logid',obj);
                       logger.info('logid',obj.seq);
                       logdata.id=obj.seq;
                       logdata.created_on = new Date();

                       
                       req.db.collection('loginfo').save(logdata,
                         function (err, data) {

                          res.end(JSON.stringify(data));
                        });
                     }
          });

   },

  getOne: function(req, res) {
    var id = req.params.id;
    var product = data[0]; // Spoof a DB call
    res.json(product);
  },

  create: function(req, res) {
    var newProduct = req.body;
    data.push(newProduct); // Spoof a DB call
    res.json(newProduct);
  },

  update: function(req, res) {
    var updateProduct = req.body;
    var id = req.params.id;
    data[id] = updateProduct // Spoof a DB call
    res.json(updateProduct);
  },

  delete: function(req, res) {
    var id = req.params.id;
    data.splice(id, 1) // Spoof a DB call
    res.json(true);
  }
};


var crypto = require('crypto'),
    algorithm = 'aes-256-ctr',
    password = 'd6F3Efeq';
 


module.exports = utils;
