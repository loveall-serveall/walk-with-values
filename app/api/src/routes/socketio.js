var http = require('request');
var _ = require('underscore');
var t = require("exectimer");

 //<!-- "serviceversion" :"default" -->

var aribapopayload={"source": "Ariba",   
    "businessUnit": "30",

    "detail": {
      "actionType": "1",
      "businessUnit": "30",
      "originalOrderLineKey": {
        "documentCompany": "00001",
        "documentLineNumber": "1",
        "documentSuffix": "000"
      },
      "product": {
        "item": { "itemid": "60003" }
      },
      "quantityOrdered": "9"
    },
    "processing": {
      "actionType": "1",
      "processingVersion": "ZJDE0001"
    },
    "supplierAddress": {
      "supplier": { "entityId": "4343" }
    }
  };

var respObj ={
      title: 'Purchase Order Submission Portal',
      polist:  [],
      itemPriceDetails:[],
      payload :'',
      message:{
           type:'',
           value:''
         }
    };

var testcaseno=0;

var polist=[];
/*
 * GET users listing.
 */

 /*exports.clear = function(req, res, next){
  
    console.info('in clear po listings');
    polist=[];
    respObj ={
      title: 'Purchase Order Submission Portal',
      polist:  [],
      payload :'',
      message:{
           type:'',
           value:''
         }
    };
    aribapopayload.source="";
    testcaseno=0;
    res.redirect('/list');
    };

exports.list = function(req, res, next){
  
    console.info('in socketio listing ..polist is ..great so far',polist );
    respObj.title = 'Purchase Order Submission Portal';
    respObj.polist =polist;

    //console.log("list payload return "+JSON.stringify(respObj));
    res.render('socketio', respObj);

    
  
}; */


 

 module.exports = function(io){


  var socketsioroutes = {};
  socketsioroutes.list =  function(req, res, next){
  
    console.info('in socketio listing ..polist is ..great so far',polist );
    respObj.title = 'Socket IO System';
    respObj.polist =polist;
     io.emit('chat message', "Hello World route 1"); 
    //console.log("list payload return "+JSON.stringify(respObj));
    res.render('socketio', respObj);
  };

  socketsioroutes.list2 =  function(req, res, next){
  
    console.info('in socketio listing ..polist is ..great so far',polist );
    respObj.title = 'Socket IO System';
    respObj.polist =polist;
     io.emit('chat message', "Hello World route 2"); 
    //console.log("list payload return "+JSON.stringify(respObj));
    res.render('socketio', respObj);
  };
 
  return socketsioroutes;
} 