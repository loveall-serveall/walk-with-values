//var config = require('./config');
var mongodb = require('mongodb');
var db;
//var url = 'mongodb://'+ config.database.username+ ':' + config.database.password + '@' + config.database.host + ':' + config.database.port + '/' + config.database.name;
var url = 'mongodb://localhost/PIF';

module.exports = function(){
    initDb :function(){
        mongodb.MongoClient.connect(url, function(err, result) {

            if(err || result === undefined || result === null) {

                  throw err;

            } else {

                  db = result;

            }

        });     

    },

    getDb : function () {

        if(db === null || db === undefined) {

            this.initDb();

        }

        return db;

    }

};